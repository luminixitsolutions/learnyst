@extends('layouts.app')

@section('title', 'Roles & Permissions')
@section('page-title', 'Roles & Permissions')
@section('breadcrumb', 'Platform Admin / System / Roles')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">{{ $roles->count() }} roles · {{ number_format($permissionCount) }} permissions. System roles are protected.</p>
        <form method="POST" action="{{ route('platform.roles.seed-permissions') }}">@csrf
            <button class="panel-btn-secondary text-sm">Sync permission catalog</button>
        </form>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('platform.roles.store') }}" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">New custom role</label>
            <input type="text" name="name" required class="panel-input w-full" placeholder="Role name">
        </div>
        <div class="flex-[2] min-w-[240px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">Description</label>
            <input type="text" name="description" class="panel-input w-full" placeholder="Optional">
        </div>
        <button class="panel-btn-primary text-sm">Create</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Users</th>
                        <th class="px-6 py-4">Permissions</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $role->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $role->slug }}</div>
                            </td>
                            <td class="px-6 py-4">{{ number_format($role->users_count) }}</td>
                            <td class="px-6 py-4">{{ number_format($role->permissions_count) }}</td>
                            <td class="px-6 py-4">
                                <x-badge :type="$role->is_system ? 'info' : 'success'">{{ $role->is_system ? 'System' : 'Custom' }}</x-badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.roles.edit', $role) }}" class="text-xs font-semibold text-indigo-600">Manage</a>
                                    @unless($role->is_system)
                                        <form method="POST" action="{{ route('platform.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs font-semibold text-rose-600">Delete</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
