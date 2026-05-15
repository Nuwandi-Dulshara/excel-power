@extends('admin.layouts.app')

@section('page-title', 'Edit Product')
@section('page-subtitle', 'Update main product details')

@section('content')

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

.btn-save:hover {
    color: white;
    transform: translateY(-1px)
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

.btn-cancel:hover {
    background: #fde68a;
    color: #450a0a
}

.preview-img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border-radius: 16px;
    border: 1px solid #fde68a
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h4>Edit Product</h4>
        <p>Update the selected main product details.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}"
                        class="form-control theme-control" required>
                    @error('product_name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Product Code</label>
                    <input type="text" name="product_code" value="{{ old('product_code', $product->product_code) }}"
                        class="form-control theme-control">
                    @error('product_code') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Parent Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select theme-control" required>
                        <option value="">Select Parent Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                    <input type="text" name="sub_category" value="{{ old('sub_category', $product->sub_category) }}"
                        class="form-control theme-control" required>
                    @error('sub_category') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Product Unit <span class="text-danger">*</span></label>
                    <select name="product_unit_id" class="form-select theme-control" required>
                        <option value="">Select Product Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}"
                            {{ old('product_unit_id', $product->product_unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->unit_name }} ({{ $unit->short_code }})
                        </option>
                        @endforeach
                    </select>
                    @error('product_unit_id') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select theme-control" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                        class="form-control theme-control">
                    @error('brand') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Product Image</label>
                    <input type="file" name="image" class="form-control theme-control" accept="image/*">
                    @error('image') <div class="error-msg">{{ $message }}</div> @enderror

                    @if($product->image)
                    <div class="mt-3">
                        <img src="{{ asset('storage/' . $product->image) }}" class="preview-img" alt="Product Image">
                    </div>
                    @endif
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4"
                        class="form-control theme-control">{{ old('description', $product->description) }}</textarea>
                    @error('description') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>
                            Active</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle me-1"></i> Update Product
                </button>

                <a href="{{ route('admin.products.index') }}" class="btn-cancel">
                    Back to Products
                </a>
            </div>
        </form>
    </div>
</div>

@endsection