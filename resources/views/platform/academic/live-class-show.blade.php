@extends('layouts.app')

@section('title', $event->title)
@section('page-title', $event->title)
@section('breadcrumb', 'Platform Admin / Academic / Live Class')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('platform.academic.live-classes') }}" class="text-sm text-slate-500 hover:text-slate-800">← Live classes</a>
        @if($institute?->is_active)
            <form method="POST" action="{{ route('platform.companies.enter-panel', $institute) }}">@csrf
                <button class="panel-btn-primary text-sm">Open institute panel</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Status" :value="ucfirst($event->status)" />
        <x-stat-card title="Starts" :value="$event->starts_at?->format('M d, H:i') ?? '—'" />
        <x-stat-card title="Institute" :value="$institute?->name ?? '—'" />
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Class details</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Course</dt><dd class="font-medium">{{ $event->course?->title ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Instructor</dt><dd class="font-medium">{{ $event->instructor?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Batch</dt><dd class="font-medium">{{ $event->batch?->title ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Platform</dt><dd class="font-medium">{{ $event->platform ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Starts</dt><dd class="font-medium">{{ $event->starts_at?->format('M d, Y H:i') }}</dd></div>
            <div><dt class="text-slate-500">Ends</dt><dd class="font-medium">{{ $event->ends_at?->format('M d, Y H:i') ?? '—' }}</dd></div>
            <div class="sm:col-span-2"><dt class="text-slate-500">Meeting URL</dt><dd class="font-medium break-all">{{ $event->meeting_url ?: '—' }}</dd></div>
        </dl>
        @if($event->description)
            <p class="text-sm text-slate-600 whitespace-pre-line">{{ $event->description }}</p>
        @endif
    </div>
</div>
@endsection
