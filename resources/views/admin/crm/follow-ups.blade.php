@extends('layouts.app')

@section('title', 'Follow-ups')
@section('page-title', 'Follow-ups')
@section('breadcrumb', 'CRM / Follow-ups')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex gap-2">
        <a href="{{ route('admin.crm.follow-ups', ['scope'=>'today']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('scope')==='today' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">Today</a>
        <a href="{{ route('admin.crm.follow-ups', ['scope'=>'mine']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('scope')==='mine' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">Mine</a>
        <a href="{{ route('admin.crm.follow-ups') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !request('scope') ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600' }}">All pending</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($followUps->count())
        <div class="overflow-x-auto">
            <table id="followUpsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Task</th>
                        <th class="px-6 py-4">Lead</th>
                        <th class="px-6 py-4">Due</th>
                        <th class="px-6 py-4">Assignee</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($followUps as $fu)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $fu->title }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.crm.leads.show', $fu->lead) }}" class="text-emerald-600">{{ $fu->lead?->name }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $fu->due_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $fu->assignee?->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.crm.follow-ups.complete', $fu) }}">@csrf
                                <button class="text-sm text-emerald-600">Complete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No follow-ups." description="Pending follow-ups will appear here." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($followUps->count())
    <x-admin.datatable-scripts table-id="followUpsTable" entity="follow-ups" :order-column="2" order-direction="asc" :action-column="4" export-file-name="crm-follow-ups" />
@endif
@endpush
