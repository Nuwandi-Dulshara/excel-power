@extends('admin.layouts.app')

@section('page-title', 'Edit Tax')
@section('page-subtitle', 'Update POS tax setting')

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
</style>

<div class="form-card">
    <div class="form-card-header">
        <h4>Edit Tax</h4>
        <p>Update tax rate, type, status, and description.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.taxes.update', $tax->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Tax Name <span class="text-danger">*</span></label>
                    <input type="text" name="tax_name" value="{{ old('tax_name', $tax->tax_name) }}"
                        class="form-control theme-control" required>
                    @error('tax_name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tax Rate (%) <span class="text-danger">*</span></label>
                    <input type="number" name="tax_rate" value="{{ old('tax_rate', $tax->tax_rate) }}"
                        class="form-control theme-control" step="0.01" min="0" required>
                    @error('tax_rate') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tax Type <span class="text-danger">*</span></label>
                    <select name="tax_type" class="form-select theme-control" required>
                        <option value="">Select Tax Type</option>
                        <option value="exclusive" {{ old('tax_type', $tax->tax_type) == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                        <option value="inclusive" {{ old('tax_type', $tax->tax_type) == 'inclusive' ? 'selected' : '' }}>Inclusive</option>
                    </select>
                    @error('tax_type') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $tax->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $tax->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4"
                        class="form-control theme-control">{{ old('description', $tax->description) }}</textarea>
                    @error('description') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle me-1"></i> Update Tax
                </button>

                <a href="{{ route('admin.taxes.index') }}" class="btn-cancel">
                    Back to Taxes
                </a>
            </div>
        </form>
    </div>
</div>

@endsection