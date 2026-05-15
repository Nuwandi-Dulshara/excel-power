@extends('admin.layouts.app')

@section('page-title', 'Edit Sub Product')
@section('page-subtitle', 'Update product variant details')

@section('content')

<style>
.info-card,
.form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #fde68a;
    box-shadow: 0 14px 35px rgba(15, 23, 42, .07);
    overflow: hidden
}

.info-card {
    padding: 24px;
    margin-bottom: 24px
}

.info-title {
    font-weight: 900;
    color: #7f1d1d
}

.info-label {
    font-size: .78rem;
    font-weight: 900;
    color: #92400e;
    text-transform: uppercase
}

.info-value {
    font-weight: 800;
    color: #431407
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
</style>

<div class="info-card">
    <h5 class="info-title mb-3">Main Product Information</h5>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="info-label">Product Name</div>
            <div class="info-value">{{ $product->product_name }}</div>
        </div>

        <div class="col-md-4">
            <div class="info-label">Product Code</div>
            <div class="info-value">{{ $product->product_code ?? 'N/A' }}</div>
        </div>

        <div class="col-md-4">
            <div class="info-label">Category</div>
            <div class="info-value">{{ $product->category->category_name ?? 'N/A' }}</div>
        </div>

        <div class="col-md-4">
            <div class="info-label">Sub Category</div>
            <div class="info-value">{{ $product->sub_category }}</div>
        </div>

        <div class="col-md-4">
            <div class="info-label">Unit</div>
            <div class="info-value">{{ $product->unit->unit_name ?? 'N/A' }}</div>
        </div>

        <div class="col-md-4">
            <div class="info-label">Supplier</div>
            <div class="info-value">{{ $product->supplier->supplier_name ?? 'N/A' }}</div>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="form-card-header">
        <h4>Edit Sub Product</h4>
        <p>Update variant price, stock, barcode, and status.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.products.variants.update', [$product->id, $variant->id]) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Main Product</label>
                    <input type="text" value="{{ $product->product_name }}" class="form-control theme-control" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sub Product Name / Variant Name <span class="text-danger">*</span></label>
                    <input type="text" name="variant_name" value="{{ old('variant_name', $variant->variant_name) }}"
                        class="form-control theme-control" required>
                    @error('variant_name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Size / Weight / Volume <span class="text-danger">*</span></label>
                    <input type="text" name="size" value="{{ old('size', $variant->size) }}"
                        class="form-control theme-control" required>
                    @error('size') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $variant->barcode) }}"
                        class="form-control theme-control">
                    @error('barcode') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $variant->sku) }}"
                        class="form-control theme-control">
                    @error('sku') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4"
                        class="form-control theme-control">{{ old('description', $variant->description) }}</textarea>
                    @error('description') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Purchase Price For One Piece <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" id="purchase_price" name="purchase_price"
                        value="{{ old('purchase_price', $variant->purchase_price) }}" class="form-control theme-control"
                        required>
                    @error('purchase_price') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Purchase Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="1" name="purchase_quantity"
                        value="{{ old('purchase_quantity', $variant->purchase_quantity) }}"
                        class="form-control theme-control" required>
                    @error('purchase_quantity') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cost Price</label>
                    <input type="number" step="0.01" min="0" name="cost_price"
                        value="{{ old('cost_price', $variant->cost_price) }}" class="form-control theme-control">
                    @error('cost_price') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Selling Price For One Piece <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="selling_price"
                        value="{{ old('selling_price', $variant->selling_price) }}" class="form-control theme-control"
                        required>
                    @error('selling_price') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Our Price / Actual Price For One Piece <span
                            class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="our_price"
                        value="{{ old('our_price', $variant->our_price) }}" class="form-control theme-control" required>
                    @error('our_price') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Minimum Wholesale Pieces Quantity</label>
                    <input type="number" min="1" name="minimum_wholesale_quantity"
                        value="{{ old('minimum_wholesale_quantity', $variant->minimum_wholesale_quantity) }}"
                        class="form-control theme-control">
                    @error('minimum_wholesale_quantity') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Wholesale Price For One Piece</label>
                    <input type="number" step="0.01" min="0" name="wholesale_price"
                        value="{{ old('wholesale_price', $variant->wholesale_price) }}"
                        class="form-control theme-control">
                    @error('wholesale_price') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Opening Stock <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="opening_stock"
                        value="{{ old('opening_stock', $variant->opening_stock) }}" class="form-control theme-control"
                        required>
                    @error('opening_stock') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Current Stock <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="current_stock"
                        value="{{ old('current_stock', $variant->current_stock) }}" class="form-control theme-control"
                        required>
                    @error('current_stock') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Reorder Level</label>
                    <input type="number" min="0" name="reorder_level"
                        value="{{ old('reorder_level', $variant->reorder_level) }}" class="form-control theme-control">
                    @error('reorder_level') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date"
                        value="{{ old('expiry_date', optional($variant->expiry_date)->format('Y-m-d')) }}"
                        class="form-control theme-control">
                    @error('expiry_date') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $variant->status) == 'active' ? 'selected' : '' }}>
                            Active</option>
                        <option value="inactive" {{ old('status', $variant->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle me-1"></i> Update Sub Product
                </button>

                <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn-cancel">
                    Back to Sub Products
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
