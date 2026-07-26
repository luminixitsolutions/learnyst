@extends('layouts.app')

@section('title', $lesson->title)
@section('page-title', $lesson->title)
@section('breadcrumb', $course->title . ' / Lesson')

@section('content')
@php
    $isCompleted = $isCompleted ?? false;
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-4">
            <a href="{{ route('learner.courses.show', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to course</a>
            <x-badge type="info">{{ ucfirst(str_replace('_', ' ', $lesson->lesson_type)) }}</x-badge>
            @if($isCompleted)
                <x-badge type="success">Completed</x-badge>
            @endif
        </div>
        <div class="text-sm text-slate-500">
            Course progress: <span class="font-semibold text-slate-800">{{ number_format((float) ($enrollment->progress ?? 0), 0) }}%</span>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($lesson->lesson_type === 'video' && $lesson->hasPlayableMedia())
            <div class="aspect-video bg-black">
                @if($lesson->isExternalEmbed() && $lesson->embedSrc())
                    <iframe src="{{ $lesson->embedSrc() }}" class="w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                @elseif($lesson->fileUrl())
                    <video controls class="w-full h-full" src="{{ $lesson->fileUrl() }}"></video>
                @elseif($lesson->embedSrc())
                    <video controls class="w-full h-full" src="{{ $lesson->embedSrc() }}"></video>
                @endif
            </div>
        @elseif($lesson->lesson_type === 'pdf' && $lesson->fileUrl())
            <div class="p-6">
                <a href="{{ $lesson->fileUrl() }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">
                    Download PDF
                </a>
            </div>
        @endif

        <div class="p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">{{ $lesson->title }}</h2>
            @if($lesson->content)
                <div class="prose max-w-none text-slate-600 text-sm whitespace-pre-line">{{ $lesson->content }}</div>
            @endif
            @if($lesson->duration_minutes)
                <p class="text-xs text-slate-500 mt-4">Duration: {{ $lesson->duration_minutes }} minutes</p>
            @endif

            <div class="mt-6 pt-5 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('learner.courses.show', $course) }}" class="panel-btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to curriculum
                </a>

                @if($isCompleted)
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-200 text-sm font-semibold text-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Completed
                        </span>
                        <form method="POST" action="{{ route('learner.lessons.incomplete', $lesson) }}">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-500 hover:bg-slate-50 hover:text-slate-700">Undo</button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('learner.lessons.complete', $lesson) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-500 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Mark as Complete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
        <div class="h-full bg-brand-500 rounded-full transition-all" style="width: {{ $enrollment->fresh()->progress ?? $enrollment->progress ?? 0 }}%"></div>
    </div>
</div>
@endsection
