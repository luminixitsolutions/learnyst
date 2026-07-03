@extends('layouts.app')

@section('title', 'Create Tag')
@section('page-title', 'Create Tag')
@section('breadcrumb', 'Tags / Create Tag')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    name: @js(old('name', '')),
    description: @js(old('description', '')),
    visibility: @js(old('visibility', 'public'))
}">
    <a href="{{ route('admin.tags.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <p class="text-sm text-slate-500 -mt-2">Create a tag to classify your content.</p>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.tags.store') }}" id="tag-create-form" class="space-y-6">
            @csrf

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-slate-400" x-text="name.length + '/60'">0/60</span>
                </div>
                <input type="text"
                       name="name"
                       id="name"
                       maxlength="60"
                       required
                       placeholder="Enter tag title"
                       x-model="name"
                       class="panel-input">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700">
                    Tag Visibility <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="visibility === 'public' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="visibility" value="public" x-model="visibility" class="sr-only">
                        <p class="text-sm font-semibold text-slate-800">Public</p>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Visible to everyone, allowing learners to filter products based on tags</p>
                    </label>
                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="visibility === 'private' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="visibility" value="private" x-model="visibility" class="sr-only">
                        <p class="text-sm font-semibold text-slate-800">Private</p>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Used only by admins for internal categorization and not visible to learners</p>
                    </label>
                    <label class="relative block cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="visibility === 'classification' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="visibility" value="classification" x-model="visibility" class="sr-only">
                        <p class="text-sm font-semibold text-slate-800">Classification</p>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Admin-only tags applied exclusively to questions and polls</p>
                    </label>
                </div>
                @error('visibility')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="description" class="block text-sm font-semibold text-slate-700">Description</label>
                    <span class="text-xs text-slate-400" x-text="description.length + '/255'">0/255</span>
                </div>
                <textarea name="description"
                          id="description"
                          rows="4"
                          maxlength="255"
                          placeholder="Tag description"
                          x-model="description"
                          class="panel-input resize-none">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" form="tag-create-form" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            Create
        </button>
        <a href="{{ route('admin.tags.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
            Cancel
        </a>
    </div>
</div>
@endsection
