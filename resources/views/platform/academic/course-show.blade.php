@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('breadcrumb', 'Platform Admin / Academic / Course')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('platform.academic.courses') }}" class="text-sm text-slate-500 hover:text-slate-800">← All courses</a>
        @if($institute?->is_active)
            <form method="POST" action="{{ route('platform.companies.enter-panel', $institute) }}">@csrf
                <button class="panel-btn-primary text-sm">Open institute panel</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <x-stat-card title="Status" :value="ucfirst($course->status)" />
        <x-stat-card title="Enrollments" :value="number_format($course->enrollments_count)" />
        <x-stat-card title="Active" :value="number_format($course->active_enrollments_count)" />
        <x-stat-card title="Revenue" :value="'₹'.number_format($revenue, 0)" />
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Course details</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Institute</dt><dd class="font-medium">{{ $institute?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Category</dt><dd class="font-medium">{{ $course->category?->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Owner</dt><dd class="font-medium">{{ $course->creator?->email ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Price</dt><dd class="font-medium">{{ $course->is_free ? 'Free' : '₹'.number_format((float) ($course->sale_price ?? $course->price), 2) }}</dd></div>
            <div><dt class="text-slate-500">Product type</dt><dd class="font-medium">{{ $course->product_type ?: '—' }}</dd></div>
            <div><dt class="text-slate-500">Created</dt><dd class="font-medium">{{ $course->created_at?->format('M d, Y H:i') }}</dd></div>
        </dl>
        @if($course->subtitle)
            <p class="text-sm text-slate-600">{{ $course->subtitle }}</p>
        @endif
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Recent enrollments</h3>
        @forelse($recentEnrollments as $enrollment)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 text-sm">
                <div>
                    <a href="{{ route('platform.academic.enrollments.show', $enrollment) }}" class="text-indigo-600 font-medium">{{ $enrollment->user?->name }}</a>
                    <div class="text-xs text-slate-400">{{ $enrollment->user?->email }} · {{ $enrollment->status }}</div>
                </div>
                <span class="text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') }}</span>
            </div>
        @empty
            <p class="text-sm text-slate-500">No enrollments</p>
        @endforelse
    </div>
</div>
@endsection
