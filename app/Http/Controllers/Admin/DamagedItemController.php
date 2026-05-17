<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DamagedItem;
use App\Models\DiscountedProduct;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DamagedItemController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $damagedItems = DamagedItem::with(['variant.product', 'supplier', 'createdBy', 'discountedProduct'])
            ->when($search, function ($query) use ($search) {
                $query->where('damage_reason', 'like', "%{$search}%")
                    ->orWhereHas('variant', function ($q) use ($search) {
                        $q->where('variant_name', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhereHas('product', fn ($product) => $product->where('product_name', 'like', "%{$search}%"));
                    });
            })
            ->when($request->input('action_type'), fn ($query, $action) => $query->where('action_type', $action))
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('admin.damaged-items.index', compact('damagedItems'));
    }

    public function create(): View
    {
        return view('admin.damaged-items.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        DB::transaction(function () use ($validated) {
            $variant = ProductVariant::with('product.supplier')
                ->whereKey($validated['product_variant_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $previousStock = (int) $variant->current_stock;
            $quantity = (int) $validated['quantity'];

            if ($quantity > $previousStock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity cannot be greater than current stock.',
                ]);
            }

            $movementType = $validated['action_type'] === 'return_to_supplier' ? 'purchase_return' : 'damaged';
            $discountedProduct = null;
            $newStock = $previousStock - $quantity;

            $variant->update(['current_stock' => $newStock]);

            if ($validated['action_type'] === 'move_to_discount') {
                $discountedProduct = DiscountedProduct::create($this->discountPayload($variant, $quantity, $validated));
                $movementType = 'discount_reserved';
            }

            $damagedItem = DamagedItem::create([
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'damage_reason' => $validated['damage_reason'],
                'action_type' => $validated['action_type'],
                'discounted_product_id' => $discountedProduct?->id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'movement_type' => $movementType,
                'quantity' => $quantity,
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reason' => $validated['damage_reason'],
                'reference_type' => DamagedItem::class,
                'reference_id' => $damagedItem->id,
                'movement_date' => today(),
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.damaged-items.index')->with('success', 'Damaged item recorded successfully.');
    }

    public function edit(DamagedItem $damagedItem): View
    {
        $damagedItem->load('variant.product');

        return view('admin.damaged-items.create', $this->formData() + compact('damagedItem'));
    }

    public function update(Request $request, DamagedItem $damagedItem): RedirectResponse
    {
        $validated = $request->validate([
            'damage_reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $damagedItem->update($validated);

        return redirect()->route('admin.damaged-items.index')->with('success', 'Damaged item updated successfully.');
    }

    public function destroy(DamagedItem $damagedItem): RedirectResponse
    {
        $damagedItem->delete();

        return redirect()->route('admin.damaged-items.index')->with('success', 'Damaged item deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'damage_reason' => ['required', 'string'],
            'action_type' => ['required', Rule::in(DamagedItem::ACTIONS)],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function formData(): array
    {
        return [
            'damagedItem' => null,
            'variants' => ProductVariant::with(['product.supplier', 'unit'])->orderBy('variant_name')->get(),
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
        ];
    }

    private function discountPayload(ProductVariant $variant, int $quantity, array $validated): array
    {
        $ourPrice = (float) $variant->our_price;
        $discountValue = 10;
        $discountPrice = round(max($ourPrice - ($ourPrice * $discountValue / 100), 0), 2);

        return [
            'product_variant_id' => $variant->id,
            'current_quantity' => (int) $variant->current_stock,
            'discount_quantity' => $quantity,
            'price_received' => (float) $variant->purchase_price,
            'our_price' => $ourPrice,
            'supplier_name' => $variant->product->supplier->supplier_name ?? null,
            'discount_percentage' => $discountValue,
            'maximum_allowed_discount_percentage' => 100,
            'discount_price' => $discountPrice,
            'discount_type' => 'percentage',
            'discount_value' => $discountValue,
            'start_date' => today(),
            'end_date' => today()->addDays(30),
            'reason' => $validated['damage_reason'],
            'status' => 'active',
        ];
    }
}
