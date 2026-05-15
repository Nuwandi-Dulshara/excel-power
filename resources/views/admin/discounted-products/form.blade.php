<style>
.form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #fde68a;
    box-shadow: 0 14px 35px rgba(15, 23, 42, .07);
    overflow: hidden
}

.form-card-header {
    background: radial-gradient(circle at top right, rgba(212, 175, 55, .25), transparent 30%), linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    padding: 28px
}

.form-card-header h4 {
    font-weight: 900;
    margin-bottom: 6px
}

.form-card-header p {
    opacity: .85;
    margin-bottom: 0
}

.form-body {
    padding: 30px
}

.form-label {
    font-weight: 800;
    color: #7f1d1d;
    font-size: .88rem;
    margin-bottom: 8px
}

.theme-control {
    border-radius: 14px;
    border: 1px solid #fde68a;
    padding: 12px 14px;
    font-weight: 600;
    color: #7f1d1d;
    background: #fffbeb
}

.theme-control:focus {
    background: white;
    border-color: #b91c1c;
    box-shadow: 0 0 0 4px rgba(185, 28, 28, .1)
}

.error-msg {
    color: #ef4444;
    font-size: .78rem;
    font-weight: 700;
    margin-top: 5px
}

.btn-save {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 850;
    box-shadow: 0 12px 24px rgba(185, 28, 28, .25)
}

.btn-cancel {
    background: #fff7ed;
    color: #7f1d1d;
    border: none;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 850;
    text-decoration: none
}

.info-note {
    background: #fff7ed;
    border: 1px solid #fde68a;
    color: #7f1d1d;
    border-radius: 16px;
    padding: 14px 18px;
    font-weight: 700
}

.searchable-dropdown {
    position: relative;
}

.search-results {
    position: absolute;
    width: 100%;
    background: white;
    border: 1px solid #fde68a;
    border-radius: 14px;
    margin-top: 6px;
    max-height: 260px;
    overflow-y: auto;
    z-index: 9999;
    box-shadow: 0 14px 35px rgba(15, 23, 42, .12);
    display: none;
}

.search-item {
    padding: 12px 14px;
    cursor: pointer;
    border-bottom: 1px solid #fef3c7;
    color: #431407;
    font-weight: 700;
}

.search-item:hover {
    background: #fff7ed;
    color: #b91c1c;
}

.search-item small {
    display: block;
    color: #92400e;
    font-weight: 600;
    margin-top: 3px;
}

.selected-product-box {
    background: #fff7ed;
    border: 1px solid #fde68a;
    color: #7f1d1d;
    border-radius: 14px;
    padding: 12px 14px;
    font-weight: 800;
    margin-top: 10px;
    display: none;
}
</style>

@php
$selectedVariantId = old('product_variant_id', optional($discountedProduct)->product_variant_id);
$selectedVariant = $variants->firstWhere('id', $selectedVariantId);
@endphp

<div class="form-card">
    <div class="form-card-header">
        <h4>{{ $discountedProduct ? 'Edit Discounted Product' : 'Add Discounted Product' }}</h4>
        <p>Select an active sub product. Price details will be calculated automatically.</p>
    </div>

    <div class="form-body">
        <div class="info-note mb-4">
            <i class="bi bi-info-circle me-1"></i>
            Discount price must always be greater than price received.
        </div>

        <form method="POST" action="{{ $formAction }}">
            @csrf

            @if($formMethod === 'PUT')
            @method('PUT')
            @endif

            <div class="row g-4">
                <div class="col-md-12">
                    <label class="form-label">Select Active Sub Product <span class="text-danger">*</span></label>

                    <div class="searchable-dropdown">
                        <input type="text" id="variant_search" class="form-control theme-control"
                            placeholder="Search by product name, sub product name, barcode, SKU, size, supplier..."
                            autocomplete="off"
                            value="{{ $selectedVariant ? (($selectedVariant->product->product_name ?? 'N/A') . ' - ' . $selectedVariant->variant_name . ' ' . ($selectedVariant->size ? '(' . $selectedVariant->size . ')' : '')) : '' }}">

                        <input type="hidden" name="product_variant_id" id="product_variant_id"
                            value="{{ $selectedVariantId }}">

                        <div class="search-results" id="search_results">
                            @foreach($variants as $variant)
                            <div class="search-item" data-id="{{ $variant->id }}"
                                data-name="{{ strtolower(($variant->product->product_name ?? '') . ' ' . $variant->variant_name . ' ' . $variant->size . ' ' . $variant->barcode . ' ' . $variant->sku . ' ' . ($variant->product->supplier->supplier_name ?? '')) }}"
                                data-label="{{ ($variant->product->product_name ?? 'N/A') . ' - ' . $variant->variant_name . ($variant->size ? ' (' . $variant->size . ')' : '') }}">
                                {{ $variant->product->product_name ?? 'N/A' }} - {{ $variant->variant_name }}
                                @if($variant->size)
                                ({{ $variant->size }})
                                @endif

                                <small>
                                    Barcode: {{ $variant->barcode ?? 'N/A' }} |
                                    SKU: {{ $variant->sku ?? 'N/A' }} |
                                    Supplier: {{ $variant->product->supplier->supplier_name ?? 'N/A' }}
                                </small>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="selected-product-box" id="selected_product_box"></div>

                    @error('product_variant_id') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Current Quantity</label>
                    <input type="text" id="current_quantity" class="form-control theme-control" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Discount Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="discount_quantity" id="discount_quantity"
                        value="{{ old('discount_quantity', optional($discountedProduct)->discount_quantity) }}"
                        class="form-control theme-control" required>
                    @error('discount_quantity') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Price Received</label>
                    <input type="text" id="price_received" class="form-control theme-control" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Our Price</label>
                    <input type="text" id="our_price" class="form-control theme-control" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Supplier Name</label>
                    <input type="text" id="supplier_name" class="form-control theme-control" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Maximum Allowed Discount Percentage</label>
                    <input type="text" id="maximum_allowed_discount_percentage" class="form-control theme-control"
                        readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Discount Percentage <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage"
                        id="discount_percentage"
                        value="{{ old('discount_percentage', optional($discountedProduct)->discount_percentage) }}"
                        class="form-control theme-control" required>
                    @error('discount_percentage') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Discount Price</label>
                    <input type="text" id="discount_price" class="form-control theme-control" readonly>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Reason for Discount</label>
                    <textarea name="reason" rows="4" class="form-control theme-control"
                        placeholder="Example: Near expiry, damaged package, special offer">{{ old('reason', optional($discountedProduct)->reason) }}</textarea>
                    @error('reason') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="">Select Status</option>
                        <option value="active"
                            {{ old('status', optional($discountedProduct)->status ?? 'active') == 'active' ? 'selected' : '' }}>
                            Active</option>
                        <option value="inactive"
                            {{ old('status', optional($discountedProduct)->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle me-1"></i> {{ $buttonText }}
                </button>

                <a href="{{ route('admin.discounted-products.index') }}" class="btn-cancel">
                    Back to Discounted Products
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('variant_search');
    const hiddenVariantInput = document.getElementById('product_variant_id');
    const searchResults = document.getElementById('search_results');
    const searchItems = document.querySelectorAll('.search-item');
    const selectedProductBox = document.getElementById('selected_product_box');

    const currentQuantity = document.getElementById('current_quantity');
    const priceReceived = document.getElementById('price_received');
    const ourPrice = document.getElementById('our_price');
    const supplierName = document.getElementById('supplier_name');
    const maxAllowed = document.getElementById('maximum_allowed_discount_percentage');
    const discountPercentage = document.getElementById('discount_percentage');
    const discountPrice = document.getElementById('discount_price');

    function clearDetails() {
        currentQuantity.value = '';
        priceReceived.value = '';
        ourPrice.value = '';
        supplierName.value = '';
        maxAllowed.value = '';
        discountPrice.value = '';
    }

    function calculateDiscountPrice() {
        const our = parseFloat(ourPrice.value || 0);
        const percentage = parseFloat(discountPercentage.value || 0);

        if (our <= 0) {
            discountPrice.value = '';
            return;
        }

        const calculated = our - (our * percentage / 100);
        discountPrice.value = calculated.toFixed(2);
    }

    function loadVariantDetails() {
        const variantId = hiddenVariantInput.value;

        if (!variantId) {
            clearDetails();
            return;
        }

        fetch("{{ url('/admin/discounted-products/variant-details') }}/" + variantId)
            .then(response => response.json())
            .then(data => {
                currentQuantity.value = data.current_quantity;
                priceReceived.value = data.price_received;
                ourPrice.value = data.our_price;
                supplierName.value = data.supplier_name;
                maxAllowed.value = data.maximum_allowed_discount_percentage + '%';
                calculateDiscountPrice();
            })
            .catch(() => {
                clearDetails();
            });
    }

    function filterItems() {
        const keyword = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        searchResults.style.display = 'block';

        searchItems.forEach(item => {
            const itemText = item.dataset.name;

            if (itemText.includes(keyword)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            searchResults.style.display = 'none';
        }
    }

    searchInput.addEventListener('focus', function() {
        filterItems();
    });

    searchInput.addEventListener('input', function() {
        hiddenVariantInput.value = '';
        selectedProductBox.style.display = 'none';
        clearDetails();
        filterItems();
    });

    searchItems.forEach(item => {
        item.addEventListener('click', function() {
            const variantId = this.dataset.id;
            const label = this.dataset.label;

            hiddenVariantInput.value = variantId;
            searchInput.value = label;
            selectedProductBox.innerHTML =
                '<i class="bi bi-check-circle me-1"></i> Selected: ' + label;
            selectedProductBox.style.display = 'block';
            searchResults.style.display = 'none';

            loadVariantDetails();
        });
    });

    discountPercentage.addEventListener('input', calculateDiscountPrice);

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.searchable-dropdown')) {
            searchResults.style.display = 'none';
        }
    });

    if (hiddenVariantInput.value) {
        selectedProductBox.innerHTML = '<i class="bi bi-check-circle me-1"></i> Selected: ' + searchInput.value;
        selectedProductBox.style.display = 'block';
        loadVariantDetails();
    }
});
</script>