<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $roles = Role::query()
            ->withCount('users')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalRoles = Role::count();
        $activeRoles = Role::where('status', 'active')->count();
        $inactiveRoles = Role::where('status', 'inactive')->count();

        return view('admin.roles.index', compact(
            'roles',
            'totalRoles',
            'activeRoles',
            'inactiveRoles',
            'search',
            'status'
        ));
    }

    public function create()
    {
        $permissionGroups = Config::get('admin_permissions', []);

        return view('admin.roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $availablePermissions = $this->availablePermissions();

        $validated = $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name',
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                Rule::in($availablePermissions),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $role = Role::create([
            'name' => $validated['role_name'],
            'guard_name' => 'web',
            'status' => $validated['status'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissionGroups = Config::get('admin_permissions', []);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact(
            'role',
            'permissionGroups',
            'rolePermissions'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $availablePermissions = $this->availablePermissions();

        $validated = $request->validate([
            'role_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                Rule::in($availablePermissions),
            ],
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $role->update([
            'name' => $validated['role_name'],
            'status' => $validated['status'],
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('admin.roles.index')
                ->with('error', 'This role cannot be deleted because users are assigned to it.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    private function availablePermissions(): array
    {
        $permissions = [];

        foreach (Config::get('admin_permissions', []) as $group) {
            foreach ($group['items'] as $item) {
                $permissions[] = $item['permission'];
            }
        }

        return $permissions;
    }
}
