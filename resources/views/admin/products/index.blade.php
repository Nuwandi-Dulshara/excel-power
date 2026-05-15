@extends('admin.layouts.app')

@section('page-title', 'Products')
@section('page-subtitle', 'Manage main products and their sub products')

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

.product-img {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid #fde68a;
    background: #fff7ed
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

.btn-variant {
    background: #dcfce7;
    color: #166534
}

.alert-success-theme {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 14px 18px;
    font-weight: 750
}

.alert-error-theme {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
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

@if(session('error'))
<div class="alert-error-theme mb-4">
    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-title">Total Products</div>
            <div class="stat-value">{{ $totalProducts }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-title">Active Products</div>
            <div class="stat-value">{{ $activeProducts }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-title">Inactive Products</div>
            <div class="stat-value">{{ $inactiveProducts }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-boxes"></i></div>
            <div class="stat-title">Total Sub Products</div>
            <div class="stat-value">{{ $totalVariants }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-check2-square"></i></div>
            <div class="stat-title">Active Sub Products</div>
            <div class="stat-value">{{ $activeVariants }}</div>
        </div>
    </div>

    <div class="col-md-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-slash-circle"></i></div>
            <div class="stat-title">Inactive Sub Products</div>
            <div class="stat-value">{{ $inactiveVariants }}</div>
        </div>
    </div>
</div>

<div class="filter-card mb-4">
    <div class="filter-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="fw-bold mb-1">Product List</h4>
            <p class="mb-0 opacity-75">Search, filter, edit, delete, and manage sub products.</p>
        </div>

        <a href="{{ route('admin.products.create') }}" class="btn-add">
            <i class="bi bi-plus-circle me-1"></i> Add Product
        </a>
    </div>

    <div class="filter-body">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control theme-control"
                        placeholder="Name, code, brand">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Parent Category</label>
                    <select name="category_id" class="form-select theme-control">
                        <option value="">All</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Sub Category</label>
                    <input type="text" name="sub_category" value="{{ request('sub_category') }}"
                        class="form-control theme-control" placeholder="Sub category">
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Unit</label>
                    <select name="product_unit_id" class="form-select theme-control">
                        <option value="">All</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('product_unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->unit_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select theme-control">
                        <option value="">All</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-1">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select theme-control">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                </div>

                <div class="col-lg-12 d-flex gap-2">
                    <button type="submit" class="btn-filter">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>

                    <a href="{{ route('admin.products.index') }}" class="btn-reset">
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
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Parent Category</th>
                    <th>Sub Category</th>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Supplier</th>
                    <th>Brand</th>
                    <th>Sub Products</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>{{ $products->firstItem() + $loop->index }}</td>

                    <td>
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="product-img" alt="Product">
                        @else
                        <div class="product-img d-flex align-items-center justify-content-center">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                        @endif
                    </td>

                    <td class="fw-bold text-danger">{{ $product->product_name }}</td>
                    <td>{{ $product->product_code ?? 'N/A' }}</td>
                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                    <td>{{ $product->sub_category }}</td>
                    <td class="description-cell">{{ $product->description ? \Illuminate\Support\Str::limit($product->description, 80) : 'N/A' }}</td>
                    <td>{{ $product->unit->short_code ?? 'N/A' }}</td>
                    <td>{{ $product->supplier->supplier_name ?? 'N/A' }}</td>
                    <td>{{ $product->brand ?? 'N/A' }}</td>
                    <td>
                        <span class="badge rounded-pill text-bg-primary">
                            {{ $product->variants_count }}
                        </span>
                    </td>

                    <td>
                        @if($product->status === 'active')
                        <span class="status-badge status-active">Active</span>
                        @else
                        <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a href="{{ route('admin.products.variants.index', $product->id) }}"
                            class="btn-action btn-variant" title="Manage Sub Products">
                            <i class="bi bi-boxes"></i>
                        </a>

                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn-action btn-edit"
                            title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this product?');">
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
                    <td colspan="13">
                        <div class="empty-box">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No products found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
    <div class="p-3">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection
