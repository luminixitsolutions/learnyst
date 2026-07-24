@extends('layouts.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('breadcrumb', 'Platform Admin / Signup Form / ' . $meta['label'])

@section('content')
@php
    $options = old('options', $content['options'] ?? []);
    if (!is_array($options) || count($options) === 0) {
        $options = [['value' => '', 'label' => '', 'is_active' => true, 'opens_teach' => false]];
    }
@endphp

<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('platform.signup-form.index') }}" class="text-sm text-indigo-600 hover:underline">← All questions</a>
            <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('signup.show', $question === 'teach' ? 'teach' : $question) }}" target="_blank" class="panel-btn-secondary">Preview step</a>
            <form method="POST" action="{{ route('platform.signup-form.reset', $question) }}" onsubmit="return confirm('Reset this question to defaults?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="panel-btn-secondary text-red-600">Reset</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('platform.signup-form.update', $question) }}" class="glass-card rounded-2xl p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <x-form-input label="Question title" name="title" :value="old('title', $content['title'] ?? '')" required />
            <x-form-input label="Subtitle (optional)" name="subtitle" type="textarea" :value="old('subtitle', $content['subtitle'] ?? '')" />
            <x-form-input label="Field label (optional)" name="label" :value="old('label', $content['label'] ?? '')" />
        </div>

        <div x-data="websiteRepeater(@js($options))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Answer options</h3>
                <button type="button" class="panel-btn-secondary" @click="add({value:'', label:'', is_active:true, opens_teach:false})">+ Add option</button>
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'Option ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Label (shown to user)</label>
                            <input type="text" :name="'options['+index+'][label]'" x-model="item.label" class="panel-input" required placeholder="Coding">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Value key</label>
                            <input type="text" :name="'options['+index+'][value]'" x-model="item.value" class="panel-input" placeholder="coding (auto if blank)">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" :name="'options['+index+'][is_active]'" value="1" x-model="item.is_active" class="rounded border-slate-300 text-indigo-600">
                            Active
                        </label>
                        @if($question === 'business_type')
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" :name="'options['+index+'][opens_teach]'" value="1" x-model="item.opens_teach" class="rounded border-slate-300 text-indigo-600">
                                Show “What do you teach?” after this choice
                            </label>
                        @endif
                    </div>
                </div>
            </template>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="panel-btn-primary">Save options</button>
            <a href="{{ route('platform.signup-form.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Cancel</a>
        </div>
    </form>
</div>

@include('platform.website-content.partials._repeater-script')
@endsection
