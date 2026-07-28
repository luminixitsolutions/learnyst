@extends('layouts.app')

@section('title', 'Login Audit')
@section('page-title', 'Login Audit')
@section('breadcrumb', 'Platform Admin / Activity / Login Audit')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Success, failed, blocked, and social logins from activity + login history.</p>
        <a href="{{ route('platform.activity.index') }}" class="panel-btn-secondary text-sm">← Monitor</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Success today" :value="number_format($stats['success_today'])" />
        <x-stat-card title="Failed today" :value="number_format($stats['failed_today'])" />
        <x-stat-card title="Blocked today" :value="number_format($stats['blocked_today'])" />
        <x-stat-card title="Google today" :value="number_format($stats['google_today'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status (history)</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['success','failed','blocked','2fa_required'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Provider</label>
            <select name="provider" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['password','google','facebook','linkedin','apple','2fa'] as $p)
                    <option value="{{ $p }}" @selected(request('provider') === $p)>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">IP</label>
            <input type="text" name="ip" value="{{ request('ip') }}" class="panel-input w-full">
        </div>
        <div class="flex gap-2">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.activity.login-audit') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Auth activity events</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead><tr class="text-left">
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">IP</th>
                    </tr></thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $log->created_at->format('M d H:i') }}</td>
                                <td class="px-4 py-3"><x-badge type="info">{{ $log->action }}</x-badge></td>
                                <td class="px-4 py-3">{{ $log->user?->email ?? data_get($log->properties, 'email', '—') }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No auth activity.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t">{{ $logs->links() }}</div>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800">Login history table</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead><tr class="text-left">
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">IP</th>
                    </tr></thead>
                    <tbody>
                        @forelse($history as $row)
                            <tr>
                                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $row->created_at->format('M d H:i') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeType = match ($row->status) {
                                            'success' => 'success',
                                            'failed', 'blocked' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <x-badge :type="$badgeType">{{ $row->status }}</x-badge>
                                </td>
                                <td class="px-4 py-3">{{ $row->email ?? $row->user?->email ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $row->provider ?? '—' }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $row->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No login history rows.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t">{{ $history->links() }}</div>
        </div>
    </div>
</div>
@endsection
