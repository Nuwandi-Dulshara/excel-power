@extends('admin.layouts.app')

@section('page-title', 'Sub Products')
@section('page-subtitle', 'Manage product variants for cashier/POS sales')

@section('content')

<style>
.info-card,
.stat-card,
.filter-card,
.table-card {
    background: white;
    border: 1px solid #fde68a;
    border-radius: 22px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, .06)
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

.stat-card {
    background: linear-gradient(135deg, #b91c1c, #d4af37);
    color: white;
    padding: 24px;
    height: 100%
}

.stat-card {
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
    font-weight: 700;
    text-transform: none
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

.btn-reset,
.btn-back {
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

.status-badge,
.stock-badge {
    border-radius: 999px;
    padding: 6px 12px;
    font-size: .75rem;
    font-weight: 900
}

.status-active,
.stock-in {
    background: #dcfce7;
    color: #166534
}

.status-inactive,
.stock-out {
    background: #fee2e2;
    color: #991b1b
}

.stock-low {
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
    white-space: normal !important
}
</style>

@if(session('success'))
<div class="alert-success-theme mb-4">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
</div>
@endif

<div class="info-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <h5 class="info-title mb-0">Main Product Information</h5>

        <a href="{{ route('admin.products.index') }}" class="btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Products
        </a>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="info-label">Product Name</div>
            <div class="info-value">{{ $product->product_name }}</div>
        </div>

        <div class="col-md-3">
            <div class="info-label">Product Code</div>
            <div class="info-value">{{ $product->product_code ?? 'N/A' }}</div>
        </div>

        <div class="col-md-3">
            <div class="info-label">Parent Category</div>
            <div class="info-value">{{ $product->category->category_name ?? 'N/A' }}</div>
        </div>

        <div class="col-md-3">
            <div class="info-label">Sub Category</div>
            <div class="info-value">{{ $product->sub_category }}</div>
        </div>

        <div class="col-md-3">
            <div class="info-label">Unit</div>
            <div class="info-value">{{ $product->unit->unit_name ?? 'N/A' }}</div>
        </div>

        <div class="col-md-3">
            <div class="info-label">Supplier</div>
            <div class="info-value">{{ $product->supplier->supplier_name ?? 'N/A' }}</div>
        </div>

        <div class="col-md-3">
            <div class="info-label">Brand</div>
            <div class="info-value">{{ $product->brand ?? 'N/A' }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-boxes"></i></div>
            <div class="stat-title">Total Sub Products</div>
            <div class="stat-value">{{ $totalVariants }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-title">Active Sub Products</div>
            <div class="stat-value">{{ $activeVariants }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-title">Inactive Sub Products</div>
            <div class="stat-value">{{ $inactiveVariants }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-title">Low Stock Items</div>
            <div class="stat-value">{{ $lowStockItems }}</div>
        </div>
    </div>
</div>

<div class="filter-card mb-4">
    <div class="filter-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1">Sub Product List</h4>
            <p class="mb-0 opacity-75">Search and filter product variants.</p>
        </div>

        <a href="{{ route('admin.products.variants.create', $product->id) }}" class="btn-add">
            <i class="bi bi-plus-circle me-1"></i> Add Sub Product
        </a>
    </div>

    <div class="filter-body">
        <form method="GET" action="{{ route('admin.products.variants.index', $product->id) }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control theme-control"
                        placeholder="Variant name, barcode, SKU, size">
                </div>

                <div class="col-lg-3">
                    <label class="form-label">Stock Status</label>
                    <select name="stock_status" class="form-select theme-control">
                        <option value="">All</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock
                        </option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low
                            Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                            Out of Stock</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select theme-control">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn-filter w-100">
                        <i class="bi bi-search"></i>
                    </button>

                    <a href="{{ route('admin.products.variants.index', $product->id) }}" class="btn-reset">
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
                    <th>Sub Product Name</th>
                    <th>Size</th>
                    <th>Description</th>
                    <th>Barcode</th>
                    <th>SKU</th>
                    <th>Purchase Price</th>
                    <th>Purchase Qty</th>
                    <th>Cost Price</th>
                    <th>Selling Price</th>
                    <th>Our Price</th>
                    <th>Min Wholesale Qty</th>
                    <th>Wholesale Price</th>
                    <th>Opening Stock</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Stock Status</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($variants as $variant)
                @php
                if ($variant->current_stock <= 0) { $stockText='Out of Stock' ; $stockClass='stock-out' ; } elseif
                    (!is_null($variant->reorder_level) && $variant->current_stock <= $variant->reorder_level) {
                        $stockText = 'Low Stock';
                        $stockClass = 'stock-low';
                        } else {
                        $stockText = 'In Stock';
                        $stockClass = 'stock-in';
                        }
                        @endphp

                        <tr>
                            <td>{{ $variants->firstItem() + $loop->index }}</td>
                            <td>{{ $product->product_name }}</td>
                            <td class="fw-bold text-danger">{{ $variant->variant_name }}</td>
                            <td>{{ $variant->size }}</td>
                            <td class="description-cell">{{ $variant->description ? \Illuminate\Support\Str::limit($variant->description, 80) : 'N/A' }}</td>
                            <td>{{ $variant->barcode ?? 'N/A' }}</td>
                            <td>{{ $variant->sku ?? 'N/A' }}</td>
                            <td>Rs. {{ number_format($variant->purchase_price, 2) }}</td>
                            <td>{{ $variant->purchase_quantity }}</td>
                            <td>{{ is_null($variant->cost_price) ? 'N/A' : 'Rs. ' . number_format($variant->cost_price, 2) }}</td>
                            <td>Rs. {{ number_format($variant->selling_price, 2) }}</td>
                            <td>Rs. {{ number_format($variant->our_price, 2) }}</td>
                            <td>{{ $variant->minimum_wholesale_quantity ?? 'N/A' }}</td>
                            <td>{{ $variant->wholesale_price ? 'Rs. ' . number_format($variant->wholesale_price, 2) : 'N/A' }}
                            </td>
                            <td>{{ $variant->opening_stock }}</td>
                            <td>{{ $variant->current_stock }}</td>
                            <td>{{ $variant->reorder_level ?? 'N/A' }}</td>
                            <td><span class="stock-badge {{ $stockClass }}">{{ $stockText }}</span></td>
                            <td>{{ $variant->expiry_date ? $variant->expiry_date->format('Y-m-d') : 'N/A' }}</td>

                            <td>
                                @if($variant->status === 'active')
                                <span class="status-badge status-active">Active</span>
                                @else
                                <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.products.variants.edit', [$product->id, $variant->id]) }}"
                                    class="btn-action btn-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <form
                                    action="{{ route('admin.products.variants.destroy', [$product->id, $variant->id]) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this sub product?');">
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
                            <td colspan="21">
                                <div class="empty-box">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No sub products found.
                                </div>
                            </td>
                        </tr>
                        @endforelse
            </tbody>
        </table>
    </div>

    @if($variants->hasPages())
    <div class="p-3">
        {{ $variants->links() }}
    </div>
    @endif
</div>

@endsection
