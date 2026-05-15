@extends('admin.layouts.app')

@section('page-title', 'Manage Suppliers')
@section('page-subtitle', 'View, search, filter, edit, and manage supplier records')

@section('content')

<style>
.supplier-stat-card {
    background: linear-gradient(135deg, #b91c1c, #d4af37);
    color: white;
    border-radius: 22px;
    border: 1px solid #fde68a;
    padding: 24px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    position: relative;
    overflow: hidden;
    height: 100%;
}

.supplier-stat-card::after {
    content: "";
    position: absolute;
    width: 150px;
    height: 150px;
    right: -60px;
    top: -60px;
    background: rgba(255, 255, 255, 0.18);
    border-radius: 50%;
}

.stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.18);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 16px;
}

.stat-title {
    color: white;
    font-size: 0.9rem;
    opacity: .85;
    font-weight: 700;
    text-transform: none;
    letter-spacing: 0;
}

.stat-value {
    color: white;
    font-weight: 900;
    font-size: 2.2rem;
    margin: 0;
}

.action-card {
    background: white;
    border-radius: 22px;
    border: 1px solid #fde68a;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
    overflow: hidden;
}

.action-card-header {
    color: #212529;
    padding: 24px 24px 0;
}

.action-card-header h4 {
    font-weight: 900;
    margin-bottom: 5px;
}

.action-card-header p {
    color: #6c757d;
    margin-bottom: 0;
}

.filter-body {
    padding: 24px;
}

.form-label {
    font-weight: 800;
    color: #7f1d1d;
    font-size: 0.84rem;
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

.btn-add {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 18px;
    font-weight: 850;
    text-decoration: none;
    box-shadow: 0 12px 24px rgba(185, 28, 28, 0.25);
}

.btn-add:hover {
    color: white;
    transform: translateY(-1px);
}

.btn-filter {
    background: #7f1d1d;
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 18px;
    font-weight: 850;
}

.btn-filter:hover {
    color: white;
    background: #450a0a;
}

.btn-reset {
    background: #fff7ed;
    color: #7f1d1d;
    border: none;
    border-radius: 14px;
    padding: 12px 18px;
    font-weight: 850;
    text-decoration: none;
}

.btn-reset:hover {
    background: #fde68a;
    color: #450a0a;
}

.table-card {
    background: white;
    border-radius: 24px;
    border: 1px solid #fde68a;
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.07);
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #fff7ed;
    color: #7f1d1d;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    font-weight: 900;
    border-bottom: 1px solid #fde68a;
    padding: 16px;
    white-space: nowrap;
}

.table tbody td {
    padding: 16px;
    vertical-align: middle;
    color: #431407;
    font-weight: 600;
    border-bottom: 1px solid #fef3c7;
}

.supplier-main {
    font-weight: 900;
    color: #7f1d1d;
}

.supplier-code {
    display: inline-block;
    background: #fef3c7;
    color: #92400e;
    border-radius: 999px;
    padding: 5px 10px;
    font-size: .75rem;
    font-weight: 850;
    margin-top: 4px;
}

.status-badge {
    border-radius: 999px;
    padding: 6px 12px;
    font-size: .75rem;
    font-weight: 900;
}

.status-active {
    background: #dcfce7;
    color: #166534;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.balance-pill {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #7f1d1d;
    border-radius: 999px;
    padding: 7px 12px;
    font-weight: 900;
    white-space: nowrap;
}

.payment-pill {
    background: #fef3c7;
    color: #92400e;
    border-radius: 999px;
    padding: 6px 11px;
    font-size: .75rem;
    font-weight: 850;
    white-space: nowrap;
}

.btn-action {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    text-decoration: none;
}

.btn-edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-edit:hover {
    background: #fde68a;
    color: #7f1d1d;
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
}

.btn-delete:hover {
    background: #fecaca;
    color: #7f1d1d;
}

.empty-box {
    padding: 50px 20px;
    text-align: center;
    color: #92400e;
}

.empty-box i {
    font-size: 3rem;
    color: #d4af37;
    margin-bottom: 15px;
}

.alert-success-theme {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 14px 18px;
    font-weight: 750;
}
</style>

@if(session('success'))
<div class="alert-success-theme mb-4">
    <i class="bi bi-check-circle me-1"></i>
    {{ session('success') }}
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="supplier-stat-card">
            <div class="stat-icon">
                <i class="bi bi-truck"></i>
            </div>
            <div class="stat-title">Total Suppliers</div>
            <div class="stat-value">{{ $totalSuppliers }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="supplier-stat-card">
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-title">Active Suppliers</div>
            <div class="stat-value">{{ $activeSuppliers }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="supplier-stat-card">
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-title">Inactive Suppliers</div>
            <div class="stat-value">{{ $inactiveSuppliers }}</div>
        </div>
    </div>

    <div class="col-12">
        <div class="supplier-stat-card">
            <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="stat-title">Total Opening Balance</div>
            <div class="stat-value">Rs. {{ number_format($totalOpeningBalance, 2) }}</div>
        </div>
    </div>
</div>

<div class="action-card mb-4">
    <div class="action-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4>Supplier Directory</h4>
            <p>Search and filter suppliers by contact details, status, payment terms, and balance.</p>
        </div>

        <a href="{{ route('admin.suppliers.create') }}" class="btn-add">
            <i class="bi bi-plus-circle me-1"></i>
            Add New Supplier
        </a>
    </div>

    <div class="filter-body">
        <form method="GET" action="{{ route('admin.suppliers.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label">Search Supplier</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control theme-control"
                        placeholder="Search by supplier name, code, phone, email, company, BR number">
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Status Filter</label>
                    <select name="status" class="form-select theme-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Payment Terms</label>
                    <select name="payment_terms" class="form-select theme-control">
                        <option value="">All Payment Terms</option>
                        <option value="Cash" {{ request('payment_terms') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Credit" {{ request('payment_terms') == 'Credit' ? 'selected' : '' }}>Credit
                        </option>
                        <option value="7 Days" {{ request('payment_terms') == '7 Days' ? 'selected' : '' }}>7 Days
                        </option>
                        <option value="15 Days" {{ request('payment_terms') == '15 Days' ? 'selected' : '' }}>15 Days
                        </option>
                        <option value="30 Days" {{ request('payment_terms') == '30 Days' ? 'selected' : '' }}>30 Days
                        </option>
                        <option value="45 Days" {{ request('payment_terms') == '45 Days' ? 'selected' : '' }}>45 Days
                        </option>
                        <option value="60 Days" {{ request('payment_terms') == '60 Days' ? 'selected' : '' }}>60 Days
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Opening Balance</label>
                    <select name="balance_filter" class="form-select theme-control">
                        <option value="">All</option>
                        <option value="with_balance"
                            {{ request('balance_filter') == 'with_balance' ? 'selected' : '' }}>With Balance</option>
                        <option value="no_balance" {{ request('balance_filter') == 'no_balance' ? 'selected' : '' }}>No
                            Balance</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-6 d-flex gap-2">
                    <button type="submit" class="btn-filter w-100">
                        <i class="bi bi-search me-1"></i>
                        Filter
                    </button>

                    <a href="{{ route('admin.suppliers.index') }}" class="btn-reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Contact</th>
                    <th>Company Details</th>
                    <th>Payment</th>
                    <th>Opening Balance</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td>
                        <div class="supplier-main">{{ $supplier->supplier_name }}</div>
                        <span class="supplier-code">{{ $supplier->supplier_code }}</span>
                    </td>

                    <td>
                        <div>
                            <i class="bi bi-person me-1 text-muted"></i>
                            {{ $supplier->contact_person ?? 'N/A' }}
                        </div>
                        <div class="small text-muted mt-1">
                            <i class="bi bi-telephone me-1"></i>
                            {{ $supplier->phone ?? 'N/A' }}
                        </div>
                        <div class="small text-muted mt-1">
                            <i class="bi bi-envelope me-1"></i>
                            {{ $supplier->email ?? 'N/A' }}
                        </div>
                    </td>

                    <td>
                        <div class="fw-bold">{{ $supplier->company_name ?? 'N/A' }}</div>
                        <div class="small text-muted mt-1">BR: {{ $supplier->br_number ?? 'N/A' }}</div>
                        <div class="small text-muted mt-1">VAT: {{ $supplier->vat_number ?? 'N/A' }}</div>
                    </td>

                    <td>
                        <span class="payment-pill">
                            {{ $supplier->payment_terms ?? 'Not Set' }}
                        </span>
                    </td>

                    <td>
                        <span class="balance-pill">
                            Rs. {{ number_format($supplier->opening_balance, 2) }}
                        </span>
                    </td>

                    <td>
                        @if($supplier->status === 'active')
                        <span class="status-badge status-active">Active</span>
                        @else
                        <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="{{ route('admin.suppliers.edit', $supplier->id) }}" class="btn-action btn-edit"
                            title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('admin.suppliers.destroy', $supplier->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn-action btn-delete" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-box">
                            <i class="bi bi-truck"></i>
                            <h5 class="fw-bold">No suppliers found</h5>
                            <p class="mb-0">Create your first supplier or change the filter options.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
    <div class="p-3">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>

@endsection