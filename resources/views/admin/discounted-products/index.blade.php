@extends('admin.layouts.app')

@section('page-title', 'Discounted Products')
@section('page-subtitle', 'Manage discounted sub products and special prices')

@section('content')

<style>
.stat-card,
.filter-card,
.table-card {
    background: white;
    border: 1px solid #fde68a;
    border-radius: 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .06)
}

.stat-card {
    background: linear-gradient(135deg, #b91c1c, #d4af37);
    color: white;
    padding: 24px;
    height: 100%;
    position: relative;
    overflow: hidden
}

.stat-card::after {
    content: "";
    position: absolute;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,.18);
    right: -60px;
    top: -60px;
    border-radius: 50%
}

.stat-icon {
    width: 54px;
    height: 54px;
    border-radius: 16px;
    background: rgba(255,255,255,.18);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 16px
}

.stat-title {
    color: white;
    font-size: .9rem;
    opacity: .85;
    font-weight: 700
}

.stat-value {
    color: white;
    font-weight: 900;
    font-size: 2.2rem;
    margin: 0
}

.filter-header {
    padding: 24px 24px 0
}

.filter-body {
    padding: 24px
}

.form-label {
    font-weight: 800;
    color: #7f1d1d;
    font-size: .84rem;
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

.btn-add,
.btn-filter {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 12px 18px;
    font-weight: 850;
    text-decoration: none
}

.btn-reset {
    background: #fff7ed;
    color: #7f1d1d;
    border: none;
    border-radius: 14px;
    padding: 12px 18px;
    font-weight: 850;
    text-decoration: none
}

.table {
    margin-bottom: 0
}

.table thead th {
    background: #fff7ed;
    color: #7f1d1d;
    font-size: .74rem;
    text-transform: uppercase;
    font-weight: 900;
    border-bottom: 1px solid #fde68a;
    padding: 14px;
    white-space: nowrap
}

.table tbody td {
    padding: 14px;
    vertical-align: middle;
    color: #431407;
    font-weight: 600;
    border-bottom: 1px solid #fef3c7;
    white-space: nowrap
}

.status-badge {
    border-radius: 999px;
    padding: 6px 12px;
    font-size: .75rem;
    font-weight: 900
}

.status-active {
    background: #dcfce7;
    color: #166534
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b
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
    margin-right: 4px
}

.btn-edit {
    background: #fef3c7;
    color: #92400e
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b
}

.alert-success-theme {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 14px 18px;
    font-weight: 750
}

.empty-box {
    padding: 50px 20px;
    text-align: center;
    color: #92400e
}

.reason-cell {
    min-width: 220px;
    max-width: 320px;
    white-space: normal !important
}
</style>

@if(session('success'))
<div class="alert-success-theme mb-4">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-tags"></i></div>
            <div class="stat-title">Total Discounted Products</div>
            <div class="stat-value">{{ $totalDiscountedProducts }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-title">Active Discounted Products</div>
            <div class="stat-value">{{ $activeDiscountedProducts }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-title">Inactive Discounted Products</div>
            <div class="stat-value">{{ $inactiveDiscountedProducts }}</div>
        </div>
    </div>
</div>

<div class="filter-card mb-4">
    <div class="filter-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1">Discounted Product List</h4>
            <p class="mb-0 opacity-75">Search, filter, edit, and delete discounted products.</p>
        </div>

        <a href="{{ route('admin.discounted-products.create') }}" class="btn-add">
            <i class="bi bi-plus-circle me-1"></i> Add Discounted Product
        </a>
    </div>

    <div class="filter-body">
        <form method="GET" action="{{ route('admin.discounted-products.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-7">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control theme-control"
                        placeholder="Product, sub product, barcode, SKU, supplier, reason">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select theme-control">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn-filter w-100">
                        <i class="bi bi-search"></i>
                    </button>

                    <a href="{{ route('admin.discounted-products.index') }}" class="btn-reset">
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
                    <th>#</th>
                    <th>Main Product</th>
                    <th>Sub Product</th>
                    <th>Current Qty</th>
                    <th>Discount Qty</th>
                    <th>Price Received</th>
                    <th>Our Price</th>
                    <th>Supplier</th>
                    <th>Discount Type</th>
                    <th>Discount Value</th>
                    <th>Discount Price</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($discountedProducts as $discountedProduct)
                <tr>
                    <td>{{ $discountedProducts->firstItem() + $loop->index }}</td>
                    <td>{{ $discountedProduct->variant->product->product_name ?? 'N/A' }}</td>
                    <td class="fw-bold text-danger">{{ $discountedProduct->variant->variant_name ?? 'N/A' }}</td>
                    <td>{{ $discountedProduct->current_quantity }}</td>
                    <td>{{ $discountedProduct->discount_quantity }}</td>
                    <td>Rs. {{ number_format($discountedProduct->price_received, 2) }}</td>
                    <td>Rs. {{ number_format($discountedProduct->our_price, 2) }}</td>
                    <td>{{ $discountedProduct->supplier_name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($discountedProduct->discount_type ?? 'percentage') }}</td>
                    <td>
                        @if(($discountedProduct->discount_type ?? 'percentage') === 'fixed')
                            Rs. {{ number_format($discountedProduct->discount_value ?: 0, 2) }}
                        @else
                            {{ number_format($discountedProduct->discount_value ?: $discountedProduct->discount_percentage, 2) }}%
                        @endif
                    </td>
                    <td class="fw-bold text-danger">Rs. {{ number_format($discountedProduct->discount_price, 2) }}</td>
                    <td>{{ $discountedProduct->start_date ? $discountedProduct->start_date->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $discountedProduct->end_date ? $discountedProduct->end_date->format('Y-m-d') : 'N/A' }}</td>
                    <td class="reason-cell">{{ $discountedProduct->reason ? \Illuminate\Support\Str::limit($discountedProduct->reason, 80) : 'N/A' }}</td>

                    <td>
                        @if($discountedProduct->status === 'active')
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="{{ route('admin.discounted-products.edit', $discountedProduct->id) }}"
                            class="btn-action btn-edit" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('admin.discounted-products.destroy', $discountedProduct->id) }}"
                            method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this discounted product?');">
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
                    <td colspan="16">
                        <div class="empty-box">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No discounted products found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($discountedProducts->hasPages())
    <div class="p-3">
        {{ $discountedProducts->links() }}
    </div>
    @endif
</div>

@endsection
