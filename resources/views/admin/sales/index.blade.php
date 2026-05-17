@extends('admin.layouts.app')

@section('page-title', $sale ? 'Edit Bill' : 'Sales / Billing')
@section('page-subtitle', 'Search sub products, prepare bills, and confirm printable sales')

@section('content')

<style>
.billing-card {
    background: white;
    border: 1px solid #fde68a;
    border-radius: 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
    overflow: hidden;
}

.billing-card-header {
    background: radial-gradient(circle at top right, rgba(212, 175, 55, .25), transparent 30%), linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    padding: 18px 20px;
}

.billing-card-header h5 {
    font-weight: 900;
    margin: 0;
}

.billing-card-body {
    padding: 20px;
}

.search-panel {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.theme-control {
    border-radius: 14px;
    border: 1px solid #fde68a;
    padding: 12px 14px;
    font-weight: 600;
    color: #7f1d1d;
    background: #fffbeb;
}

.theme-control:focus {
    background: white;
    border-color: #b91c1c;
    box-shadow: 0 0 0 4px rgba(185, 28, 28, .1);
}

.search-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 12px;
}

.product-result {
    border: 1px solid #fde68a;
    background: #fff7ed;
    border-radius: 16px;
    padding: 14px;
}

.product-result-title {
    color: #7f1d1d;
    font-weight: 900;
}

.discount-chip {
    background: #dcfce7;
    color: #166534;
    border-radius: 999px;
    display: inline-flex;
    font-size: .76rem;
    font-weight: 900;
    padding: 5px 10px;
}

.no-discount-chip {
    background: #fee2e2;
    color: #991b1b;
}

.btn-add,
.btn-action-main {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 10px 14px;
    font-weight: 850;
}

.btn-add:hover,
.btn-action-main:hover {
    color: white;
}

.btn-soft {
    background: #fff7ed;
    color: #7f1d1d;
    border: none;
    border-radius: 14px;
    padding: 10px 14px;
    font-weight: 850;
    text-decoration: none;
}

.btn-danger-soft {
    background: #fee2e2;
    color: #991b1b;
    border: none;
    border-radius: 12px;
    padding: 8px 10px;
    font-weight: 850;
}

.qty-btn {
    width: 34px;
    height: 34px;
    border: none;
    border-radius: 10px;
    background: #fef3c7;
    color: #7f1d1d;
    font-weight: 900;
}

.qty-input {
    width: 76px;
    text-align: center;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 9px 0;
    border-bottom: 1px solid #fef3c7;
    font-weight: 750;
    color: #7c2d12;
}

.summary-total {
    color: #7f1d1d;
    font-size: 1.08rem;
    font-weight: 950;
}

.summary-divider {
    border: 0;
    border-top: 1px solid #fde68a;
    margin: 18px 0;
}

.summary-table-card {
    border: 1px solid #fde68a;
    border-radius: 18px;
    overflow: hidden;
}

.alert-theme {
    border-radius: 16px;
    padding: 12px 14px;
    font-weight: 750;
}

.alert-error-theme {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-success-theme {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.table thead th {
    background: #fff7ed;
    color: #7f1d1d;
    font-size: .74rem;
    text-transform: uppercase;
    font-weight: 900;
    border-bottom: 1px solid #fde68a;
    white-space: nowrap;
}

.table tbody td {
    color: #431407;
    font-weight: 650;
    vertical-align: middle;
}

@media (max-width: 992px) {
    .search-panel {
        gap: 12px;
    }

}
</style>

@if(session('success'))
<div class="alert-theme alert-success-theme mb-4">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="alert-theme alert-error-theme mb-4">
    <i class="bi bi-exclamation-triangle me-1"></i>
    {{ $errors->first() }}
</div>
@endif

<div class="d-flex flex-wrap gap-2 justify-content-end mb-4">
    <a href="{{ route('admin.sales.held') }}" class="btn-soft">
        <i class="bi bi-pause-circle me-1"></i> Hold Bills
    </a>
    <a href="{{ route('admin.sales.confirmed') }}" class="btn-action-main text-decoration-none">
        <i class="bi bi-check2-circle me-1"></i> Confirm Bills
    </a>
</div>

<form method="POST" id="billing-form">
    @csrf
    <input type="hidden" name="sale_id" value="{{ $sale?->id }}">
    <input type="hidden" name="items" id="items_payload">

    <div class="billing-card mb-4">
        <div class="billing-card-header">
            <h5><i class="bi bi-search me-1"></i> Product Search / Manual Product Add</h5>
        </div>
        <div class="billing-card-body">
            <div class="search-panel">
                <div>
                    <input type="text" id="product-search" class="form-control theme-control mb-3"
                        placeholder="Product name, variant, barcode, SKU">
                    <div id="search-message" class="small fw-bold text-muted mb-3">Type to search products.</div>
                </div>
                <div id="search-results" class="search-results-grid"></div>
            </div>
        </div>
    </div>

    <div class="billing-card mb-4">
        <div class="billing-card-header">
            <h5><i class="bi bi-cart-check me-1"></i> Bill Items</h5>
        </div>
        <div class="billing-card-body">
            <div id="cart-error" class="alert-theme alert-error-theme mb-3 d-none"></div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product / Variant</th>
                            <th>Stock</th>
                            <th>Our Price</th>
                            <th>Qty</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No products added.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" id="confirm-items-btn" class="btn-action-main">
                    <i class="bi bi-check-circle me-1"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <div class="billing-card">
        <div class="billing-card-header">
            <h5><i class="bi bi-receipt me-1"></i> Billing Summary</h5>
        </div>
        <div class="billing-card-body">
            <div class="summary-table-card table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>QTY</th>
                            <th>Selling Price</th>
                            <th>Our Price</th>
                            <th>Discount Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="summary-body">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No confirmed bill items.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <hr class="summary-divider">

            <div class="summary-row"><span>Total</span><span id="total-text">Rs. 0.00</span></div>
            <div class="summary-row"><span>Subtotal</span><span id="subtotal-text">Rs. 0.00</span></div>
            <div class="summary-row">
                <span>{{ $tax?->tax_name ?? 'Tax' }}
                    {{ $tax ? '(' . number_format($tax->tax_rate, 2) . '%)' : '(0.00%)' }}</span>
                <span id="tax-text">Rs. 0.00</span>
            </div>
            <div class="summary-row border-0">
                <span class="summary-total">Grand Total</span>
                <span class="summary-total" id="grand-text">Rs. 0.00</span>
            </div>

            <hr class="summary-divider">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-select theme-control">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Paid Amount</label>
                    <input type="number" min="0" step="0.01" name="paid_amount" id="paid_amount"
                        class="form-control theme-control" value="0">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Balance</label>
                    <div class="form-control theme-control" id="balance-text">Rs. 0.00</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Due</label>
                    <div class="form-control theme-control" id="due-text">Rs. 0.00</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold text-danger">Customer</label>
                    <select name="customer_id" id="customer_id" class="form-select theme-control">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ (string) old('customer_id', $sale?->customer_id) === (string) $customer->id ? 'selected' : '' }}>
                            {{ $customer->customer_name ?? $customer->name ?? ('Customer #' . $customer->id) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-none" id="reference-wrap">
                    <label class="form-label fw-bold text-danger">Reference Number</label>
                    <input type="text" name="reference_no" id="reference_no" class="form-control theme-control">
                </div>
            </div>

            <input type="hidden" name="notes" value="{{ old('notes', $sale?->notes) }}">

            <hr class="summary-divider">

            <div class="d-flex flex-wrap justify-content-end gap-2">
                <button type="submit" formaction="{{ route('admin.sales.hold') }}" class="btn-soft">
                    <i class="bi bi-pause-circle me-1"></i> Hold Bill
                </button>
                <button type="submit" formaction="{{ route('admin.sales.confirm') }}" class="btn-action-main">
                    <i class="bi bi-printer me-1"></i> Confirm & Print
                </button>
            </div>
        </div>
    </div>
</form>

@if($sale)
<form method="POST" action="{{ route('admin.sales.cancel', $sale) }}" class="mt-3"
    onsubmit="return confirm('Cancel this bill?');">
    @csrf
    <button type="submit" class="btn-danger-soft">
        <i class="bi bi-x-circle me-1"></i> Cancel This Bill
    </button>
</form>
@endif

@php
$activeTaxRate = (float) ($tax->tax_rate ?? 0);
$activeTaxType = $tax->tax_type ?? 'exclusive';
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('product-search');
    const searchResults = document.getElementById('search-results');
    const searchMessage = document.getElementById('search-message');
    const cartBody = document.getElementById('cart-body');
    const summaryBody = document.getElementById('summary-body');
    const cartError = document.getElementById('cart-error');
    const itemsPayload = document.getElementById('items_payload');
    const paymentMethod = document.getElementById('payment_method');
    const paidAmount = document.getElementById('paid_amount');
    const referenceWrap = document.getElementById('reference-wrap');
    const confirmItemsBtn = document.getElementById('confirm-items-btn');

    const tax = {
        rate: Number(@json($activeTaxRate)),
        type: @json($activeTaxType)
    };

    let billItems = [];
    let confirmedItems = @json($cartItems);
    let searchTimer = null;

    const money = value => 'Rs. ' + Number(value || 0).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    function showError(message) {
        cartError.textContent = message;
        cartError.classList.remove('d-none');
        setTimeout(() => cartError.classList.add('d-none'), 3500);
    }

    function hasDiscount(item) {
        return Boolean(item.has_discount) || (
            Number(item.discount_price || 0) > 0 &&
            Number(item.discount_price || 0) < Number(item.our_price || 0)
        );
    }

    function itemPrice(item) {
        return hasDiscount(item) ? Number(item.discount_price || 0) : Number(item.our_price || 0);
    }

    function itemLineTotal(item) {
        return itemPrice(item) * Number(item.quantity || 0);
    }

    function totals() {
        const subtotal = confirmedItems.reduce((sum, item) => sum + itemLineTotal(item), 0);
        let taxAmount = 0;
        let grand = subtotal;

        if (tax.rate > 0) {
            if (tax.type === 'inclusive') {
                taxAmount = subtotal - (subtotal / (1 + tax.rate / 100));
            } else {
                taxAmount = subtotal * tax.rate / 100;
                grand = subtotal + taxAmount;
            }
        }

        return {
            subtotal,
            taxAmount,
            grand
        };
    }

    function updatePayload() {
        itemsPayload.value = JSON.stringify(confirmedItems.map(item => ({
            id: item.id,
            quantity: item.quantity
        })));
    }

    function renderSummary() {
        const result = totals();
        const paid = Number(paidAmount.value || 0);
        const isCredit = paymentMethod.value === 'credit';

        document.getElementById('total-text').textContent = money(result.subtotal);
        document.getElementById('subtotal-text').textContent = money(result.subtotal);
        document.getElementById('tax-text').textContent = money(result.taxAmount);
        document.getElementById('grand-text').textContent = money(result.grand);
        document.getElementById('balance-text').textContent = money(isCredit ? 0 : Math.max(paid - result.grand,
            0));
        document.getElementById('due-text').textContent = money(isCredit ? Math.max(result.grand - paid, 0) :
            0);

        referenceWrap.classList.toggle('d-none', !['card', 'bank_transfer'].includes(paymentMethod.value));
        updatePayload();

        if (confirmedItems.length === 0) {
            summaryBody.innerHTML =
                `<tr><td colspan="6" class="text-center text-muted py-4">No confirmed bill items.</td></tr>`;
            return;
        }

        summaryBody.innerHTML = confirmedItems.map(item => `
            <tr>
                <td><strong>${item.product_name}</strong><br><span class="small">${item.variant_name}</span></td>
                <td>${item.quantity}</td>
                <td>${money(item.unit_price)}</td>
                <td>${money(item.our_price || 0)}</td>
                <td>${hasDiscount(item) ? money(item.discount_price) : 'N/A'}</td>
                <td class="fw-bold text-danger">${money(itemLineTotal(item))}</td>
            </tr>
        `).join('');
    }

    function renderCart() {
        if (billItems.length === 0) {
            cartBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No products added.</td></tr>`;
            renderSummary();
            return;
        }

        cartBody.innerHTML = billItems.map(item => {
            const lineTotal = itemLineTotal(item);

            return `<tr>
                <td><strong class="text-danger">${item.product_name}</strong><br><span class="small">${item.variant_name}</span></td>
                <td>${item.available_stock}</td>
                <td>${money(itemPrice(item))}</td>
                <td>
                    <div class="d-flex align-items-center gap-1">
                        <button type="button" class="qty-btn" data-action="dec" data-id="${item.id}">-</button>
                        <input type="number" min="1" class="form-control theme-control qty-input" data-action="qty" data-id="${item.id}" value="${item.quantity}">
                        <button type="button" class="qty-btn" data-action="inc" data-id="${item.id}">+</button>
                    </div>
                </td>
                <td>${hasDiscount(item) ? money(Number(item.our_price || 0) - Number(item.discount_price || 0)) : 'N/A'}</td>
                <td>${money(lineTotal)}</td>
                <td class="text-end"><button type="button" class="btn-danger-soft" data-action="remove" data-id="${item.id}"><i class="bi bi-trash"></i></button></td>
            </tr>`;
        }).join('');

        renderSummary();
    }

    function setQuantity(id, quantity) {
        const item = billItems.find(row => Number(row.id) === Number(id));

        if (!item) return;

        if (quantity < 1) {
            showError('Quantity must be at least 1.');
            quantity = 1;
        }

        if (quantity > Number(item.available_stock)) {
            showError(`Available stock is ${item.available_stock}.`);
            quantity = Number(item.available_stock);
        }

        item.quantity = quantity;
        renderCart();
    }

    function addToCart(product) {
        const existing = billItems.find(item => Number(item.id) === Number(product.id));

        if (existing) {
            setQuantity(existing.id, Number(existing.quantity) + 1);
            return;
        }

        billItems.push({
            ...product,
            quantity: 1
        });
        renderCart();
    }

    function mergeConfirmedItem(item) {
        const existing = confirmedItems.find(row => Number(row.id) === Number(item.id));

        if (existing) {
            const newQuantity = Number(existing.quantity) + Number(item.quantity);
            existing.quantity = Math.min(newQuantity, Number(item.available_stock));
            return;
        }

        confirmedItems.push({
            ...item
        });
    }

    cartBody.addEventListener('click', function(event) {
        const button = event.target.closest('button[data-action]');
        if (!button) return;

        const id = button.dataset.id;
        const action = button.dataset.action;
        const item = billItems.find(row => Number(row.id) === Number(id));

        if (action === 'inc') setQuantity(id, Number(item.quantity) + 1);
        if (action === 'dec') setQuantity(id, Number(item.quantity) - 1);
        if (action === 'remove') {
            billItems = billItems.filter(row => Number(row.id) !== Number(id));
            renderCart();
        }
    });

    cartBody.addEventListener('change', function(event) {
        if (event.target.dataset.action === 'qty') {
            setQuantity(event.target.dataset.id, Number(event.target.value || 1));
        }
    });

    confirmItemsBtn.addEventListener('click', function() {
        if (billItems.length === 0) {
            showError('Please add products to bill items first.');
            return;
        }

        billItems.forEach(mergeConfirmedItem);
        billItems = [];
        renderCart();
    });

    function renderSearchResults(products) {
        if (products.length === 0) {
            searchResults.innerHTML = '';
            searchMessage.textContent = 'No active stocked sub products found.';
            return;
        }

        searchMessage.textContent = '';
        searchResults.innerHTML = products.map(product => {
            const discounted = hasDiscount(product);

            return `
            <div class="product-result">
                <div>Product name: ${product.product_name}</div>
                <div>Sub product name: ${product.variant_name}${product.size ? ' (' + product.size + ')' : ''}</div>
                <div>Barcode: ${product.barcode || 'N/A'}</div>
                <div>SKU: ${product.sku || 'N/A'}</div>
                <div>Selling price: ${money(product.unit_price)}</div>
                <div>Our price: ${money(product.our_price || 0)}</div>
                <div>Discount price: ${discounted ? money(product.discount_price) : 'N/A'}</div>
                <div>Available stock: ${product.available_stock}</div>

                <div class="d-flex justify-content-end align-items-center mt-3">
                    <button type="button" class="btn-add" data-product="${encodeURIComponent(JSON.stringify(product))}">
                        Add
                    </button>
                </div>
            </div>
        `;
        }).join('');
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        const query = searchInput.value.trim();

        if (query.length < 1) {
            searchResults.innerHTML = '';
            searchMessage.textContent = 'Type to search products.';
            return;
        }

        searchMessage.textContent = 'Searching...';

        searchTimer = setTimeout(() => {
            fetch(
                    `{{ route('admin.sales.search-products') }}?search=${encodeURIComponent(query)}`
                    )
                .then(response => response.json())
                .then(renderSearchResults)
                .catch(() => searchMessage.textContent = 'Product search failed.');
        }, 250);
    });

    searchResults.addEventListener('click', function(event) {
        const button = event.target.closest('button[data-product]');
        if (button) {
            addToCart(JSON.parse(decodeURIComponent(button.dataset.product)));
        }
    });

    paymentMethod.addEventListener('change', renderSummary);
    paidAmount.addEventListener('input', renderSummary);

    renderCart();
});
</script>

@endsection