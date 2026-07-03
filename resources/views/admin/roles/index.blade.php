@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles & Permissions')
@section('breadcrumb', 'Manage user roles')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Create Role</h3>
        <form method="POST" action="{{ route('admin.roles.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            @csrf
            <x-form-input label="Role Name" name="name" :value="old('name')" required />
            <x-form-input label="Description" name="description" :value="old('description')" />
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Add Role</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($roles->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Role</th>
                        <th class="px-6 py-4 font-medium">Users</th>
                        <th class="px-6 py-4 font-medium">Permissions</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="text-slate-800 font-semibold">{{ $role->name }}</p>
                            @if($role->description)<p class="text-xs text-slate-500">{{ Str::limit($role->description, 50) }}</p>@endif
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $role->users_count }}</td>
                        <td class="px-6 py-4 text-slate-300">{{ $role->permissions_count }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$role->is_system ? 'info' : 'default'">{{ $role->is_system ? 'System' : 'Custom' }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800 text-sm mr-3">Edit Permissions</a>
                            @unless($role->is_system)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                            @endunless
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $roles->links() }}</div>
        @else
        <x-empty-state title="No roles found" />
        @endif
    </div>
</div>
@endsection
