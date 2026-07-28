<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function roles()
    {
        $roles = Role::withCount('users', 'permissions')->latest()->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['slug'] = str($validated['name'])->slug()->toString();
        $validated['is_system'] = false;

        Role::create($validated);

        return back()->with('success', 'Role created.');
    }

    public function editRole(Role $role)
    {
        $modules = PermissionService::modules();
        $actions = PermissionService::actions();
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'modules', 'actions', 'permissions', 'rolePermissions'));
    }

    public function updateRole(Request $request, Role $role)
    {
        if ($role->is_system && $role->slug === 'admin') {
            return back()->with('error', 'Cannot modify system admin role.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(collect($validated)->only(['name', 'description'])->toArray());
        $role->permissions()->sync($validated['permissions'] ?? []);

        ActivityLogger::log('role_updated', "Role {$role->name} permissions updated", $role);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroyRole(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Cannot delete system role.');
        }

        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    public function permissions()
    {
        $permissions = Permission::orderBy('module')->orderBy('action')->paginate(50);
        $modules = PermissionService::modules();

        return view('admin.permissions.index', compact('permissions', 'modules'));
    }

    public function seedPermissions()
    {
        $modules = PermissionService::modules();
        $actions = PermissionService::actions();
        $created = 0;

        foreach ($modules as $module => $label) {
            foreach ($actions as $action) {
                $slug = "{$module}.{$action}";
                Permission::firstOrCreate(['slug' => $slug], [
                    'name' => ucfirst($action) . ' ' . $label,
                    'group' => $module,
                    'module' => $module,
                    'action' => $action,
                ]);
                $created++;
            }
        }

        return back()->with('success', "Permissions synced ({$created} checked).");
    }
}
