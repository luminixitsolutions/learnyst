@extends('layouts.app')
@section('title', 'Instructor Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Instructor / Dashboard')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 xl:grid-cols-5 gap-4">
        <x-stat-card title="Courses" :value="number_format($stats['courses'])" />
        <x-stat-card title="Learners" :value="number_format($stats['learners'])" />
        <x-stat-card title="Upcoming classes" :value="number_format($stats['upcoming'])" />
        <x-stat-card title="To grade" :value="number_format($stats['grading'])" />
        <x-stat-card title="Open doubts" :value="number_format($stats['doubts'])" />
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('instructor.courses.index') }}" class="panel-btn-secondary text-sm">My Courses</a>
        <a href="{{ route('instructor.live-classes.create') }}" class="panel-btn-secondary text-sm">Schedule class</a>
        <a href="{{ route('instructor.assessments.index') }}" class="panel-btn-secondary text-sm">Assessments</a>
        <a href="{{ route('instructor.ai.index') }}" class="panel-btn-secondary text-sm">AI Tools</a>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-800 mb-3">Assigned courses</h3>
            @forelse($courses as $course)
                <a href="{{ route('instructor.courses.show', $course) }}" class="flex justify-between py-2 border-b border-slate-100 text-sm">
                    <span class="font-medium text-indigo-600">{{ $course->title }}</span>
                    <span class="text-slate-500">{{ $course->enrollments_count }} enrolled</span>
                </a>
            @empty
                <p class="text-sm text-slate-500">No courses assigned.</p>
            @endforelse
        </div>
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-800 mb-3">Upcoming live classes</h3>
            @forelse($upcomingClasses as $event)
                <a href="{{ route('instructor.live-classes.show', $event) }}" class="block py-2 border-b border-slate-100 text-sm">
                    <div class="font-medium text-slate-800">{{ $event->title }}</div>
                    <div class="text-xs text-slate-500">{{ $event->starts_at?->format('M d, Y H:i') }}</div>
                </a>
            @empty
                <p class="text-sm text-slate-500">No upcoming classes.</p>
            @endforelse
        </div>
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-800 mb-3">Pending submissions</h3>
            @forelse($pendingSubmissions as $sub)
                <a href="{{ route('instructor.assessments.submissions', $sub->lesson) }}" class="block py-2 border-b border-slate-100 text-sm">
                    <div class="font-medium">{{ $sub->learner?->name }} · {{ $sub->lesson?->title }}</div>
                    <div class="text-xs text-slate-500">{{ $sub->submitted_at?->diffForHumans() }}</div>
                </a>
            @empty
                <p class="text-sm text-slate-500">Nothing to grade.</p>
            @endforelse
        </div>
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-800 mb-3">Student questions</h3>
            @forelse($openDiscussions as $d)
                <a href="{{ route('instructor.discussions.show', $d) }}" class="block py-2 border-b border-slate-100 text-sm">
                    <div class="font-medium text-indigo-600">{{ $d->title }}</div>
                    <div class="text-xs text-slate-500">{{ $d->course?->title }} · {{ $d->user?->name }}</div>
                </a>
            @empty
                <p class="text-sm text-slate-500">No open questions.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
