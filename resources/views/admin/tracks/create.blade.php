@extends('layouts.app')

@section('title', 'Create Instructor Track')
@section('page-title', 'Create Instructor Track')
@section('breadcrumb', 'Tracks / Create')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    title: @js(old('title', '')),
    contentSecurity: @js(old('content_security', 'encryption'))
}">
    <p class="text-sm text-slate-500 -mt-2">Create a Instructor track by adding a title, description.</p>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.tracks.store') }}" id="track-create-form" class="space-y-6">
            @csrf

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="title" class="block text-sm font-semibold text-slate-700">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-slate-400" x-text="title.length + '/60'">0/60</span>
                </div>
                <input type="text"
                       name="title"
                       id="title"
                       maxlength="60"
                       required
                       placeholder="Enter track title"
                       x-model="title"
                       class="panel-input">
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label for="instructor_id" class="block text-sm font-semibold text-slate-700">
                    Select Instructor <span class="text-red-500">*</span>
                </label>
                <select name="instructor_id" id="instructor_id" required class="panel-select w-full">
                    <option value="">Select a Instructor</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id') == $instructor->id)>
                            {{ $instructor->name }} ({{ $instructor->email }})
                        </option>
                    @endforeach
                </select>
                @error('instructor_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                @if($instructors->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">No instructors found. <a href="{{ route('admin.instructors.create') }}" class="underline">Add an instructor</a> first.</p>
                @endif
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700">
                    Content Security <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="contentSecurity === 'encryption' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="content_security" value="encryption" x-model="contentSecurity" class="sr-only">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-800">Encryption</p>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide rounded-full bg-amber-100 text-amber-800">Recommended</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Secure content will be encrypted using DRM system and will be protected against piracy.</p>
                    </label>
                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="contentSecurity === 'no_encryption' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="content_security" value="no_encryption" x-model="contentSecurity" class="sr-only">
                        <p class="text-sm font-semibold text-slate-800">No Encryption</p>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Content will not be encrypted. Unsecure content can be easily downloaded and pirated.</p>
                    </label>
                </div>
                @error('content_security')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label for="description" class="block text-sm font-semibold text-slate-700">Description</label>
                <textarea name="description"
                          id="description"
                          rows="3"
                          placeholder="Optional track description"
                          class="panel-input resize-none">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" form="track-create-form" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            Create
        </button>
        <a href="{{ route('admin.tracks.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
            Cancel
        </a>
    </div>
</div>
@endsection
