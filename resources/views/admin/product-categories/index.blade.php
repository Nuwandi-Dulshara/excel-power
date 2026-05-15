@extends('admin.layouts.app')

@section('page-title', 'Parent Categories')
@section('page-subtitle', 'Manage top-level product categories')

@section('content')

<style>
.unit-toolbar-card {
    background: #ffffff;
    border: 1px solid #fde68a;
    border-radius: 22px;
    padding: 24px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
}

.unit-total-card {
    background: linear-gradient(135deg, #b91c1c, #d4af37);
    color: white;
    border-radius: 22px;
    padding: 24px;
    box-shadow: 0 18px 35px rgba(185, 28, 28, 0.25);
    position: relative;
    overflow: hidden;
}

.unit-total-card::after {
    content: "";
    position: absolute;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.18);
    right: -60px;
    top: -60px;
    border-radius: 50%;
}

.unit-total-icon {
    width: 54px;
    height: 54px;
    background: rgba(255, 255, 255, 0.18);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    margin-bottom: 16px;
}

.unit-total-label {
    font-size: 0.9rem;
    opacity: 0.85;
    font-weight: 700;
}

.unit-total-value {
    font-size: 2.2rem;
    font-weight: 900;
    margin: 0;
}

.btn-theme {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 13px;
    padding: 11px 18px;
    font-weight: 800;
    box-shadow: 0 10px 22px rgba(185, 28, 28, 0.25);
    text-decoration: none;
}

.btn-theme:hover {
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 14px 26px rgba(185, 28, 28, 0.32);
}

.filter-control {
    border-radius: 13px;
    border: 1px solid #fde68a;
    padding: 11px 14px;
    font-weight: 600;
    color: #7f1d1d;
}

.filter-control:focus {
    border-color: #b91c1c;
    box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.1);
}

.unit-table-card {
    background: white;
    border-radius: 22px;
    border: 1px solid #fde68a;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table thead th {
    background: #fffbeb;
    color: #7f1d1d;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    font-weight: 900;
    border-bottom: 1px solid #fde68a;
    padding: 16px;
}

.table tbody td {
    padding: 16px;
    vertical-align: middle;
    color: #7f1d1d;
    font-weight: 600;
    border-color: #fff7ed;
}

.badge-active {
    background: #dcfce7;
    color: #166534;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.76rem;
}

.badge-inactive {
    background: #fee2e2;
    color: #991b1b;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.76rem;
}

.action-btn {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: 0.25s ease;
    margin-right: 5px;
}

.edit-btn {
    background: rgba(185, 28, 28, 0.12);
    color: #b91c1c;
}

.edit-btn:hover {
    background: #b91c1c;
    color: white;
}

.delete-btn {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
}

.delete-btn:hover {
    background: #ef4444;
    color: white;
}

.theme-code {
    color: #b91c1c;
}

.empty-box {
    padding: 50px 20px;
    text-align: center;
    color: #7c2d12;
}

.alert-success-theme {
    border: none;
    border-radius: 16px;
    background: #dcfce7;
    color: #166534;
    font-weight: 700;
    padding: 14px 18px;
}
</style>

@if (session('success'))
<div class="alert alert-success-theme mb-4">
    <i class="bi bi-check-circle-fill me-2"></i>
    {{ session('success') }}
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon">
                <i class="bi bi-tags-fill"></i>
            </div>
            <div class="unit-total-label">Total Parent Categories</div>
            <h2 class="unit-total-value">{{ $totalCategories }}</h2>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="unit-total-label">Active Categories</div>
            <h2 class="unit-total-value">{{ $activeCategories }}</h2>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="unit-total-label">Inactive Categories</div>
            <h2 class="unit-total-value">{{ $inactiveCategories }}</h2>
        </div>
    </div>
</div>

<div class="unit-toolbar-card mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Parent Category List</h4>
            <p class="text-muted mb-0">Search, filter, edit, and manage parent categories.</p>
        </div>

        <a href="{{ route('admin.product-categories.create') }}" class="btn-theme align-self-start">
            <i class="bi bi-plus-circle me-1"></i>
            Add Parent Category
        </a>
    </div>

    <form method="GET" action="{{ route('admin.product-categories.index') }}">
        <div class="row g-3">
            <div class="col-md-7">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control filter-control"
                    placeholder="Search parent category name, code, or description">
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select filter-control">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn-theme w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="unit-table-card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Parent Category Name</th>
                    <th>Description</th>
                    <th>Parent Category Code</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($categories as $category)
                <tr>
                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                    <td>{{ $category->category_name }}</td>
                    <td>{{ $category->description ?? 'No description' }}</td>
                    <td>
                        <span class="fw-bold theme-code">{{ $category->category_code }}</span>
                    </td>
                    <td>
                        @if ($category->status == 'active')
                        <span class="badge-active">Active</span>
                        @else
                        <span class="badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.product-categories.edit', $category->id) }}"
                            class="action-btn edit-btn" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('admin.product-categories.destroy', $category->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this parent category?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="action-btn delete-btn" title="Delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-box">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No parent categories found.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($categories->hasPages())
    <div class="p-3 border-top">
        {{ $categories->links() }}
    </div>
    @endif
</div>

@endsection
