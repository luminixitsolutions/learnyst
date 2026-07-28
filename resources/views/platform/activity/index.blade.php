@extends('layouts.app')

@section('title', 'Activity Monitor')
@section('page-title', 'Activity Monitor')
@section('breadcrumb', 'Platform Admin / Activity / Monitor')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Platform-wide audit trail with filters and CSV export.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('platform.activity.login-audit') }}" class="panel-btn-secondary text-sm">Login Audit</a>
            <a href="{{ route('platform.activity.live-sessions') }}" class="panel-btn-secondary text-sm">Live Sessions</a>
            <a href="{{ route('platform.activity.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Total events" :value="number_format($stats['total'])" />
        <x-stat-card title="Today" :value="number_format($stats['today'])" />
        <x-stat-card title="Failed logins today" :value="number_format($stats['failed_logins_today'])" />
        <x-stat-card title="Unique IPs today" :value="number_format($stats['unique_ips_today'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">User</label>
            <select name="user_id" class="panel-input w-full">
                <option value="">All users</option>
                @foreach($filters['users'] as $u)
                    <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
            <select name="role" class="panel-input w-full">
                <option value="">All roles</option>
                @foreach($filters['roles'] as $role)
                    <option value="{{ $role->slug }}" @selected(request('role') === $role->slug)>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute</label>
            <select name="company_id" class="panel-input w-full">
                <option value="">All institutes</option>
                @foreach($filters['companies'] as $company)
                    <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Action</label>
            <select name="action" class="panel-input w-full">
                <option value="">All actions</option>
                @foreach($filters['actions'] as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Subject type</label>
            <select name="subject_type" class="panel-input w-full">
                <option value="">All subjects</option>
                @foreach($filters['subject_types'] as $label => $type)
                    <option value="{{ $label }}" @selected(request('subject_type') === $label || request('subject_type') === $type)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">IP</label>
            <input type="text" name="ip" value="{{ request('ip') }}" placeholder="203.0.113…" class="panel-input w-full">
        </div>
        <div class="xl:col-span-3">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Action or description…" class="panel-input w-full">
        </div>
        <div class="flex gap-2">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.activity.index') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($logs->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">When</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">IP</th>
                        <th class="px-6 py-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4"><x-badge type="info">{{ $log->action }}</x-badge></td>
                            <td class="px-6 py-4 text-slate-800 max-w-xs truncate">{{ $log->description ?? '—' }}</td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <a href="{{ route('platform.users.show', $log->user) }}" class="text-indigo-600 hover:underline">{{ $log->user->name }}</a>
                                    <div class="text-xs text-slate-400 capitalize">{{ str_replace('-', ' ', $log->user->role?->slug ?? '') }}</div>
                                @else
                                    System
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                @if($log->company)
                                    <a href="{{ route('platform.companies.show', $log->company) }}" class="hover:underline">{{ $log->company->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('platform.activity.show', $log) }}" class="text-xs font-semibold text-indigo-600">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $logs->links() }}</div>
        @else
            <x-empty-state title="No activity matches your filters" />
        @endif
    </div>
</div>
@endsection
