<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountedProduct;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscountedProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $discountedProducts = DiscountedProduct::with(['variant.product.supplier'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('variant', function ($q) use ($search) {
                    $q->where('variant_name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })
                ->orWhereHas('variant.product', function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                })
                ->orWhere('supplier_name', 'like', "%{$search}%")
                ->orWhere('reason', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalDiscountedProducts = DiscountedProduct::count();
        $activeDiscountedProducts = DiscountedProduct::where('status', 'active')->count();
        $inactiveDiscountedProducts = DiscountedProduct::where('status', 'inactive')->count();

        return view('admin.discounted-products.index', compact(
            'discountedProducts',
            'totalDiscountedProducts',
            'activeDiscountedProducts',
            'inactiveDiscountedProducts',
            'search',
            'status'
        ));
    }

    public function create()
    {
        $variants = ProductVariant::with(['product.supplier'])
            ->where('status', 'active')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('variant_name')
            ->get();

        return view('admin.discounted-products.create', compact('variants'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $variant = ProductVariant::with(['product.supplier'])
            ->where('status', 'active')
            ->findOrFail($validated['product_variant_id']);

        $calculated = $this->calculateDiscountValues($variant, $validated);

        DiscountedProduct::create($calculated);

        return redirect()
            ->route('admin.discounted-products.index')
            ->with('success', 'Discounted product created successfully.');
    }

    public function edit(DiscountedProduct $discountedProduct)
    {
        $discountedProduct->load(['variant.product.supplier']);

        $variants = ProductVariant::with(['product.supplier'])
            ->where('status', 'active')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('variant_name')
            ->get();

        return view('admin.discounted-products.edit', compact('discountedProduct', 'variants'));
    }

    public function update(Request $request, DiscountedProduct $discountedProduct)
    {
        $validated = $this->validateRequest($request);

        $variant = ProductVariant::with(['product.supplier'])
            ->where('status', 'active')
            ->findOrFail($validated['product_variant_id']);

        $calculated = $this->calculateDiscountValues($variant, $validated);

        $discountedProduct->update($calculated);

        return redirect()
            ->route('admin.discounted-products.index')
            ->with('success', 'Discounted product updated successfully.');
    }

    public function destroy(DiscountedProduct $discountedProduct)
    {
        $discountedProduct->delete();

        return redirect()
            ->route('admin.discounted-products.index')
            ->with('success', 'Discounted product deleted successfully.');
    }

    public function variantDetails(ProductVariant $variant)
    {
        $variant->load(['product.supplier']);

        abort_if($variant->status !== 'active', 404);

        $priceReceived = $this->getPriceReceived($variant);
        $ourPrice = (float) $variant->our_price;
        $maximumAllowedDiscountPercentage = $this->getMaximumAllowedDiscountPercentage($ourPrice, $priceReceived);

        return response()->json([
            'current_quantity' => $variant->current_stock,
            'price_received' => number_format($priceReceived, 2, '.', ''),
            'our_price' => number_format($ourPrice, 2, '.', ''),
            'supplier_name' => $variant->product->supplier->supplier_name ?? 'N/A',
            'maximum_allowed_discount_percentage' => number_format($maximumAllowedDiscountPercentage, 2, '.', ''),
        ]);
    }

    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'product_variant_id' => [
                'required',
                Rule::exists('product_variants', 'id')->where('status', 'active'),
            ],
            'discount_quantity' => ['required', 'integer', 'min:1'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'reason' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }

    private function calculateDiscountValues(ProductVariant $variant, array $validated): array
    {
        $currentQuantity = (int) $variant->current_stock;
        $discountQuantity = (int) $validated['discount_quantity'];
        $discountPercentage = (float) $validated['discount_percentage'];

        if ($discountQuantity > $currentQuantity) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'discount_quantity' => 'Discount quantity cannot be greater than current quantity.',
                ]));
        }

        if ((float) $variant->cost_price <= 0 || (int) $variant->purchase_quantity <= 0) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'product_variant_id' => 'This sub product does not have valid cost price or purchase quantity.',
                ]));
        }

        $priceReceived = $this->getPriceReceived($variant);
        $ourPrice = (float) $variant->our_price;

        if ($ourPrice <= 0) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'product_variant_id' => 'This sub product does not have a valid our price.',
                ]));
        }

        if ($priceReceived >= $ourPrice) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'discount_percentage' => 'Discount cannot be created because price received is greater than or equal to our price.',
                ]));
        }

        $maximumAllowedDiscountPercentage = $this->getMaximumAllowedDiscountPercentage($ourPrice, $priceReceived);
        $discountPrice = round($ourPrice - ($ourPrice * $discountPercentage / 100), 2);

        if ($discountPercentage > $maximumAllowedDiscountPercentage) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'discount_percentage' => 'Discount percentage cannot be greater than the maximum allowed discount percentage.',
                ]));
        }

        if ($discountPrice <= $priceReceived) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'discount_percentage' => 'Discount price must be greater than price received.',
                ]));
        }

        return [
            'product_variant_id' => $variant->id,
            'current_quantity' => $currentQuantity,
            'discount_quantity' => $discountQuantity,
            'price_received' => $priceReceived,
            'our_price' => $ourPrice,
            'supplier_name' => $variant->product->supplier->supplier_name ?? null,
            'discount_percentage' => $discountPercentage,
            'maximum_allowed_discount_percentage' => $maximumAllowedDiscountPercentage,
            'discount_price' => $discountPrice,
            'reason' => $validated['reason'] ?? null,
            'status' => $validated['status'],
        ];
    }

    private function getPriceReceived(ProductVariant $variant): float
    {
        return round(((float) $variant->cost_price / max((int) $variant->purchase_quantity, 1)), 2);
    }

    private function getMaximumAllowedDiscountPercentage(float $ourPrice, float $priceReceived): float
    {
        if ($ourPrice <= 0 || $priceReceived >= $ourPrice) {
            return 0;
        }

        return round((($ourPrice - $priceReceived) / $ourPrice) * 100, 2);
    }
}