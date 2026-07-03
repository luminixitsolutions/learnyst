@extends('layouts.app')

@section('title', 'My Courses')
@section('page-title', 'My Courses')
@section('breadcrumb', 'Enrolled courses')

@section('content')
<div class="space-y-6">
    @if($enrollments->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($enrollments as $enrollment)
        <a href="{{ route('learner.courses.show', $enrollment->course) }}" class="glass-card rounded-2xl overflow-hidden hover:border-indigo-400/30 transition group">
            @if($enrollment->course?->thumbnail)
                <img src="{{ Storage::url($enrollment->course->thumbnail) }}" alt="" class="w-full h-40 object-cover">
            @else
                <div class="w-full h-40 bg-slate-800 flex items-center justify-center text-3xl font-bold text-indigo-600">{{ strtoupper(substr($enrollment->course?->title ?? 'C', 0, 2)) }}</div>
            @endif
            <div class="p-5">
                @if($enrollment->course?->category)
                    <x-badge type="info">{{ $enrollment->course->category->name }}</x-badge>
                @endif
                <h3 class="text-lg font-bold text-slate-800 mt-2 group-hover:text-indigo-600">{{ $enrollment->course?->title }}</h3>
                <div class="mt-3 h-2 bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-brand-500 rounded-full" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                </div>
                <p class="text-xs text-slate-500 mt-2">{{ $enrollment->progress ?? 0 }}% complete</p>
            </div>
        </a>
        @endforeach
    </div>
    <div>{{ $enrollments->links() }}</div>
    @else
    <div class="glass-card rounded-2xl">
        <x-empty-state title="No enrolled courses" description="Browse our catalog to start learning." />
    </div>
    @endif
</div>
@endsection
