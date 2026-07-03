@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('breadcrumb', 'Course / Curriculum')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-badge type="success">{{ $enrollment->progress ?? 0 }}% Complete</x-badge>
                <p class="text-sm text-slate-500 mt-3 max-w-2xl">{{ $course->description }}</p>
            </div>
            @if($course->thumbnail)
                <img src="{{ Storage::url($course->thumbnail) }}" alt="" class="w-32 h-20 object-cover rounded-xl">
            @endif
        </div>
        <div class="mt-4 h-2 bg-slate-800 rounded-full overflow-hidden">
            <div class="h-full bg-brand-500 rounded-full transition-all" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($course->sections as $section)
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h3 class="font-bold text-slate-800">{{ $section->title }}</h3>
                @if($section->description)<p class="text-xs text-slate-500 mt-1">{{ $section->description }}</p>@endif
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($section->lessons as $lesson)
                <a href="{{ route('learner.lessons.show', $lesson) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-indigo-50/40 transition">
                    <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-indigo-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-white font-medium">{{ $lesson->title }}</p>
                        <p class="text-xs text-slate-500">{{ ucfirst($lesson->lesson_type) }} @if($lesson->duration_minutes)· {{ $lesson->duration_minutes }} min @endif</p>
                    </div>
                    @if($lesson->is_preview)<x-badge type="warning">Preview</x-badge>@endif
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
