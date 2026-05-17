<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamagedItem;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $variants = ProductVariant::with(['product.category', 'product.supplier', 'unit'])
            ->when($search, fn ($query) => $this->applySearch($query, $search))
            ->when($request->input('stock_status'), fn ($query, $status) => $this->applyStockStatus($query, $status))
            ->when($request->input('expiry_status'), fn ($query, $status) => $this->applyExpiryStatus($query, $status))
            ->when($request->input('supplier_id'), function ($query, $supplierId) {
                $query->whereHas('product', fn ($q) => $q->where('supplier_id', $supplierId));
            })
            ->when($request->input('category_id'), function ($query, $categoryId) {
                $query->whereHas('product', fn ($q) => $q->where('category_id', $categoryId));
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.stocks.index', [
            'variants' => $variants,
            'totalStockQuantity' => ProductVariant::sum('current_stock'),
            'lowStockItems' => ProductVariant::where('current_stock', '>', 0)
                ->whereNotNull('reorder_level')
                ->whereColumn('current_stock', '<=', 'reorder_level')
                ->count(),
            'outOfStockItems' => ProductVariant::where('current_stock', '<=', 0)->count(),
            'expiredItems' => ProductVariant::whereNotNull('expiry_date')->whereDate('expiry_date', '<', today())->count(),
            'damagedItemsCount' => DamagedItem::sum('quantity'),
            'recentMovements' => StockMovement::with(['variant.product', 'supplier', 'createdBy'])
                ->latest()
                ->limit(6)
                ->get(),
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
            'categories' => ProductCategory::orderBy('category_name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.stocks.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'reason' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $variant = ProductVariant::whereKey($validated['product_variant_id'])->lockForUpdate()->firstOrFail();
            $previousStock = (int) $variant->current_stock;
            $newStock = $previousStock + (int) $validated['quantity'];

            $variant->update(['current_stock' => $newStock]);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'movement_type' => 'stock_in',
                'quantity' => $validated['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'unit_cost' => $validated['unit_cost'] ?? null,
                'reason' => $validated['reason'] ?? null,
                'movement_date' => today(),
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.stocks.index')->with('success', 'Stock added successfully.');
    }

    private function formData(): array
    {
        return [
            'variants' => ProductVariant::with(['product.supplier', 'unit'])
                ->orderBy('variant_name')
                ->get(),
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
        ];
    }

    private function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('variant_name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('size', 'like', "%{$search}%")
                ->orWhereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                });
        });
    }

    private function applyStockStatus($query, string $status): void
    {
        if ($status === 'in_stock') {
            $query->where('current_stock', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('reorder_level')->orWhereColumn('current_stock', '>', 'reorder_level');
                });
        }

        if ($status === 'low_stock') {
            $query->where('current_stock', '>', 0)
                ->whereNotNull('reorder_level')
                ->whereColumn('current_stock', '<=', 'reorder_level');
        }

        if ($status === 'out_of_stock') {
            $query->where('current_stock', '<=', 0);
        }
    }

    private function applyExpiryStatus($query, string $status): void
    {
        if ($status === 'expired') {
            $query->whereNotNull('expiry_date')->whereDate('expiry_date', '<', today());
        }

        if ($status === 'expiring_soon') {
            $query->whereNotNull('expiry_date')
                ->whereDate('expiry_date', '>=', today())
                ->whereDate('expiry_date', '<=', today()->addDays(30));
        }
    }
}
