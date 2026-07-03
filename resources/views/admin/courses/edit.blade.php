@extends('layouts.app')

@section('title', 'Edit Course')
@section('page-title', 'Edit Course')
@section('breadcrumb', $course->title)

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-6">Course Details</h3>
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.courses._form', ['course' => $course])
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary transition">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">Curriculum</h3>
            <span class="text-sm text-slate-500">{{ $course->sections->count() }} sections</span>
        </div>

        <form method="POST" action="{{ route('admin.courses.sections.store', $course) }}" class="mb-8 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
            @csrf
            <p class="text-sm font-medium text-indigo-600">Add Section</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Section Title" name="title" required />
                <x-form-input label="Drip Days" name="drip_days" type="number" placeholder="0" />
                <x-form-input label="Drip Date" name="drip_date" type="date" />
                <x-form-input label="Description" name="description" placeholder="Optional" class="md:col-span-3" />
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm hover:bg-brand-500">Add Section</button>
        </form>

        @forelse($course->sections as $section)
        <div class="mb-6 border border-slate-200 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 bg-slate-900/80">
                <div>
                    <h4 class="font-semibold text-slate-800">{{ $section->title }}</h4>
                    @if($section->description)<p class="text-xs text-slate-500 mt-0.5">{{ $section->description }}</p>@endif
                </div>
                <form method="POST" action="{{ route('admin.sections.destroy', $section) }}">@csrf @method('DELETE')
                    <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-500 hover:text-red-700">Delete Section</button>
                </form>
            </div>
            <div class="p-4 space-y-3">
                @foreach($section->lessons as $lesson)
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-900/40">
                    <div class="flex items-center gap-3">
                        <x-badge type="info">{{ ucfirst($lesson->lesson_type) }}</x-badge>
                        <span class="text-sm text-white">{{ $lesson->title }}</span>
                        @if($lesson->is_preview)<x-badge type="warning">Preview</x-badge>@endif
                    </div>
                    <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}">@csrf @method('DELETE')
                        <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-400">Remove</button>
                    </form>
                </div>
                @endforeach

                <form method="POST" action="{{ route('admin.sections.lessons.store', $section) }}" enctype="multipart/form-data" class="mt-4 p-4 rounded-lg border border-dashed border-slate-200 space-y-3">
                    @csrf
                    <p class="text-xs font-medium text-slate-500">Add Lesson</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <x-form-input label="Lesson Title" name="title" required />
                        <x-form-input label="Lesson Type" name="lesson_type" type="select" required>
                            @foreach(['video','pdf','text','quiz','assignment','live_class'] as $lt)
                                <option value="{{ $lt }}">{{ ucfirst(str_replace('_', ' ', $lt)) }}</option>
                            @endforeach
                        </x-form-input>
                        <x-form-input label="Video URL" name="video_url" placeholder="https://" />
                        <x-form-input label="Duration (min)" name="duration_minutes" type="number" />
                        <x-form-input label="Sort Order" name="sort_order" type="number" placeholder="Auto if empty" />
                        <x-form-input label="Drip Date" name="drip_date" type="date" />
                    </div>
                    <x-form-input label="Lesson Content" name="content" type="textarea" />
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Upload PDF / File</label>
                            <input type="file" name="file_path" class="text-xs text-slate-500">
                        </div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_preview" value="1" class="rounded border-slate-300 text-indigo-600">
                            <span class="text-xs text-slate-600">Preview Allowed</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_locked" value="1" class="rounded border-slate-300 text-indigo-600">
                            <span class="text-xs text-slate-600">Lock Lesson</span>
                        </label>
                    </div>
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs hover:bg-slate-700">Add Lesson</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-sm text-slate-500 text-center py-8">No sections yet. Add your first section above.</p>
        @endforelse
    </div>
</div>
@endsection
