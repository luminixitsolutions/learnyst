@extends('layouts.app')

@section('title', 'Call Logs')
@section('page-title', 'Call Logs')
@section('breadcrumb', 'CRM / Call Logs')

@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm panel-table">
        <thead><tr class="text-left">
            <th class="px-6 py-4">When</th>
            <th class="px-6 py-4">Lead</th>
            <th class="px-6 py-4">Counselor</th>
            <th class="px-6 py-4">Direction</th>
            <th class="px-6 py-4">Outcome</th>
            <th class="px-6 py-4">Duration</th>
        </tr></thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="px-6 py-4 text-slate-400">{{ $log->called_at?->format('M d, Y H:i') }}</td>
                <td class="px-6 py-4"><a href="{{ route('admin.crm.leads.show', $log->lead) }}" class="text-emerald-400">{{ $log->lead?->name }}</a></td>
                <td class="px-6 py-4 text-slate-400">{{ $log->user?->name }}</td>
                <td class="px-6 py-4 text-slate-400 capitalize">{{ $log->direction }}</td>
                <td class="px-6 py-4 text-slate-400">{{ str_replace('_',' ',$log->outcome) }}</td>
                <td class="px-6 py-4 text-slate-400">{{ $log->duration_seconds }}s</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No call logs.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $logs->links() }}</div>
</div>
@endsection
