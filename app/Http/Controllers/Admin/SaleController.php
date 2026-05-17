<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\DiscountedProduct;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Tax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        return view('admin.sales.index', [
            'tax' => $this->activeTax(),
            'customers' => $this->customers(),
            'sale' => null,
            'cartItems' => [],
        ]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search'));

        if ($search === '') {
            return response()->json([]);
        }

        $variants = ProductVariant::query()
            ->with(['product.supplier', 'unit'])
            ->where('status', 'active')
            ->where('current_stock', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('variant_name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('size', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('product_name', 'like', "%{$search}%")
                            ->orWhere('product_code', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('product.supplier', function ($q) use ($search) {
                        $q->where('supplier_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('discountedProducts', function ($q) use ($search) {
                        $q->where('supplier_name', 'like', "%{$search}%")
                            ->orWhere('reason', 'like', "%{$search}%");
                    });
            })
            ->limit(12)
            ->get();

        return response()->json($variants->map(function (ProductVariant $variant) {
            $discount = $this->activeDiscountForVariant($variant);

            return [
                'id' => $variant->id,
                'product_name' => $variant->product->product_name ?? 'N/A',
                'variant_name' => $variant->variant_name,
                'size' => $variant->size,
                'description' => $variant->description,
                'sku' => $variant->sku,
                'barcode' => $variant->barcode,
                'unit' => $variant->unit->short_code ?? $variant->unit->unit_name ?? 'N/A',
                'unit_price' => (float) $variant->selling_price,
                'our_price' => (float) $variant->our_price,
                'purchase_price' => (float) $variant->purchase_price,
                'cost_price' => (float) $variant->cost_price,
                'wholesale_price' => (float) $variant->wholesale_price,
                'available_stock' => (int) $variant->current_stock,
                'reorder_level' => $variant->reorder_level,
                'expiry_date' => $variant->expiry_date?->format('Y-m-d'),
                'status' => $variant->status,
                'has_discount' => $discount['has_discount'],
                'discount_type' => $discount['discount_type'],
                'discount_value' => $discount['discount_value'],
                'discount_per_item' => $discount['discount_per_item'],
                'discount_price' => $discount['discount_price'],
            ];
        }));
    }

    public function saveDraft(Request $request): RedirectResponse
    {
        $sale = $this->storeBill($request, 'draft');

        return redirect()
            ->route('admin.sales.edit', $sale)
            ->with('success', 'Draft bill saved successfully.');
    }

    public function hold(Request $request): RedirectResponse
    {
        $sale = $this->storeBill($request, 'hold');

        return redirect()
            ->route('admin.sales.held')
            ->with('success', 'Bill placed on hold successfully.');
    }

    public function confirm(Request $request): RedirectResponse
    {
        $sale = $this->storeBill($request, 'completed');

        return redirect()
            ->route('admin.sales.print', $sale)
            ->with('success', 'Bill confirmed successfully.');
    }

    public function held(): View
    {
        $sales = Sale::with(['cashier', 'items'])
            ->where('status', 'hold')
            ->latest()
            ->paginate(10);

        return view('admin.sales.held', compact('sales'));
    }

    public function confirmed(): View
    {
        $sales = Sale::with(['cashier', 'items'])
            ->where('status', 'completed')
            ->latest()
            ->paginate(10);

        return view('admin.sales.confirmed', compact('sales'));
    }

    public function drafts(): View
    {
        $sales = Sale::with(['cashier', 'items'])
            ->where('status', 'draft')
            ->latest()
            ->paginate(10);

        return view('admin.sales.drafts', compact('sales'));
    }

    public function edit(Sale $sale): View
    {
        abort_unless(in_array($sale->status, ['draft', 'hold'], true), 403);

        $sale->load('items');

        return view('admin.sales.index', [
            'tax' => $this->activeTax(),
            'customers' => $this->customers(),
            'sale' => $sale,
            'cartItems' => $sale->items->map(fn ($item) => [
                'id' => $item->product_variant_id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'sku' => $item->sku,
                'barcode' => $item->barcode,
                'unit_price' => (float) ProductVariant::whereKey($item->product_variant_id)->value('selling_price'),
                'our_price' => (float) ProductVariant::whereKey($item->product_variant_id)->value('our_price'),
                'discount_price' => (float) $item->unit_price,
                'available_stock' => (int) $item->available_stock_before_sale,
                'quantity' => (int) $item->quantity,
                'discount_type' => $item->discount_type,
                'discount_value' => (float) $item->discount_value,
                'discount_per_item' => (float) $item->discount_amount / max((int) $item->quantity, 1),
            ])->values()->toArray(),
        ]);
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        abort_unless(in_array($sale->status, ['draft', 'hold'], true), 403);

        $sale->update(['status' => 'cancelled']);

        return redirect()
            ->route('admin.sales.index')
            ->with('success', 'Bill cancelled successfully.');
    }

    public function print(Sale $sale): View
    {
        $sale->load(['items.variant', 'payments', 'cashier']);

        return view('admin.sales.print', compact('sale'));
    }

    private function storeBill(Request $request, string $status): Sale
    {
        $validated = $request->validate([
            'sale_id' => ['nullable', 'integer', 'exists:sales,id'],
            'items' => ['required', 'json'],
            'customer_id' => ['nullable', 'integer'],
            'payment_method' => ['nullable', Rule::in(['cash', 'card', 'bank_transfer', 'credit'])],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $items = json_decode($validated['items'], true);

        if (! is_array($items) || count($items) === 0) {
            throw ValidationException::withMessages([
                'items' => 'Please add at least one product to the bill.',
            ]);
        }

        if ($status === 'completed') {
            $this->validatePayment($validated);
        }

        return DB::transaction(function () use ($validated, $items, $status) {
            $calculated = $this->calculateBill($items);
            $payment = $this->paymentAmounts($validated, $calculated['grand_total'], $status);

            $sale = $this->resolveSale($validated['sale_id'] ?? null);

            $sale->fill([
                'customer_id' => $validated['customer_id'] ?? null,
                'cashier_id' => auth()->id(),
                'subtotal' => $calculated['subtotal'],
                'discount_amount' => $calculated['discount_amount'],
                'taxable_amount' => $calculated['taxable_amount'],
                'tax_name' => $calculated['tax_name'],
                'tax_rate' => $calculated['tax_rate'],
                'tax_amount' => $calculated['tax_amount'],
                'grand_total' => $calculated['grand_total'],
                'paid_amount' => $payment['paid_amount'],
                'balance_amount' => $payment['balance_amount'],
                'due_amount' => $payment['due_amount'],
                'status' => $status,
                'notes' => $validated['notes'] ?? null,
            ]);

            $sale->save();

            if ($status === 'completed' && ! $sale->invoice_no) {
                $prefix = AppSetting::getValue('invoice_prefix', 'INV') ?: 'INV';

                $sale->update([
                    'invoice_no' => $prefix.'-'.now()->format('Ymd').'-'.str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT),
                ]);
            }

            $sale->items()->delete();
            $sale->payments()->delete();

            foreach ($calculated['items'] as $item) {
                $sale->items()->create($item);

                if ($status === 'completed') {
                    $variant = ProductVariant::whereKey($item['product_variant_id'])->lockForUpdate()->firstOrFail();
                    $previousStock = (int) $variant->current_stock;
                    $newStock = $previousStock - (int) $item['quantity'];

                    if ($newStock < 0) {
                        throw ValidationException::withMessages([
                            'items' => "{$variant->variant_name} has only {$previousStock} available.",
                        ]);
                    }

                    $variant->update(['current_stock' => $newStock]);

                    StockMovement::create([
                        'product_variant_id' => $variant->id,
                        'product_id' => $variant->product_id,
                        'supplier_id' => $variant->product?->supplier_id,
                        'movement_type' => (float) ($item['discount_amount'] ?? 0) > 0 ? 'discount_sold' : 'sale',
                        'quantity' => $item['quantity'],
                        'previous_stock' => $previousStock,
                        'new_stock' => $newStock,
                        'reason' => 'Stock reduced from completed sale.',
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'movement_date' => today(),
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            if ($status === 'completed') {
                $sale->payments()->create([
                    'payment_method' => $validated['payment_method'],
                    'paid_amount' => $payment['paid_amount'],
                    'reference_no' => $validated['reference_no'] ?? null,
                ]);
            }

            return $sale->fresh();
        });
    }

    private function resolveSale(?int $saleId): Sale
    {
        if (! $saleId) {
            return new Sale();
        }

        $sale = Sale::whereKey($saleId)->lockForUpdate()->firstOrFail();

        if (! in_array($sale->status, ['draft', 'hold'], true)) {
            throw ValidationException::withMessages([
                'sale_id' => 'Only draft or held bills can be edited.',
            ]);
        }

        return $sale;
    }

    private function calculateBill(array $items): array
    {
        $rows = [];
        $subtotal = 0;
        $discountAmount = 0;

        foreach ($items as $item) {
            $variantId = (int) ($item['id'] ?? $item['product_variant_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'items' => 'Quantity must be at least 1.',
                ]);
            }

            $variant = ProductVariant::with(['product', 'unit'])
                ->whereKey($variantId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($variant->status !== 'active' || $variant->current_stock < 1) {
                throw ValidationException::withMessages([
                    'items' => "{$variant->variant_name} is not available for billing.",
                ]);
            }

            if ($quantity > $variant->current_stock) {
                throw ValidationException::withMessages([
                    'items' => "{$variant->variant_name} has only {$variant->current_stock} available.",
                ]);
            }

            $discount = $this->activeDiscountForVariant($variant);
            $ourPrice = (float) $variant->our_price;
            $hasDiscount = (float) $discount['discount_per_item'] > 0;
            $discountPrice = $hasDiscount ? (float) $discount['discount_price'] : $ourPrice;
            $lineSubtotal = $ourPrice * $quantity;
            $lineDiscount = $hasDiscount
                ? min(($ourPrice - $discountPrice) * $quantity, $lineSubtotal)
                : 0;
            $lineTotal = $hasDiscount ? ($discountPrice * $quantity) : $lineSubtotal;

            $subtotal += $lineSubtotal;
            $discountAmount += $lineDiscount;

            $rows[] = [
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->product_name ?? 'N/A',
                'variant_name' => $variant->variant_name,
                'sku' => $variant->sku,
                'barcode' => $variant->barcode,
                'unit_price' => $hasDiscount ? $discountPrice : $ourPrice,
                'quantity' => $quantity,
                'available_stock_before_sale' => $variant->current_stock,
                'discount_type' => $discount['discount_type'],
                'discount_value' => $discount['discount_value'],
                'discount_amount' => $lineDiscount,
                'line_total' => $lineTotal,
            ];
        }

        $taxableAmount = max($subtotal - $discountAmount, 0);
        $tax = $this->activeTax();
        $taxName = $tax?->tax_name;
        $taxRate = (float) ($tax?->tax_rate ?? 0);
        $taxAmount = 0;
        $grandTotal = $taxableAmount;

        if ($tax && $taxRate > 0) {
            if ($tax->tax_type === 'inclusive') {
                $taxAmount = $taxableAmount - ($taxableAmount / (1 + ($taxRate / 100)));
            } else {
                $taxAmount = $taxableAmount * $taxRate / 100;
                $grandTotal = $taxableAmount + $taxAmount;
            }
        }

        return [
            'items' => $rows,
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'taxable_amount' => round($taxableAmount, 2),
            'tax_name' => $taxName,
            'tax_rate' => round($taxRate, 2),
            'tax_amount' => round($taxAmount, 2),
            'grand_total' => round($grandTotal, 2),
        ];
    }

    private function activeDiscountForVariant(ProductVariant $variant): array
    {
        $discount = DiscountedProduct::query()
            ->where('product_variant_id', $variant->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $discount) {
            return [
                'has_discount' => false,
                'discount_type' => null,
                'discount_value' => null,
                'discount_per_item' => 0,
                'discount_price' => null,
            ];
        }

        $type = $discount->discount_type ?: 'percentage';
        $value = (float) ($discount->discount_value > 0 ? $discount->discount_value : $discount->discount_percentage);
        $unitPrice = (float) $variant->our_price;

        $perItem = $type === 'fixed'
            ? $value
            : $unitPrice * $value / 100;

        $perItem = min($perItem, $unitPrice);
        $discountPrice = (float) $discount->discount_price;

        if ($discountPrice <= 0) {
            $discountPrice = max($unitPrice - $perItem, 0);
        }

        if ($perItem <= 0 && $discountPrice > 0 && $discountPrice < $unitPrice) {
            $perItem = $unitPrice - $discountPrice;
        }

        $hasDiscount = $discountPrice > 0 && $discountPrice < $unitPrice;

        return [
            'has_discount' => $hasDiscount,
            'discount_type' => $type,
            'discount_value' => round($value, 2),
            'discount_per_item' => round($perItem, 2),
            'discount_price' => round($discountPrice, 2),
        ];
    }

    private function validatePayment(array $validated): void
    {
        $method = $validated['payment_method'] ?? null;

        if (! $method) {
            throw ValidationException::withMessages([
                'payment_method' => 'Please select a payment method.',
            ]);
        }

        if (in_array($method, ['card', 'bank_transfer'], true) && empty($validated['reference_no'])) {
            throw ValidationException::withMessages([
                'reference_no' => 'Reference number is required for this payment method.',
            ]);
        }

        if ($method === 'credit' && empty($validated['customer_id'])) {
            throw ValidationException::withMessages([
                'customer_id' => 'Credit sale requires a registered customer.',
            ]);
        }

        if ($method === 'credit' && ! $this->customerExists((int) $validated['customer_id'])) {
            throw ValidationException::withMessages([
                'customer_id' => 'Selected customer was not found.',
            ]);
        }
    }

    private function paymentAmounts(array $validated, float $grandTotal, string $status): array
    {
        if ($status !== 'completed') {
            return [
                'paid_amount' => 0,
                'balance_amount' => 0,
                'due_amount' => 0,
            ];
        }

        $paidAmount = round((float) ($validated['paid_amount'] ?? 0), 2);
        $method = $validated['payment_method'];

        if ($method === 'credit') {
            return [
                'paid_amount' => $paidAmount,
                'balance_amount' => 0,
                'due_amount' => max(round($grandTotal - $paidAmount, 2), 0),
            ];
        }

        if ($paidAmount < $grandTotal) {
            throw ValidationException::withMessages([
                'paid_amount' => 'Paid amount must be at least the grand total.',
            ]);
        }

        return [
            'paid_amount' => $paidAmount,
            'balance_amount' => round($paidAmount - $grandTotal, 2),
            'due_amount' => 0,
        ];
    }

    private function activeTax(): ?Tax
    {
        return Tax::where('status', 'active')->latest()->first();
    }

    private function customers()
    {
        if (! Schema::hasTable('customers')) {
            return collect();
        }

        return DB::table('customers')
            ->when(Schema::hasColumn('customers', 'status'), fn ($query) => $query->where('status', 'active'))
            ->orderBy(Schema::hasColumn('customers', 'customer_name') ? 'customer_name' : 'id')
            ->get();
    }

    private function customerExists(int $customerId): bool
    {
        return Schema::hasTable('customers')
            && DB::table('customers')->where('id', $customerId)->exists();
    }
}
