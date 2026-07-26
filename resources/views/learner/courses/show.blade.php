@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('breadcrumb', 'Course / Curriculum')

@push('styles')
<style>
    .lesson-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        min-height: 2.25rem;
        padding: 0.45rem 0.9rem;
        border-radius: 0.7rem;
        font-size: 0.8125rem;
        font-weight: 600;
        line-height: 1;
        border: 1px solid transparent;
        transition: all .15s ease;
        white-space: nowrap;
        text-decoration: none;
    }
    .lesson-action-btn svg { width: 0.95rem; height: 0.95rem; flex-shrink: 0; }

    .lesson-action-btn--complete {
        background: #059669;
        border-color: #059669;
        color: #fff;
        box-shadow: 0 1px 2px rgba(5, 150, 105, 0.2);
    }
    .lesson-action-btn--complete:hover {
        background: #047857;
        border-color: #047857;
    }

    .lesson-action-btn--done {
        background: #ecfdf5;
        border-color: #6ee7b7;
        color: #047857;
        cursor: default;
        pointer-events: none;
    }

    .lesson-action-btn--open {
        background: #fff;
        border-color: #e2e8f0;
        color: #475569;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .lesson-action-btn--open:hover {
        background: #f8fafc;
        border-color: #c7d2fe;
        color: #4f46e5;
    }

    .lesson-row-icon {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .lesson-row-icon--todo { background: #eef2ff; color: #4f46e5; }
    .lesson-row-icon--done { background: #ecfdf5; color: #059669; }
</style>
@endpush

@section('content')
@php
    $completedLessonIds = $completedLessonIds ?? [];
    $totalLessons = $course->sections->sum(fn ($s) => $s->lessons->count());
    $completedCount = count($completedLessonIds);
    $allComplete = $totalLessons > 0 && $completedCount >= $totalLessons;
@endphp

<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <x-badge type="success">{{ number_format((float) ($enrollment->progress ?? 0), 0) }}% Complete</x-badge>
                <p class="text-sm text-slate-500 mt-3 max-w-2xl">{{ $course->description }}</p>
            </div>
            @if($course->thumbnailUrl())
                <img src="{{ $course->thumbnailUrl() }}" alt="" class="w-32 h-20 object-cover rounded-xl border border-slate-200">
            @endif
        </div>
        <div class="mt-4 h-2 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-full bg-brand-500 rounded-full transition-all" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
        </div>
        <p class="text-xs text-slate-400 mt-2">{{ count($completedLessonIds) }} of {{ $course->sections->sum(fn ($s) => $s->lessons->count()) }} lessons completed</p>
    </div>

    <div class="space-y-4">
        @foreach($course->sections as $section)
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-slate-800">{{ $section->title }}</h3>
                    @if($section->description)<p class="text-xs text-slate-500 mt-1">{{ $section->description }}</p>@endif
                </div>
                @php
                    $sectionDone = $section->lessons->filter(fn ($l) => in_array($l->id, $completedLessonIds))->count();
                    $sectionTotal = $section->lessons->count();
                @endphp
                <span class="text-xs font-medium text-slate-500">{{ $sectionDone }}/{{ $sectionTotal }} done</span>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($section->lessons as $lesson)
                @php $done = in_array($lesson->id, $completedLessonIds); @endphp
                <div class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 hover:bg-slate-50/80 transition">
                    <a href="{{ route('learner.lessons.show', $lesson) }}" class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                        <div class="lesson-row-icon {{ $done ? 'lesson-row-icon--done' : 'lesson-row-icon--todo' }}">
                            @if($done)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 font-semibold truncate">{{ $lesson->title }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ ucfirst(str_replace('_', ' ', $lesson->lesson_type)) }}
                                @if($lesson->duration_minutes) · {{ $lesson->duration_minutes }} min @endif
                            </p>
                        </div>
                    </a>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($lesson->is_preview)
                            <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Preview</span>
                        @endif

                        @if($done)
                            <span class="lesson-action-btn lesson-action-btn--done" title="Lesson completed">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Completed
                            </span>
                        @else
                            <form method="POST" action="{{ route('learner.lessons.complete', $lesson) }}">
                                @csrf
                                <button type="submit" class="lesson-action-btn lesson-action-btn--complete" title="Mark lesson as complete">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Complete
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('learner.lessons.show', $lesson) }}" class="lesson-action-btn lesson-action-btn--open" title="Open lesson">
                            Open
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @if($allComplete)
    <div class="glass-card rounded-2xl p-6 border border-emerald-200 bg-gradient-to-br from-emerald-50/80 to-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Course completed</h3>
                    <p class="text-sm text-slate-500 mt-0.5">
                        @if($certificate ?? null)
                            Your certificate is ready. Download it anytime.
                        @else
                            You finished every lesson. Issue your certificate to download it.
                        @endif
                    </p>
                </div>
            </div>

            @if($certificate ?? null)
                <a href="{{ route('learner.certificates.download', $certificate) }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition shadow-sm shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download Certificate
                </a>
            @else
                <form method="POST" action="{{ route('learner.courses.certificate.issue', $course) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition shadow-sm shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        Issue Certificate
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
