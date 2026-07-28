<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformRoleController extends Controller
{
    /** Roles whose permissions must not be edited from the platform UI. */
    protected array $lockedSlugs = ['super-admin', 'admin'];

    public function index()
    {
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        $permissionCount = Permission::count();

        return view('platform.roles.index', compact('roles', 'permissionCount'));
    }

    public function edit(Role $role)
    {
        $modules = PermissionService::modules();
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $locked = $this->isLocked($role);

        return view('platform.roles.edit', compact('role', 'modules', 'permissions', 'rolePermissions', 'locked'));
    }

    public function update(Request $request, Role $role)
    {
        if ($this->isLocked($role)) {
            return back()->with('error', 'This system role cannot be modified from the platform panel.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $payload = [
            'description' => $validated['description'] ?? null,
        ];

        if (! $role->is_system) {
            $payload['name'] = $validated['name'];
            $payload['slug'] = Str::slug($validated['name']);
        }

        $role->update($payload);
        $role->permissions()->sync($validated['permissions'] ?? []);

        ActivityLogger::log('role_permissions_updated', "Role {$role->name} updated from platform", $role);

        return redirect()
            ->route('platform.roles.edit', $role)
            ->with('success', 'Role updated.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $slug = Str::slug($validated['name']);
        if (Role::where('slug', $slug)->exists()) {
            return back()->with('error', 'A role with this name already exists.');
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        ActivityLogger::log('role_created', "Custom role created: {$role->name}", $role);

        return redirect()
            ->route('platform.roles.edit', $role)
            ->with('success', 'Role created. Assign permissions next.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete a role that still has users.');
        }

        $name = $role->name;
        $role->permissions()->detach();
        $role->delete();

        ActivityLogger::log('role_deleted', "Custom role deleted: {$name}");

        return redirect()
            ->route('platform.roles.index')
            ->with('success', 'Role deleted.');
    }

    public function seedPermissions()
    {
        $modules = PermissionService::modules();
        $actions = PermissionService::actions();
        $checked = 0;

        foreach ($modules as $module => $label) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['slug' => "{$module}.{$action}"], [
                    'name' => ucfirst($action).' '.$label,
                    'group' => $module,
                    'module' => $module,
                    'action' => $action,
                ]);
                $checked++;
            }
        }

        ActivityLogger::log('permissions_synced', "Platform permission catalog synced ({$checked} checked)");

        return back()->with('success', "Permissions synced ({$checked} checked).");
    }

    protected function isLocked(Role $role): bool
    {
        return in_array($role->slug, $this->lockedSlugs, true);
    }
}
