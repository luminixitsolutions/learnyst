@extends('layouts.app')

@section('title', $lesson->title)
@section('page-title', $lesson->title)
@section('breadcrumb', $course->title . ' / Lesson')

@section('content')
@php
    $isCompleted = $isCompleted ?? false;
    $drmPolicy = $drmPolicy ?? [];
    $mediaUrl = $mediaUrl ?? null;
    $watermark = $watermark ?? null;
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
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($lesson->lesson_type === 'video' && $lesson->hasPlayableMedia())
            <div class="aspect-video bg-black relative" id="sn-player-wrap">
                @if($lesson->isExternalEmbed() && $lesson->embedSrc())
                    <iframe src="{{ $lesson->embedSrc() }}" class="w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                @else
                    @php
                        $src = $mediaUrl ?: ($lesson->fileUrl() ?: $lesson->embedSrc());
                        $controlsList = ($drmPolicy['block_download'] ?? false) ? 'nodownload' : '';
                    @endphp
                    <video
                        id="sn-video"
                        controls
                        class="w-full h-full"
                        src="{{ $src }}"
                        @if($controlsList) controlsList="{{ $controlsList }}" oncontextmenu="return false" @endif
                    ></video>
                @endif
                @if($watermark)
                    <div class="pointer-events-none absolute inset-0 overflow-hidden opacity-40 select-none" aria-hidden="true">
                        <div class="absolute top-1/3 left-1/4 -rotate-12 text-white text-xs md:text-sm whitespace-nowrap drop-shadow">
                            {{ $watermark }}
                        </div>
                        <div class="absolute top-2/3 right-1/4 -rotate-12 text-white text-xs md:text-sm whitespace-nowrap drop-shadow">
                            {{ $watermark }}
                        </div>
                    </div>
                @endif
            </div>
            @if(!empty($mediaToken))
            <script>
                (function () {
                    const token = @json($mediaToken);
                    const video = document.getElementById('sn-video');
                    if (!video || !token) return;
                    @if(!empty($drmPolicy['restrict_seeking']))
                    let maxAllowed = 0;
                    video.addEventListener('timeupdate', () => { if (video.currentTime > maxAllowed) maxAllowed = video.currentTime; });
                    video.addEventListener('seeking', () => {
                        if (video.currentTime > maxAllowed + 1) video.currentTime = maxAllowed;
                    });
                    @endif
                    setInterval(() => {
                        if (video.paused) return;
                        fetch(@json(route('learner.media.heartbeat')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ token, seconds: 15 }),
                        }).then(r => r.json()).then(data => {
                            if (data && data.ok === false) video.pause();
                        }).catch(() => {});
                    }, 15000);
                })();
            </script>
            @endif
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
</div>
@endsection
