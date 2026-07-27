@extends('layouts.app')

@section('title', 'Edit Test Series')
@section('page-title', 'Edit Test Series')
@section('breadcrumb', 'Test Series / Edit')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    title: @js(old('title', $testSeries->title)),
    isFree: @js((bool) old('is_free', $testSeries->is_free))
}">
    <a href="{{ route('admin.test-series.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <p class="text-sm text-slate-500 -mt-2">Update test series details.</p>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.test-series.update', $testSeries) }}" id="test-series-edit-form" class="space-y-6">
            @csrf
            @method('PUT')

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
                       placeholder="Enter test series title"
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
                           value="{{ old('price', $testSeries->price) }}"
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
                           @checked(old('is_free', $testSeries->is_free))
                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/30">
                    <span class="text-sm text-slate-600">Make it a free test series</span>
                </label>
            </div>

            <div class="space-y-1.5">
                <label for="status" class="block text-sm font-semibold text-slate-700">Status</label>
                <select name="status" id="status" class="panel-select w-full">
                    @foreach(['draft', 'published', 'unpublished'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $testSeries->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" form="test-series-edit-form" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            Save Changes
        </button>
        <a href="{{ route('admin.test-series.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
            Cancel
        </a>
    </div>
</div>
@endsection
