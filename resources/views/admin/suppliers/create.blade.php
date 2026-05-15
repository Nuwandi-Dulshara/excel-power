@extends('admin.layouts.app')

@section('page-title', 'Add New Supplier')
@section('page-subtitle', 'Create supplier records for product purchasing and stock management')

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
        <h4>Add New Supplier</h4>
        <p>Use this form to create a supplier for purchasing, stock receiving, and supplier balance tracking.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}"
                        class="form-control theme-control" placeholder="Example: ABC Hardware Suppliers" required>

                    @error('supplier_name')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Supplier Code <span class="text-danger">*</span></label>
                    <input type="text" name="supplier_code" value="{{ old('supplier_code') }}"
                        class="form-control theme-control" placeholder="Example: SUP-001" required>

                    @error('supplier_code')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        class="form-control theme-control" placeholder="Example: Mr. Perera">

                    @error('contact_person')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control theme-control"
                        placeholder="Example: 0771234567">

                    @error('phone')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control theme-control"
                        placeholder="Example: supplier@example.com">

                    @error('email')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                        class="form-control theme-control" placeholder="Example: ABC Trading Pvt Ltd">

                    @error('company_name')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">BR Number</label>
                    <input type="text" name="br_number" value="{{ old('br_number') }}"
                        class="form-control theme-control" placeholder="Example: PV123456">

                    @error('br_number')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">VAT Number</label>
                    <input type="text" name="vat_number" value="{{ old('vat_number') }}"
                        class="form-control theme-control" placeholder="Example: 123456789-7000">

                    @error('vat_number')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Opening Balance</label>
                    <input type="number" step="0.01" min="0" name="opening_balance"
                        value="{{ old('opening_balance', 0) }}" class="form-control theme-control"
                        placeholder="Example: 0.00">

                    @error('opening_balance')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Payment Terms</label>
                    <select name="payment_terms" class="form-select theme-control">
                        <option value="">Select Payment Terms</option>
                        <option value="Cash" {{ old('payment_terms') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Credit" {{ old('payment_terms') == 'Credit' ? 'selected' : '' }}>Credit</option>
                        <option value="7 Days" {{ old('payment_terms') == '7 Days' ? 'selected' : '' }}>7 Days</option>
                        <option value="15 Days" {{ old('payment_terms') == '15 Days' ? 'selected' : '' }}>15 Days
                        </option>
                        <option value="30 Days" {{ old('payment_terms') == '30 Days' ? 'selected' : '' }}>30 Days
                        </option>
                        <option value="45 Days" {{ old('payment_terms') == '45 Days' ? 'selected' : '' }}>45 Days
                        </option>
                        <option value="60 Days" {{ old('payment_terms') == '60 Days' ? 'selected' : '' }}>60 Days
                        </option>
                    </select>

                    @error('payment_terms')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" class="form-control theme-control"
                        placeholder="Example: No. 25, Main Street, Colombo">{{ old('address') }}</textarea>

                    @error('address')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control theme-control"
                        placeholder="Example: Special supplier remarks, delivery instructions, or credit details">{{ old('notes') }}</textarea>

                    @error('notes')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    @error('status')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle me-1"></i>
                    Save Supplier
                </button>

                <a href="{{ route('admin.suppliers.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection