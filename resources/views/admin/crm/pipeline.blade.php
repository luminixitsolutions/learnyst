@extends('layouts.app')

@section('title', 'Admission Pipeline')
@section('page-title', 'Pipeline')
@section('breadcrumb', 'CRM / Pipeline')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-slate-500">Drag is not required — open a lead to move stages. Stages: New → Contacted → Counseling → Documents → Admitted / Lost.</p>
    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach($stages as $key => $label)
        <div class="min-w-[260px] w-[260px] flex-shrink-0 glass-card rounded-2xl p-3">
            <div class="flex items-center justify-between mb-3 px-1">
                <h3 class="text-sm font-bold text-slate-800">{{ $label }}</h3>
                <span class="text-xs text-slate-500">{{ $grouped[$key]->count() }}</span>
            </div>
            <div class="space-y-2 max-h-[70vh] overflow-y-auto">
                @forelse($grouped[$key] as $lead)
                <a href="{{ route('admin.crm.leads.show', $lead) }}" class="block p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition">
                    <div class="text-sm font-semibold text-slate-800">{{ $lead->name }}</div>
                    <div class="text-xs text-slate-600 mt-1">{{ $lead->course?->title ?? 'No course' }}</div>
                    <div class="text-xs text-slate-500">{{ $lead->assignee?->name ?? 'Unassigned' }}</div>
                </a>
                @empty
                <p class="text-xs text-slate-400 px-1 py-4">Empty</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
