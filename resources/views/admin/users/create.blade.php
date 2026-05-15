@extends('admin.layouts.app')

@section('page-title', 'Add New User')
@section('page-subtitle', 'Create admin user and assign one role')

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
        <h4>Add New User</h4>
        <p>Create a system user and assign one active role.</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control theme-control" required>

                    @error('name')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}"
                        class="form-control theme-control" required>

                    @error('username')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="form-control theme-control" required>

                    @error('email')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select theme-control" required>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>

                    @error('role')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control theme-control" required>

                    @error('password')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control theme-control" required>
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

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                        Cancel
                    </a>

                    <button type="submit" class="btn-save">
                        <i class="bi bi-save me-1"></i>
                        Save User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection