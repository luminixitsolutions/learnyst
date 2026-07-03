@extends('layouts.app')

@section('title', $lesson->title)
@section('page-title', $lesson->title)
@section('breadcrumb', $course->title . ' / Lesson')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('learner.courses.show', $course) }}" class="text-sm text-indigo-600 hover:text-indigo-800">← Back to course</a>
        <x-badge type="info">{{ ucfirst($lesson->lesson_type) }}</x-badge>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($lesson->lesson_type === 'video' && $lesson->video_url)
            <div class="aspect-video bg-black">
                <iframe src="{{ $lesson->video_url }}" class="w-full h-full" allowfullscreen></iframe>
            </div>
        @elseif($lesson->lesson_type === 'pdf' && $lesson->file_path)
            <div class="p-6">
                <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">
                    Download PDF
                </a>
            </div>
        @endif

        <div class="p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">{{ $lesson->title }}</h2>
            @if($lesson->content)
                <div class="prose prose-invert max-w-none text-slate-300 text-sm whitespace-pre-line">{{ $lesson->content }}</div>
            @endif
            @if($lesson->duration_minutes)
                <p class="text-xs text-slate-500 mt-4">Duration: {{ $lesson->duration_minutes }} minutes</p>
            @endif
        </div>
    </div>
</div>
@endsection
