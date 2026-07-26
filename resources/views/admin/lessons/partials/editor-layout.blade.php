@props(['lesson', 'course', 'settings', 'mediaType' => 'video', 'accept' => 'video/*', 'showVideoUrl' => false, 'showEmbed' => false])

@php
    $section = $lesson->section;
    $totalLessons = $course->lessons()->count();
    $lessonIndex = $course->lessons()->where('course_lessons.id', '<=', $lesson->id)->count();
    $typeLabel = $lesson->typeLabel();
    $hasMedia = $lesson->hasPlayableMedia();
    $embedSrc = $lesson->embedSrc();
    $fileUrl = $lesson->fileUrl();
    $isEmbed = $lesson->isExternalEmbed();
@endphp

<div x-data="{
    dragOver: false,
    showSettings: false,
    showPreview: false,
    showEmbed: {{ ($showEmbed || $showVideoUrl) && ($isEmbed || filled($lesson->video_url)) ? 'true' : 'false' }}
}">
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
            <span class="text-sm text-slate-500">{{ $lessonIndex }} / {{ $totalLessons }} Lesson</span>
            <button type="button" onclick="location.reload()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reload
            </button>
            <a href="{{ route('admin.courses.builder', $course) }}" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">+ Add Lesson</a>
            <button type="button" @click="showSettings = !showSettings" class="p-1.5 rounded-lg bg-indigo-600 text-white" title="Settings">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </button>
        </div>
    </div>

    <div class="flex gap-6">
        {{-- Main content area --}}
        <div class="flex-1 min-w-0">
            <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Lesson title bar --}}
                <div class="mb-4">
                    <input type="text" name="title" value="{{ $lesson->title }}" required maxlength="60"
                           class="w-full text-lg font-semibold text-slate-800 border-0 border-b border-transparent hover:border-slate-200 focus:border-indigo-400 focus:ring-0 px-0 py-1 bg-transparent outline-none"
                           placeholder="Lesson title">
                    <p class="text-xs text-slate-400 mt-1">{{ $typeLabel }} lesson</p>
                </div>

                @if(in_array($mediaType, ['video', 'audio', 'pdf']))
                {{-- Media preview / dropzone --}}
                <div @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false"
                     @drop.prevent="dragOver = false; const dt = $event.dataTransfer.files; if(dt.length) { $refs.uploadInput.files = dt; $refs.uploadForm.submit(); }"
                     :class="dragOver ? 'border-indigo-400 bg-indigo-50/50' : 'border-slate-200 bg-white'"
                     class="border-2 border-dashed rounded-xl min-h-[320px] flex items-center justify-center transition mb-4 overflow-hidden">
                    <div class="text-center p-4 w-full">
                        @if($hasMedia)
                            @if($mediaType === 'video')
                                @if($isEmbed && $embedSrc)
                                    <div class="aspect-video w-full max-w-3xl mx-auto rounded-lg overflow-hidden bg-black mb-3">
                                        <iframe src="{{ $embedSrc }}" class="w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                    </div>
                                    <p class="text-sm text-green-600 font-medium">Embedded video ready</p>
                                @elseif($fileUrl)
                                    <video controls class="max-w-full max-h-72 mx-auto rounded-lg mb-3" src="{{ $fileUrl }}"></video>
                                    <p class="text-sm text-green-600 font-medium">{{ basename($lesson->file_path) }}</p>
                                @elseif($embedSrc)
                                    <video controls class="max-w-full max-h-72 mx-auto rounded-lg mb-3" src="{{ $embedSrc }}"></video>
                                    <p class="text-sm text-green-600 font-medium">Video URL linked</p>
                                @endif
                            @elseif($mediaType === 'audio' && $fileUrl)
                                <audio controls class="w-full max-w-md mx-auto mb-3" src="{{ $fileUrl }}"></audio>
                                <p class="text-sm text-green-600 font-medium">{{ basename($lesson->file_path) }}</p>
                            @elseif($mediaType === 'pdf' && $fileUrl)
                                <iframe src="{{ $fileUrl }}" class="w-full h-72 rounded-lg mb-3"></iframe>
                                <p class="text-sm text-green-600 font-medium">{{ basename($lesson->file_path) }}</p>
                            @endif

                            @if(in_array($lesson->media_processing_status, ['processing', 'encryption']))
                                <p class="text-xs text-amber-600 mt-1">{{ $lesson->media_processing_status === 'encryption' ? 'Encryption in progress...' : 'Processing...' }}</p>
                            @endif

                            <p class="text-xs text-slate-400 mt-3">
                                Drop a new file to replace, or use
                                <label class="text-indigo-600 font-medium cursor-pointer hover:underline">
                                    browse files
                                    <input type="file" name="file_path" accept="{{ $accept }}" class="hidden" @change="$el.closest('form').submit()">
                                </label>
                            </p>
                        @else
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <p class="text-slate-500 text-sm mb-1">No {{ $mediaType }} uploaded yet</p>
                            <p class="text-slate-500 text-sm">
                                Drop files here or
                                <label class="text-indigo-600 font-medium cursor-pointer hover:underline">
                                    browse files
                                    <input type="file" name="file_path" accept="{{ $accept }}" class="hidden" @change="$el.closest('form').submit()">
                                </label>
                            </p>
                            @if($showVideoUrl || $showEmbed)
                            <p class="text-xs text-slate-400 mt-2">or click <button type="button" @click="showEmbed = true" class="text-indigo-600 hover:underline">Embed video</button> to paste a YouTube / Vimeo URL</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Secondary upload options --}}
                <div class="flex items-center gap-3 mb-6">
                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                        Cloud Storage
                        <input type="file" name="file_path" accept="{{ $accept }}" class="hidden" @change="$el.closest('form').submit()">
                    </label>
                    @if($showVideoUrl || $showEmbed)
                    <button type="button" @click="showEmbed = !showEmbed" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        Embed video
                    </button>
                    @endif
                </div>

                <div x-show="showEmbed" x-cloak class="mb-6 p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                    <x-form-input label="Video / Embed URL" name="video_url" :value="$lesson->video_url" placeholder="https://youtube.com/watch?v=... or https://youtu.be/..." />
                    <p class="text-xs text-slate-400">Supports YouTube, Vimeo, or a direct video file URL. Click Save after pasting.</p>
                </div>
                @endif

                <div class="flex items-center justify-between">
                    <button type="button" @click="showPreview = !showPreview" class="px-4 py-2 rounded-lg border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">Preview</button>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800">Save</button>
                </div>
            </form>
            <form method="POST" action="{{ route('admin.lessons.remove', $lesson) }}" class="mt-3">@csrf @method('DELETE')
                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-sm text-red-500 hover:text-red-700">Remove lesson</button>
            </form>
        </div>

        {{-- Attachments sidebar --}}
        <div class="w-72 shrink-0">
            @include('admin.lessons.partials.attachments-sidebar', ['lesson' => $lesson])
        </div>
    </div>

    <form x-ref="uploadForm" method="POST" action="{{ route('admin.lessons.media.upload', $lesson) }}" enctype="multipart/form-data" class="hidden">
        @csrf
        <input type="file" name="media_file" accept="{{ $accept }}" x-ref="uploadInput">
    </form>

    @include('admin.lessons.partials.settings-panel', ['lesson' => $lesson])
</div>
