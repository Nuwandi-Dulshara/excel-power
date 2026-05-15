@extends('admin.layouts.app')

@section('page-title', 'Add New Role')
@section('page-subtitle', 'Create a role and assign sidebar permissions')

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

.permission-box {
    border: 1px solid #fde68a;
    background: #fffbeb;
    border-radius: 18px;
    padding: 18px;
}

.permission-title {
    color: #7f1d1d;
    font-weight: 900;
    margin-bottom: 12px;
}

.form-check-input:checked {
    background-color: #b91c1c;
    border-color: #b91c1c;
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
        <h4>Add New Role</h4>
        <p>Select sidebar permissions for this role.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="role_name" value="{{ old('role_name') }}"
                        class="form-control theme-control" placeholder="Example: manager" required>

                    @error('role_name')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select theme-control" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    @error('status')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Role Permissions</label>

                    @error('permissions')
                    <div class="error-msg mb-2">{{ $message }}</div>
                    @enderror

                    <div class="row g-3">
                        @foreach($permissionGroups as $group)
                            <div class="col-lg-6">
                                <div class="permission-box">
                                    <div class="permission-title">
                                        <i class="bi bi-folder2-open me-1"></i>
                                        {{ $group['group'] }}
                                    </div>

                                    @foreach($group['items'] as $item)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="permissions[]"
                                                value="{{ $item['permission'] }}"
                                                id="permission_{{ $item['permission'] }}"
                                                {{ in_array($item['permission'], old('permissions', [])) ? 'checked' : '' }}>

                                            <label class="form-check-label fw-bold text-dark"
                                                for="permission_{{ $item['permission'] }}">
                                                <i class="{{ $item['icon'] }} me-1 text-warning"></i>
                                                {{ $item['label'] }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
                        Cancel
                    </a>

                    <button type="submit" class="btn-save">
                        <i class="bi bi-save me-1"></i>
                        Save Role
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection