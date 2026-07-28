@extends('layouts.app')

@section('title', 'Enrollment #'.$enrollment->id)
@section('page-title', 'Enrollment #'.$enrollment->id)
@section('breadcrumb', 'Platform Admin / Academic / Enrollment')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('platform.academic.enrollments') }}" class="text-sm text-slate-500 hover:text-slate-800">← All enrollments</a>
        @if($institute?->is_active)
            <form method="POST" action="{{ route('platform.companies.enter-panel', $institute) }}">@csrf
                <button class="panel-btn-primary text-sm">Open institute panel</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Status" :value="ucfirst($enrollment->status)" />
        <x-stat-card title="Progress" :value="number_format((float) $enrollment->progress, 0).'%'" />
        <x-stat-card title="Institute" :value="$institute?->name ?? '—'" />
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Enrollment details</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Student</dt><dd class="font-medium">{{ $enrollment->user?->name }} · {{ $enrollment->user?->email }}</dd></div>
            <div>
                <dt class="text-slate-500">Course</dt>
                <dd class="font-medium">
                    @if($enrollment->course)
                        <a href="{{ route('platform.academic.courses.show', $enrollment->course) }}" class="text-indigo-600 hover:underline">{{ $enrollment->course->title }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div><dt class="text-slate-500">Type</dt><dd class="font-medium">{{ $enrollment->enrollment_type }}</dd></div>
            <div><dt class="text-slate-500">Amount</dt><dd class="font-medium">₹{{ number_format((float) $enrollment->amount, 2) }}</dd></div>
            <div><dt class="text-slate-500">Enrolled at</dt><dd class="font-medium">{{ $enrollment->enrolled_at?->format('M d, Y H:i') ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Expires</dt><dd class="font-medium">{{ $enrollment->expires_at?->format('M d, Y') ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Batch</dt><dd class="font-medium">{{ $enrollment->batch?->title ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Order</dt><dd class="font-medium">{{ $enrollment->order?->order_number ?? '—' }}</dd></div>
        </dl>
    </div>
</div>
@endsection
