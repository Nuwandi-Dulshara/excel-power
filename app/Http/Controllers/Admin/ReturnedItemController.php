<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamagedItem;
use App\Models\ProductVariant;
use App\Models\ReturnedItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReturnedItemController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $returnedItems = ReturnedItem::with(['variant.product', 'sale', 'saleItem', 'createdBy'])
            ->when($search, function ($query) use ($search) {
                $query->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('variant', function ($q) use ($search) {
                        $q->where('variant_name', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhereHas('product', fn ($product) => $product->where('product_name', 'like', "%{$search}%"));
                    });
            })
            ->when($request->input('return_type'), fn ($query, $type) => $query->where('return_type', $type))
            ->when($request->input('condition'), fn ($query, $condition) => $query->where('condition', $condition))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.returned-items.index', compact('returnedItems'));
    }

    public function create(): View
    {
        return view('admin.returned-items.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'sale_id' => ['nullable', 'exists:sales,id'],
            'sale_item_id' => ['nullable', 'exists:sale_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'return_type' => ['required', Rule::in(ReturnedItem::RETURN_TYPES)],
            'condition' => ['required', Rule::in(ReturnedItem::CONDITIONS)],
            'action_type' => ['required', Rule::in(ReturnedItem::ACTIONS)],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $variant = ProductVariant::whereKey($validated['product_variant_id'])->lockForUpdate()->firstOrFail();
            $previousStock = (int) $variant->current_stock;
            $quantity = (int) $validated['quantity'];
            $newStock = $previousStock;
            $movementType = null;

            if ($validated['return_type'] === 'customer_return' && $validated['condition'] === 'good' && $validated['action_type'] === 'add_back_to_stock') {
                $newStock = $previousStock + $quantity;
                $movementType = 'sale_return';
                $variant->update(['current_stock' => $newStock]);
            }

            if ($validated['return_type'] === 'supplier_return' || $validated['action_type'] === 'return_to_supplier') {
                if ($quantity > $previousStock) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Quantity cannot be greater than current stock.',
                    ]);
                }

                $newStock = $previousStock - $quantity;
                $movementType = 'purchase_return';
                $variant->update(['current_stock' => $newStock]);
            }

            $returnedItem = ReturnedItem::create($validated + ['created_by' => auth()->id()]);

            if ($validated['condition'] === 'damaged' || $validated['action_type'] === 'move_to_damage') {
                DamagedItem::create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'damage_reason' => $validated['reason'] ?? 'Customer returned damaged item',
                    'action_type' => 'reduce_from_stock',
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => auth()->id(),
                ]);
                $movementType = $movementType ?: 'damaged';
            }

            if ($movementType) {
                StockMovement::create([
                    'product_variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'movement_type' => $movementType,
                    'quantity' => $quantity,
                    'previous_stock' => $previousStock,
                    'new_stock' => $newStock,
                    'reason' => $validated['reason'] ?? null,
                    'reference_type' => ReturnedItem::class,
                    'reference_id' => $returnedItem->id,
                    'movement_date' => today(),
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('admin.returned-items.index')->with('success', 'Returned item recorded successfully.');
    }

    public function edit(ReturnedItem $returnedItem): View
    {
        $returnedItem->load('variant.product');

        return view('admin.returned-items.create', $this->formData() + compact('returnedItem'));
    }

    public function update(Request $request, ReturnedItem $returnedItem): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $returnedItem->update($validated);

        return redirect()->route('admin.returned-items.index')->with('success', 'Returned item updated successfully.');
    }

    public function destroy(ReturnedItem $returnedItem): RedirectResponse
    {
        $returnedItem->delete();

        return redirect()->route('admin.returned-items.index')->with('success', 'Returned item deleted successfully.');
    }

    private function formData(): array
    {
        return [
            'returnedItem' => null,
            'variants' => ProductVariant::with(['product.supplier', 'unit'])->orderBy('variant_name')->get(),
            'sales' => Sale::where('status', 'completed')->latest()->limit(100)->get(),
            'saleItems' => SaleItem::with('sale')->latest()->limit(200)->get(),
        ];
    }
}
