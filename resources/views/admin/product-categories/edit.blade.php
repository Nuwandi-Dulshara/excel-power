@extends('admin.layouts.app')

@section('page-title', 'Edit Parent Category')
@section('page-subtitle', 'Update top-level product category details')

@section('content')

<style>
.form-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #fde68a;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
    overflow: hidden;
}

.form-card-header {
    background:
        radial-gradient(circle at top right, rgba(212, 175, 55, 0.25), transparent 30%),
        linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    padding: 28px;
}

.form-card-header h4 {
    font-weight: 900;
    margin-bottom: 6px;
}

.form-card-header p {
    opacity: 0.85;
    margin-bottom: 0;
}

.form-body {
    padding: 30px;
}

.form-label {
    font-weight: 800;
    color: #7f1d1d;
    font-size: 0.88rem;
    margin-bottom: 8px;
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
    box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.1);
}

.error-msg {
    color: #ef4444;
    font-size: 0.78rem;
    font-weight: 700;
    margin-top: 5px;
}

.btn-save {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 850;
    box-shadow: 0 12px 24px rgba(185, 28, 28, 0.25);
}

.btn-save:hover {
    color: white;
    transform: translateY(-1px);
}

.btn-cancel {
    background: #fff7ed;
    color: #7f1d1d;
    border: none;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 850;
    text-decoration: none;
}

.btn-cancel:hover {
    background: #fde68a;
    color: #450a0a;
}
</style>

<div class="form-card">
    <div class="form-card-header">
        <h4>Edit Parent Category</h4>
        <p>Update the selected top-level product category.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.product-categories.update', $productCategory->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Parent Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="category_name"
                        value="{{ old('category_name', $productCategory->category_name) }}"
                        class="form-control theme-control" placeholder="Example: Electrical Items" required>

                    @error('category_name')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Parent Category Code <span class="text-danger">*</span></label>
                    <input type="text" name="category_code"
                        value="{{ old('category_code', $productCategory->category_code) }}"
                        class="form-control theme-control" placeholder="Example: ELEC" required>

                    @error('category_code')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control theme-control"
                        placeholder="Example: Main group for electrical and hardware products">{{ old('description', $productCategory->description) }}</textarea>

                    @error('description')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $productCategory->status) == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ old('status', $productCategory->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>

                    @error('status')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle me-1"></i>
                    Update Parent Category
                </button>

                <a href="{{ route('admin.product-categories.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
