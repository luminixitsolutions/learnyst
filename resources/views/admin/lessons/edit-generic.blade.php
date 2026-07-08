@extends('layouts.app')

@section('title', $lesson->typeLabel())
@section('page-title', $lesson->typeLabel())
@section('breadcrumb', $lesson->title)

@section('content')
@php $section = $lesson->section; @endphp
<div x-data="{ showSettings: false, showPreview: false }">
    {{-- Top toolbar --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3 text-sm text-slate-500">
            <a href="{{ route('admin.courses.builder', $course) }}" class="hover:text-indigo-600 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ $course->title }}
            </a>
            <span>•</span>
            <span>{{ $section->title }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="showSettings = !showSettings" class="p-1.5 rounded-lg bg-indigo-600 text-white" title="Settings">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
            </button>
        </div>
    </div>

    <div class="flex gap-6">
        <div class="flex-1">
            <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="text" name="title" value="{{ $lesson->title }}" required maxlength="60"
                       class="w-full text-lg font-semibold text-slate-800 border-0 border-b border-transparent hover:border-slate-200 focus:border-indigo-400 focus:ring-0 px-0 py-1 bg-transparent outline-none mb-1">
                <p class="text-xs text-slate-400 mb-6">{{ $lesson->typeLabel() }} lesson</p>

                @if($lesson->lesson_type === 'text')
                <div class="border border-slate-200 rounded-xl bg-white p-6 mb-4 min-h-[320px]">
                    <label class="text-sm font-semibold text-slate-700 mb-2 block">Article Content</label>
                    <textarea name="content" rows="14" class="w-full border-0 text-sm text-slate-700 focus:ring-0 resize-none outline-none" placeholder="Write your article content here...">{{ $lesson->content }}</textarea>
                </div>

                @elseif($lesson->lesson_type === 'external_link')
                <div class="border-2 border-dashed border-slate-200 rounded-xl bg-white min-h-[320px] flex items-center justify-center mb-4">
                    <div class="text-center p-8 w-full max-w-lg">
                        <svg class="w-12 h-12 mx-auto text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <x-form-input label="SCORM / External URL" name="external_url" :value="$lesson->external_url" placeholder="https://your-scorm-package.com" />
                    </div>
                </div>

                @elseif($lesson->lesson_type === 'quiz')
                <div class="border border-purple-200 rounded-xl bg-purple-50/30 p-6 mb-4 min-h-[200px]">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">Quiz Builder</p>
                            <p class="text-xs text-slate-500">Add quiz questions and settings</p>
                        </div>
                    </div>
                    <x-form-input label="Quiz Instructions" name="content" type="textarea" :value="$lesson->content" />
                    <a href="{{ route('admin.quizzes.create') }}" class="inline-flex mt-3 text-sm text-purple-600 hover:underline">Open full Quiz Builder →</a>
                </div>

                @elseif($lesson->lesson_type === 'assignment')
                <div class="border border-indigo-200 rounded-xl bg-indigo-50/30 p-6 mb-4 min-h-[200px]">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">Assignment</p>
                            <p class="text-xs text-slate-500">Define assignment details and submission rules</p>
                        </div>
                    </div>
                    <x-form-input label="Assignment Instructions" name="content" type="textarea" :value="$lesson->content" />
                    <a href="{{ route('admin.assignments.create') }}" class="inline-flex mt-3 text-sm text-indigo-600 hover:underline">Open full Assignment Builder →</a>
                </div>

                @elseif($lesson->lesson_type === 'code')
                <div class="border border-slate-200 rounded-xl bg-slate-900 p-6 mb-4 min-h-[320px]">
                    <label class="text-sm font-semibold text-slate-300 mb-2 block">Code / Coding Lesson</label>
                    <textarea name="content" rows="14" class="w-full bg-transparent border-0 text-sm text-green-400 font-mono focus:ring-0 resize-none outline-none" placeholder="// Write code or coding instructions...">{{ $lesson->content }}</textarea>
                </div>
                @endif

                <div x-show="showPreview" x-cloak class="mb-4 p-4 rounded-xl border border-slate-200 bg-slate-50">
                    @if($lesson->lesson_type === 'external_link' && $lesson->external_url)
                        <iframe src="{{ $lesson->external_url }}" class="w-full h-64 rounded-lg"></iframe>
                    @elseif($lesson->content)
                        <div class="prose prose-sm text-slate-700 whitespace-pre-wrap">{{ $lesson->content }}</div>
                    @else
                        <p class="text-sm text-slate-400">Nothing to preview yet</p>
                    @endif
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" @click="showPreview = !showPreview" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">Preview</button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">Save</button>
                </div>
            </form>
            <form method="POST" action="{{ route('admin.lessons.remove', $lesson) }}" class="mt-3">@csrf @method('DELETE')
                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-sm text-red-500 hover:text-red-700">Remove lesson</button>
            </form>
        </div>

        <div class="w-72 shrink-0">
            @include('admin.lessons.partials.attachments-sidebar', ['lesson' => $lesson])
        </div>
    </div>

    @include('admin.lessons.partials.settings-panel', ['lesson' => $lesson])
</div>
@endsection
