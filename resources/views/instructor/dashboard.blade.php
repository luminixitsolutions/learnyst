@extends('layouts.app')

@section('title', 'Instructor Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Instructor Overview')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="My Courses" :value="number_format($courses->count())" />
        <x-stat-card title="Active Batches" :value="number_format($batches->count())" />
        <x-stat-card title="Pending Tasks" :value="number_format($tasks->count())" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">My Courses</h3>
            @forelse($courses as $course)
                <div class="flex items-center justify-between py-3 border-b border-slate-200 last:border-0">
                    <p class="text-sm text-white font-medium">{{ $course->title }}</p>
                    <span class="text-xs text-slate-500">{{ $course->enrollments_count }} enrolled</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No courses assigned</p>
            @endforelse
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">My Batches</h3>
            @forelse($batches as $batch)
                <div class="py-3 border-b border-slate-200 last:border-0">
                    <p class="text-sm text-white font-medium">{{ $batch->title }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $batch->learners_count }} learners · {{ ucfirst($batch->status) }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">No batches assigned</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Pending Tasks</h3>
            @forelse($tasks as $task)
                <div class="py-3 border-b border-slate-200 last:border-0">
                    <p class="text-sm text-white">{{ $task->title }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">No pending tasks</p>
            @endforelse
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Upcoming Events</h3>
            @forelse($events as $event)
                <div class="py-3 border-b border-slate-200 last:border-0">
                    <p class="text-sm text-white">{{ $event->title }}</p>
                    <p class="text-xs text-indigo-600 mt-1">{{ $event->starts_at->format('M d, Y h:i A') }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">No upcoming events</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
