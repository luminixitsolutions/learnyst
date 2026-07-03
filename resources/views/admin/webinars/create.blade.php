@extends('layouts.app')

@section('title', 'Create Webinar')
@section('page-title', 'Create Webinar')
@section('breadcrumb', 'Webinars / Create Webinar')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    title: @js(old('title', '')),
    isFree: @js((bool) old('is_free')),
    contentSecurity: @js(old('content_security', 'encryption'))
}">
    <a href="{{ route('admin.webinars.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <p class="text-sm text-slate-500 -mt-2">Start creating a new webinar.</p>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.webinars.store') }}" id="webinar-create-form" class="space-y-6">
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
                       placeholder="Enter webinar title"
                       x-model="title"
                       class="panel-input">
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700">Price</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                    <input type="number"
                           name="price"
                           id="price"
                           step="0.01"
                           min="0"
                           placeholder="Price"
                           value="{{ old('price') }}"
                           :disabled="isFree"
                           :class="isFree ? 'opacity-50 cursor-not-allowed' : ''"
                           class="panel-input pl-9">
                </div>
                @error('price')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox"
                           name="is_free"
                           value="1"
                           x-model="isFree"
                           @checked(old('is_free'))
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/30">
                    <span class="text-sm text-slate-600">Make this a free webinar</span>
                </label>
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
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" form="webinar-create-form" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            Create
        </button>
        <a href="{{ route('admin.webinars.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
            Cancel
        </a>
    </div>
</div>
@endsection
