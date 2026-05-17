<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function index(Request $request, Product $product)
    {
        $product->load(['category', 'unit', 'supplier']);

        $search = $request->input('search');
        $stockStatus = $request->input('stock_status');
        $status = $request->input('status');

        $variants = $product->variants()
            ->with(['product', 'unit'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('variant_name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('size', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($stockStatus, function ($query) use ($stockStatus) {
                if ($stockStatus === 'in_stock') {
                    $query->where('current_stock', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('reorder_level')
                                ->orWhereColumn('current_stock', '>', 'reorder_level');
                        });
                }

                if ($stockStatus === 'low_stock') {
                    $query->where('current_stock', '>', 0)
                        ->whereNotNull('reorder_level')
                        ->whereColumn('current_stock', '<=', 'reorder_level');
                }

                if ($stockStatus === 'out_of_stock') {
                    $query->where('current_stock', '<=', 0);
                }
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $totalVariants = $product->variants()->count();
        $activeVariants = $product->variants()->where('status', 'active')->count();
        $inactiveVariants = $product->variants()->where('status', 'inactive')->count();
        $lowStockItems = $product->variants()
            ->whereNotNull('reorder_level')
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->count();

        return view('admin.product-variants.index', compact(
            'product',
            'variants',
            'totalVariants',
            'activeVariants',
            'inactiveVariants',
            'lowStockItems',
            'search',
            'stockStatus',
            'status'
        ));
    }

    public function create(Product $product)
    {
        $product->load(['category', 'unit', 'supplier']);

        return view('admin.product-variants.create', compact('product'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'variant_name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:150', 'unique:product_variants,barcode'],
            'sku' => ['nullable', 'string', 'max:150', 'unique:product_variants,sku'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'purchase_quantity' => ['required', 'integer', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'our_price' => ['required', 'numeric', 'min:0'],
            'minimum_wholesale_quantity' => ['nullable', 'integer', 'min:1'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'opening_stock' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['product_id'] = $product->id;
        $validated['product_unit_id'] = $product->product_unit_id;
        $validated['current_stock'] = $validated['opening_stock'];

        DB::transaction(function () use ($validated) {
            $variant = ProductVariant::create($validated);

            if ((int) $variant->current_stock > 0) {
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'supplier_id' => $variant->product?->supplier_id,
                    'movement_type' => 'initial_stock',
                    'quantity' => $variant->current_stock,
                    'previous_stock' => 0,
                    'new_stock' => $variant->current_stock,
                    'unit_cost' => $variant->purchase_price,
                    'reason' => 'Opening stock entered when sub product was created.',
                    'reference_type' => ProductVariant::class,
                    'reference_id' => $variant->id,
                    'movement_date' => today(),
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()
            ->route('admin.products.variants.index', $product->id)
            ->with('success', 'Sub product created successfully.');
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        $product->load(['category', 'unit', 'supplier']);

        return view('admin.product-variants.edit', compact('product', 'variant'));
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        $validated = $request->validate([
            'variant_name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'string', 'max:100'],
            'barcode' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('product_variants', 'barcode')->ignore($variant->id),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('product_variants', 'sku')->ignore($variant->id),
            ],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'purchase_quantity' => ['required', 'integer', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'our_price' => ['required', 'numeric', 'min:0'],
            'minimum_wholesale_quantity' => ['nullable', 'integer', 'min:1'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'opening_stock' => ['required', 'integer', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'expiry_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $validated['product_unit_id'] = $product->product_unit_id;

        DB::transaction(function () use ($validated, $variant) {
            $previousStock = (int) $variant->current_stock;
            $requestedStock = (int) $validated['current_stock'];

            $variant->update($validated);

            if ($requestedStock !== $previousStock) {
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'supplier_id' => $variant->product?->supplier_id,
                    'movement_type' => 'adjustment',
                    'quantity' => abs($requestedStock - $previousStock),
                    'previous_stock' => $previousStock,
                    'new_stock' => $requestedStock,
                    'reason' => 'Current stock changed from sub product edit screen.',
                    'reference_type' => ProductVariant::class,
                    'reference_id' => $variant->id,
                    'movement_date' => today(),
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()
            ->route('admin.products.variants.index', $product->id)
            ->with('success', 'Sub product updated successfully.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        abort_if($variant->product_id !== $product->id, 404);

        $variant->delete();

        return redirect()
            ->route('admin.products.variants.index', $product->id)
            ->with('success', 'Sub product deleted successfully.');
    }
}
