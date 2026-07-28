@extends('layouts.app')

@section('title', 'All Users')
@section('page-title', 'All Users')
@section('breadcrumb', 'Platform Admin / Users')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Global user directory across institutes and platform roles.</p>
        <a href="{{ route('platform.users.create') }}" class="panel-btn-primary text-sm">Create user</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Total users" :value="number_format($stats['total'])" />
        <x-stat-card title="Active" :value="number_format($stats['active'])" />
        <x-stat-card title="Inactive" :value="number_format($stats['inactive'])" />
        <x-stat-card title="Super admins" :value="number_format($stats['super_admins'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, email, phone…" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
            <select name="role" class="panel-input w-full">
                <option value="">All roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->slug }}" @selected(request('role') === $role->slug)>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute</label>
            <select name="institute" class="panel-input w-full">
                <option value="">All institutes</option>
                @foreach($institutes as $institute)
                    <option value="{{ $institute->id }}" @selected((string) request('institute') === (string) $institute->id)>{{ $institute->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Last login</label>
            <select name="last_login" class="panel-input w-full">
                <option value="">Any time</option>
                <option value="today" @selected(request('last_login') === 'today')>Today</option>
                <option value="7d" @selected(request('last_login') === '7d')>Last 7 days</option>
                <option value="30d" @selected(request('last_login') === '30d')>Last 30 days</option>
                <option value="never" @selected(request('last_login') === 'never')>Never</option>
            </select>
        </div>
        <div class="flex gap-2 xl:col-span-6">
            <button type="submit" class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.users.index') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($users->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Last login</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php
                            $instituteName = $user->company?->name
                                ?? $user->creator?->company?->name
                                ?? null;
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.users.show', $user) }}" class="block group">
                                    <div class="font-medium text-slate-800 group-hover:text-teal-700">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $user->email }}</div>
                                </a>
                            </td>
                            <td class="px-6 py-4 capitalize">{{ str_replace('-', ' ', $user->role?->slug ?? '—') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $instituteName ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <x-badge :type="$user->is_active ? 'success' : 'danger'">{{ $user->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.users.show', $user) }}" class="inline-flex rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                                    <a href="{{ route('platform.users.edit', $user) }}" class="inline-flex rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $users->links() }}</div>
        @else
            <x-empty-state title="No users match your filters" />
        @endif
    </div>
</div>
@endsection
