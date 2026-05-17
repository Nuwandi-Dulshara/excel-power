<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockAdjustmentController extends Controller
{
    public function create(): View
    {
        return view('admin.stocks.reduce', [
            'variants' => ProductVariant::with(['product.supplier', 'unit'])->orderBy('variant_name')->get(),
            'suppliers' => Supplier::orderBy('supplier_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'movement_type' => ['required', Rule::in(['damaged', 'expired', 'adjustment', 'purchase_return'])],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'reason' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $variant = ProductVariant::whereKey($validated['product_variant_id'])->lockForUpdate()->firstOrFail();
            $previousStock = (int) $variant->current_stock;

            if ((int) $validated['quantity'] > $previousStock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity cannot be greater than current stock.',
                ]);
            }

            $newStock = $previousStock - (int) $validated['quantity'];
            $variant->update(['current_stock' => $newStock]);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'product_id' => $variant->product_id,
                'supplier_id' => $validated['supplier_id'] ?? null,
                'movement_type' => $validated['movement_type'],
                'quantity' => $validated['quantity'],
                'previous_stock' => $previousStock,
                'new_stock' => $newStock,
                'reason' => $validated['reason'] ?? null,
                'movement_date' => today(),
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.stocks.index')->with('success', 'Stock reduced successfully.');
    }
}
