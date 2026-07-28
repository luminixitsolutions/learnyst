@extends('layouts.app')

@section('title', 'Call Logs')
@section('page-title', 'Call Logs')
@section('breadcrumb', 'CRM / Call Logs')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
    @if($logs->count())
    <div class="overflow-x-auto">
        <table id="callLogsTable" class="w-full text-sm panel-table display" style="width:100%">
            <thead>
                <tr class="text-left">
                    <th class="px-6 py-4">When</th>
                    <th class="px-6 py-4">Lead</th>
                    <th class="px-6 py-4">Counselor</th>
                    <th class="px-6 py-4">Direction</th>
                    <th class="px-6 py-4">Outcome</th>
                    <th class="px-6 py-4">Duration</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr class="hover:bg-indigo-50/40">
                    <td class="px-6 py-4 text-slate-500">{{ $log->called_at?->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.crm.leads.show', $log->lead) }}" class="text-emerald-600">{{ $log->lead?->name }}</a></td>
                    <td class="px-6 py-4 text-slate-600">{{ $log->user?->name }}</td>
                    <td class="px-6 py-4 text-slate-600 capitalize">{{ $log->direction }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ str_replace('_',' ',$log->outcome) }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $log->duration_seconds }}s</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <x-empty-state title="No call logs." description="Call activity will appear here once logged." />
    @endif
</div>
@endsection

@push('scripts')
@if($logs->count())
    <x-admin.datatable-scripts table-id="callLogsTable" entity="call logs" :order-column="0" order-direction="desc" export-file-name="crm-call-logs" />
@endif
@endpush
