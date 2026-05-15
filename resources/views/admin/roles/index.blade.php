@extends('admin.layouts.app')

@section('page-title', 'Role Management')
@section('page-subtitle', 'Create and manage admin roles and sidebar permissions')

@section('content')

<style>
.unit-total-card {
    background: white;
    border-radius: 22px;
    border: 1px solid #fde68a;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    padding: 24px;
}

.unit-total-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #b91c1c, #d4af37);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
    margin-bottom: 16px;
}

.unit-total-label {
    color: #7c2d12;
    font-size: 0.85rem;
    font-weight: 800;
}

.unit-total-value {
    color: #450a0a;
    font-weight: 900;
    margin: 0;
}

.unit-toolbar-card {
    background: white;
    border-radius: 22px;
    border: 1px solid #fde68a;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    padding: 24px;
}

.btn-theme {
    background: linear-gradient(135deg, #b91c1c, #7f1d1d);
    color: white;
    border: none;
    border-radius: 14px;
    padding: 11px 18px;
    font-weight: 850;
    text-decoration: none;
}

.btn-theme:hover {
    color: white;
    transform: translateY(-1px);
}

.theme-control {
    border-radius: 14px;
    border: 1px solid #fde68a;
    padding: 11px 14px;
    font-weight: 600;
    color: #7f1d1d;
    background: #fffbeb;
}

.theme-table-card {
    background: white;
    border-radius: 22px;
    border: 1px solid #fde68a;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    overflow: hidden;
}

.table thead th {
    background: #fff7ed;
    color: #7f1d1d;
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: .4px;
    border-bottom: 1px solid #fde68a;
}

.table td {
    vertical-align: middle;
    color: #450a0a;
    font-weight: 600;
}

.status-badge {
    border-radius: 999px;
    padding: 7px 12px;
    font-weight: 800;
    font-size: 0.75rem;
}

.status-active {
    background: #dcfce7;
    color: #166534;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 11px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.btn-edit {
    background: #fef3c7;
    color: #92400e;
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
}
</style>

@if(session('success'))
<div class="alert alert-success border-0 rounded-4 fw-bold">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger border-0 rounded-4 fw-bold">
    {{ session('error') }}
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="unit-total-label">Total Roles</div>
            <h2 class="unit-total-value">{{ $totalRoles }}</h2>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="unit-total-label">Active Roles</div>
            <h2 class="unit-total-value">{{ $activeRoles }}</h2>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="unit-total-card">
            <div class="unit-total-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="unit-total-label">Inactive Roles</div>
            <h2 class="unit-total-value">{{ $inactiveRoles }}</h2>
        </div>
    </div>
</div>

<div class="unit-toolbar-card mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">Role List</h4>
            <p class="text-muted mb-0">Search, filter, edit, and manage role permissions.</p>
        </div>

        <a href="{{ route('admin.roles.create') }}" class="btn-theme align-self-start">
            <i class="bi bi-plus-circle me-1"></i>
            Add New Role
        </a>
    </div>

    <form method="GET" action="{{ route('admin.roles.index') }}">
        <div class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" value="{{ $search }}"
                    class="form-control theme-control" placeholder="Search role name...">
            </div>

            <div class="col-md-4">
                <select name="status" class="form-select theme-control">
                    <option value="">All Status</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn-theme w-100">
                    <i class="bi bi-search me-1"></i> Filter
                </button>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-4 fw-bold">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<div class="theme-table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>{{ $roles->firstItem() + $loop->index }}</td>
                    <td class="fw-bold">{{ $role->name }}</td>
                    <td>{{ $role->permissions()->count() }}</td>
                    <td>{{ $role->users_count }}</td>
                    <td>
                        <span class="status-badge {{ $role->status === 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ ucfirst($role->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="action-btn btn-edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this role?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn btn-delete">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 fw-bold text-muted">
                        No roles found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">
        {{ $roles->links() }}
    </div>
</div>

@endsection