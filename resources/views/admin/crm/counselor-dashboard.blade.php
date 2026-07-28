@extends('layouts.app')

@section('title', 'Counselor Dashboard')
@section('page-title', 'Counselor Dashboard')
@section('breadcrumb', 'CRM / Counselor')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Assigned leads', $stats['assigned']],
            ["Today's follow-ups", $stats['today_followups']],
            ['Admitted this month', $stats['converted_month']],
            ['Calls this week', $stats['calls_week']],
        ] as [$label, $value])
        <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-slate-400">{{ $label }}</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-4">Today's follow-ups</h3>
            @forelse($todayFollowUps as $fu)
            <div class="py-2 border-b border-slate-700/40 flex justify-between">
                <div>
                    <div class="text-sm text-white">{{ $fu->title }}</div>
                    <a href="{{ route('admin.crm.leads.show', $fu->lead) }}" class="text-xs text-emerald-400">{{ $fu->lead?->name }}</a>
                </div>
                <span class="text-xs text-slate-500">{{ $fu->due_at?->format('H:i') }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500">Nothing due today.</p>
            @endforelse
        </div>
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-4">Recent assigned leads</h3>
            @forelse($myLeads as $lead)
            <a href="{{ route('admin.crm.leads.show', $lead) }}" class="block py-2 border-b border-slate-700/40">
                <div class="text-sm text-white">{{ $lead->name }}</div>
                <div class="text-xs text-slate-500">{{ ucfirst($lead->stage) }} · {{ $lead->course?->title ?? '—' }}</div>
            </a>
            @empty
            <p class="text-sm text-slate-500">No assigned leads.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
