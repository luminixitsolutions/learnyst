@extends('layouts.app')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('breadcrumb', 'Platform Admin / Activity Logs')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search activity..."
            class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
        <button type="submit" class="panel-btn-primary text-sm">Search</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($logs->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Action</th>
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">IP</th>
                    <th class="px-6 py-4">Date</th>
                </tr></thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($log->action) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-800">{{ $log->description ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $log->user?->name ?? 'System' }}</td>
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $log->ip_address ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $logs->links() }}</div>
        @else
        <x-empty-state title="No activity logs found" />
        @endif
    </div>
</div>
@endsection
