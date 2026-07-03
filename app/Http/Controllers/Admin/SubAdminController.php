<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SubAdminController extends Controller
{
    public function index(Request $request)
    {
        $subAdminRole = Role::where('slug', 'sub-admin')->first();

        $query = User::where('role_id', $subAdminRole?->id)->with('role')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $subAdmins = $query->paginate(15)->withQueryString();

        return view('admin.sub-admins.index', compact('subAdmins'));
    }

    public function show(User $subAdmin)
    {
        $subAdmin->load(['role.permissions', 'permissions', 'subAdminScopes']);

        return view('admin.sub-admins.show', compact('subAdmin'));
    }

    public function edit(User $subAdmin)
    {
        $roles = Role::where('slug', 'like', 'sub-admin%')->orWhere('is_system', false)->get();

        return view('admin.sub-admins.edit', compact('subAdmin', 'roles'));
    }

    public function update(Request $request, User $subAdmin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $subAdmin->id],
            'phone' => ['nullable', 'string'],
            'password' => ['nullable', Password::defaults()],
            'is_active' => ['boolean'],
            'social_links' => ['nullable', 'array'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['social_links'] = $request->input('social_links', []);

        $subAdmin->update($validated);
        ActivityLogger::log('sub_admin_updated', "Sub-admin {$subAdmin->name} updated", $subAdmin);

        return redirect()->route('admin.sub-admins.show', $subAdmin)->with('success', 'Sub-admin updated.');
    }

    public function destroy(User $subAdmin)
    {
        $subAdmin->delete();

        return redirect()->route('admin.sub-admins.index')->with('success', 'Sub-admin deleted.');
    }

    public function toggleStatus(User $subAdmin)
    {
        $subAdmin->update(['is_active' => !$subAdmin->is_active]);

        return back()->with('success', 'Status updated.');
    }
}
