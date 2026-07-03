@extends('layouts.app')

@section('title', 'Create Mock Test')
@section('page-title', 'Create Mock Test')
@section('breadcrumb', 'MOCK TESTS / Create Mock Test')

@section('content')
<div class="max-w-3xl space-y-6" x-data="{
    title: @js(old('title', '')),
    isFree: @js((bool) old('is_free')),
    quizType: @js(old('quiz_type', 'online'))
}">
    <p class="text-sm text-slate-500 -mt-2">Start creating a new mock test</p>

    <div class="glass-card rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('admin.mock-tests.store') }}" id="mock-test-create-form" class="space-y-6">
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
                       placeholder="Enter mock test title"
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
                    <span class="text-sm text-slate-600">Make this a free mock test</span>
                </label>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-semibold text-slate-700">Quiz Type</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="quizType === 'online' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="quiz_type" value="online" x-model="quizType" class="sr-only">
                        <p class="text-sm font-semibold text-slate-800">Online Quiz</p>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Create online quiz by using competitive exam template</p>
                    </label>
                    <label class="relative cursor-pointer rounded-xl border-2 p-4 transition"
                           :class="quizType === 'offline' ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" name="quiz_type" value="offline" x-model="quizType" class="sr-only">
                        <p class="text-sm font-semibold text-slate-800">Offline Quiz</p>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Create offline quiz using essay type questions &amp; digitally evaluate the answers</p>
                    </label>
                </div>
                @error('quiz_type')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label for="template" class="block text-sm font-semibold text-slate-700">
                    Select Template
                    <span x-show="quizType === 'online'" class="text-red-500">*</span>
                    <span x-show="quizType === 'offline'" x-cloak class="font-normal text-slate-400">(optional)</span>
                </label>
                <select name="template" id="template" class="panel-select w-full">
                    <option value="">Select template for the quiz</option>
                    @foreach($templates as $key => $label)
                        <option value="{{ $key }}" @selected(old('template') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('template')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </form>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" form="mock-test-create-form" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shadow-sm">
            Create
        </button>
        <a href="{{ route('admin.mock-tests.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
            Cancel
        </a>
    </div>
</div>
@endsection
