@extends('layouts.app')

@section('title', 'CRM Leads')
@section('page-title', 'CRM Leads')
@section('breadcrumb', 'CRM / Leads')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <x-form-input label="Search" name="search" :value="request('search')" />
            <x-form-input label="Stage" name="stage" type="select" :value="request('stage')">
                <option value="">All stages</option>
                @foreach($stages as $key => $label)
                    <option value="{{ $key }}" @selected(request('stage')===$key)>{{ $label }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Counselor" name="assigned_to" type="select" :value="request('assigned_to')">
                <option value="">All</option>
                @foreach($counselors as $c)
                    <option value="{{ $c->id }}" @selected(request('assigned_to')==$c->id)>{{ $c->name }}</option>
                @endforeach
            </x-form-input>
            <div class="flex items-end"><button class="px-4 py-2.5 rounded-xl panel-btn-primary">Filter</button></div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($leads->count())
        <div class="overflow-x-auto">
            <table id="crmLeadsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Lead</th>
                        <th class="px-6 py-4">Stage</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Counselor</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <div class="text-slate-800 font-medium">{{ $lead->name }}</div>
                            <div class="text-xs text-slate-500">{{ $lead->email }}</div>
                        </td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $stages[$lead->stage] ?? ucfirst($lead->stage) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-600">{{ $lead->source ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $lead->assignee?->name ?? '—' }}</td>
                        <td class="px-6 py-4"><a href="{{ route('admin.crm.leads.show', $lead) }}" class="text-emerald-600 text-sm">Open</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No leads." description="Leads will appear here as they are captured." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($leads->count())
    <x-admin.datatable-scripts table-id="crmLeadsTable" entity="leads" :order-column="0" order-direction="desc" :action-column="4" export-file-name="crm-leads" />
@endif
@endpush
