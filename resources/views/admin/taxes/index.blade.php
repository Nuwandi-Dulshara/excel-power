@extends('admin.layouts.app')

@section('page-title', 'Tax Settings')
@section('page-subtitle', 'Manage POS tax rates for sales and invoices')

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
    background: rgba(255, 255, 255, 0.18);
    right: -60px;
    top: -60px;
    border-radius: 50%
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
    color: #212529;
    padding: 24px 24px 0
}

.filter-header p {
    color: #6c757d
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

.btn-add:hover,
.btn-filter:hover {
    color: white;
    transform: translateY(-1px)
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

.btn-reset:hover {
    background: #fde68a;
    color: #450a0a
}

.table {
    margin-bottom: 0
}

.table thead th {
    background: #fff7ed;
    color: #7f1d1d;
    font-size: .76rem;
    text-transform: uppercase;
    font-weight: 900;
    border-bottom: 1px solid #fde68a;
    padding: 15px;
    white-space: nowrap
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
    color: #431407;
    font-weight: 600;
    border-bottom: 1px solid #fef3c7
}

.status-badge,
.type-badge {
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

.type-inclusive {
    background: #dbeafe;
    color: #1e40af
}

.type-exclusive {
    background: #fef3c7;
    color: #92400e
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

.description-cell {
    min-width: 220px;
    max-width: 320px;
    white-space: normal
}
</style>

@if(session('success'))
<div class="alert-success-theme mb-4">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <div class="stat-title">Total Taxes</div>
            <div class="stat-value">{{ $totalTaxes }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-title">Active Taxes</div>
            <div class="stat-value">{{ $activeTaxes }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-title">Inactive Taxes</div>
            <div class="stat-value">{{ $inactiveTaxes }}</div>
        </div>
    </div>
</div>

<div class="filter-card mb-4">
    <div class="filter-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1">Tax List</h4>
            <p class="mb-0 opacity-75">Search, filter, create, edit, and delete tax settings.</p>
        </div>

        <a href="{{ route('admin.taxes.create') }}" class="btn-add">
            <i class="bi bi-plus-circle me-1"></i> Add Tax
        </a>
    </div>

    <div class="filter-body">
        <form method="GET" action="{{ route('admin.taxes.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control theme-control" placeholder="Search by tax name">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select theme-control">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn-filter">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>

                    <a href="{{ route('admin.taxes.index') }}" class="btn-reset">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
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
                    <th>Tax Name</th>
                    <th>Tax Rate</th>
                    <th>Tax Type</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Created Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($taxes as $tax)
                <tr>
                    <td>{{ $taxes->firstItem() + $loop->index }}</td>
                    <td class="fw-bold text-danger">{{ $tax->tax_name }}</td>
                    <td>{{ number_format($tax->tax_rate, 2) }}%</td>

                    <td>
                        @if($tax->tax_type === 'inclusive')
                            <span class="type-badge type-inclusive">Inclusive</span>
                        @else
                            <span class="type-badge type-exclusive">Exclusive</span>
                        @endif
                    </td>

                    <td>
                        @if($tax->status === 'active')
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>

                    <td class="description-cell">
                        {{ $tax->description ? \Illuminate\Support\Str::limit($tax->description, 80) : 'N/A' }}
                    </td>

                    <td>{{ $tax->created_at ? $tax->created_at->format('Y-m-d') : 'N/A' }}</td>

                    <td class="text-end">
                        <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="btn-action btn-edit" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('admin.taxes.destroy', $tax->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this tax?');">
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
                    <td colspan="8">
                        <div class="empty-box">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No taxes found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($taxes->hasPages())
    <div class="p-3">
        {{ $taxes->links() }}
    </div>
    @endif
</div>

@endsection