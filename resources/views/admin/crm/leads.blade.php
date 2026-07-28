@extends('layouts.app')

@section('title', 'CRM Leads')
@section('page-title', 'CRM Leads')
@section('breadcrumb', 'CRM / Leads')

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

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Lead</th>
                <th class="px-6 py-4">Stage</th>
                <th class="px-6 py-4">Source</th>
                <th class="px-6 py-4">Counselor</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-white font-medium">{{ $lead->name }}</div>
                        <div class="text-xs text-slate-500">{{ $lead->email }}</div>
                    </td>
                    <td class="px-6 py-4"><x-badge type="info">{{ $stages[$lead->stage] ?? ucfirst($lead->stage) }}</x-badge></td>
                    <td class="px-6 py-4 text-slate-400">{{ $lead->source ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $lead->assignee?->name ?? '—' }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.crm.leads.show', $lead) }}" class="text-emerald-400 text-sm">Open</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No leads.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $leads->links() }}</div>
    </div>
</div>
@endsection
