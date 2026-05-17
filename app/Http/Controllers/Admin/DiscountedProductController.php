<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountedProduct;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(function () use ($variant, $validated) {
            $calculated = $this->calculateDiscountValues($variant, $validated);
            $discountedProduct = DiscountedProduct::create($calculated);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'supplier_id' => $variant->product?->supplier_id,
                'movement_type' => 'discount_reserved',
                'quantity' => $discountedProduct->discount_quantity,
                'previous_stock' => $variant->current_stock,
                'new_stock' => $variant->current_stock,
                'reason' => $discountedProduct->reason ?? 'Discount stock reserved for discounted product.',
                'reference_type' => DiscountedProduct::class,
                'reference_id' => $discountedProduct->id,
                'movement_date' => today(),
                'created_by' => auth()->id(),
            ]);
        });

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

        return response()->json([
            'current_quantity' => $variant->current_stock,
            'price_received' => number_format($priceReceived, 2, '.', ''),
            'our_price' => number_format($ourPrice, 2, '.', ''),
            'supplier_name' => $variant->product->supplier->supplier_name ?? 'N/A',
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
            'discount_type' => ['required', Rule::in(['percentage', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
    }

    private function calculateDiscountValues(ProductVariant $variant, array $validated): array
    {
        $currentQuantity = (int) $variant->current_stock;
        $discountQuantity = (int) $validated['discount_quantity'];
        $discountType = $validated['discount_type'];
        $discountValue = (float) $validated['discount_value'];

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
                    'discount_value' => 'Discount cannot be created because price received is greater than or equal to our price.',
                ]));
        }

        $maximumAllowedDiscountPercentage = $this->getMaximumAllowedDiscountPercentage($ourPrice, $priceReceived);
        $discountPercentage = $discountType === 'percentage'
            ? $discountValue
            : round(($discountValue / $ourPrice) * 100, 2);

        if ($discountType === 'percentage' && $discountValue > 100) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'discount_value' => 'Percentage discount cannot be greater than 100.',
                ]));
        }

        $discountAmountPerItem = $discountType === 'percentage'
            ? ($ourPrice * $discountValue / 100)
            : $discountValue;

        $discountPrice = round($ourPrice - $discountAmountPerItem, 2);

        if ($discountPrice <= $priceReceived) {
            abort(back()
                ->withInput()
                ->withErrors([
                    'discount_value' => 'Discount price must be greater than price received.',
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
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
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
