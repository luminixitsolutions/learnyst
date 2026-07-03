@extends('layouts.app')

@section('title', 'Create Segment')
@section('page-title', 'Create Segment')
@section('breadcrumb', 'Segments / Create Segment')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    title: @js(old('title', '')),
    description: @js(old('description', ''))
}">
    <a href="{{ route('admin.segments.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <p class="text-sm text-slate-500 -mt-2">Start creating a new segment</p>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.segments.store') }}" id="segment-create-form" class="space-y-6">
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
                       placeholder="Enter segment title"
                       x-model="title"
                       class="panel-input">
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="description" class="block text-sm font-semibold text-slate-700">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-slate-400" x-text="description.length + '/255'">0/255</span>
                </div>
                <textarea name="description"
                          id="description"
                          rows="4"
                          maxlength="255"
                          required
                          placeholder="Segment description"
                          x-model="description"
                          class="panel-input resize-none">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" form="segment-create-form" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            Create
        </button>
        <a href="{{ route('admin.segments.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
            Cancel
        </a>
    </div>
</div>
@endsection
