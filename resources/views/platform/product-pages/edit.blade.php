@extends('layouts.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('breadcrumb', 'Platform Admin / Product Pages / ' . $meta['label'])

@section('content')
@php
    $benefits = old('benefits', $content['benefits'] ?? []);
    if (!is_array($benefits) || count($benefits) === 0) {
        $benefits = [['icon' => 'fa-star', 'title' => '', 'desc' => '']];
    }
    $useCases = old('use_cases', $content['use_cases'] ?? []);
    if (!is_array($useCases) || count($useCases) === 0) {
        $useCases = [['title' => '', 'desc' => '']];
    }
    $stats = old('stats', $content['stats'] ?? []);
    if (!is_array($stats) || count($stats) === 0) {
        $stats = [['value' => '', 'label' => '']];
    }
    $faq = old('faq', $content['faq'] ?? []);
    if (!is_array($faq) || count($faq) === 0) {
        $faq = [['question' => '', 'answer' => '']];
    }
    $featuresText = old('features', implode("\n", $content['features'] ?? []));
    $heroUrl = \App\Models\WebsiteContent::mediaUrl($content['hero_image'] ?? null);
@endphp

<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('platform.product-pages.index') }}" class="text-sm text-indigo-600 hover:underline">← All product pages</a>
            <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('website.product', $slug) }}" target="_blank" class="panel-btn-secondary">Preview page</a>
            <form method="POST" action="{{ route('platform.product-pages.reset', $slug) }}" onsubmit="return confirm('Reset this product page to defaults?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="panel-btn-secondary text-red-600">Reset</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('platform.product-pages.update', $slug) }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-8">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Hero</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Page title" name="title" :value="old('title', $content['title'] ?? '')" required />
                <x-form-input label="Eyebrow" name="eyebrow" :value="old('eyebrow', $content['eyebrow'] ?? '')" />
                <x-form-input label="Caption" name="caption" :value="old('caption', $content['caption'] ?? 'Products')" />
                <x-form-input label="Hero gradient (CSS)" name="hero_gradient" :value="old('hero_gradient', $content['hero_gradient'] ?? '')" />
            </div>
            <x-form-input label="Summary (hero subtitle)" name="summary" type="textarea" :value="old('summary', $content['summary'] ?? '')" required />
            <x-form-input label="Body" name="body" type="textarea" :value="old('body', $content['body'] ?? '')" required />
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Hero image</label>
                <input type="file" name="hero_image" accept="image/*" class="panel-input">
                @if($heroUrl)
                    <img src="{{ $heroUrl }}" alt="" class="mt-2 h-28 rounded-lg object-contain border border-slate-200 bg-slate-50 p-2">
                @endif
            </div>
            <x-form-input label="Features (one per line)" name="features" type="textarea" :value="$featuresText" />
        </div>

        <div x-data="websiteRepeater(@js($benefits))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Benefit cards</h3>
                <button type="button" class="panel-btn-secondary" @click="add({icon:'fa-star', title:'', desc:''})">+ Add benefit</button>
            </div>
            <template x-for="(item, index) in items" :key="'b'+index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'Benefit ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" :name="'benefits['+index+'][icon]'" x-model="item.icon" class="panel-input" placeholder="fa-star">
                        <input type="text" :name="'benefits['+index+'][title]'" x-model="item.title" class="panel-input md:col-span-2" placeholder="Title">
                    </div>
                    <textarea :name="'benefits['+index+'][desc]'" x-model="item.desc" class="panel-input" rows="2" placeholder="Description"></textarea>
                </div>
            </template>
        </div>

        <div x-data="websiteRepeater(@js($useCases))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Use cases</h3>
                <button type="button" class="panel-btn-secondary" @click="add({title:'', desc:''})">+ Add use case</button>
            </div>
            <template x-for="(item, index) in items" :key="'u'+index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'Use case ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                    <input type="text" :name="'use_cases['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Title">
                    <textarea :name="'use_cases['+index+'][desc]'" x-model="item.desc" class="panel-input" rows="2" placeholder="Description"></textarea>
                </div>
            </template>
        </div>

        <div x-data="websiteRepeater(@js($stats))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Stats</h3>
                <button type="button" class="panel-btn-secondary" @click="add({value:'', label:''})">+ Add stat</button>
            </div>
            <template x-for="(item, index) in items" :key="'s'+index">
                <div class="rounded-xl border border-slate-200 p-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-50/60">
                    <input type="text" :name="'stats['+index+'][value]'" x-model="item.value" class="panel-input" placeholder="50,000+">
                    <div class="flex gap-2">
                        <input type="text" :name="'stats['+index+'][label]'" x-model="item.label" class="panel-input flex-1" placeholder="Label">
                        <button type="button" class="text-xs text-red-600 px-2" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                </div>
            </template>
        </div>

        <div x-data="websiteRepeater(@js($faq))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">FAQ</h3>
                <button type="button" class="panel-btn-secondary" @click="add({question:'', answer:''})">+ Add FAQ</button>
            </div>
            <template x-for="(item, index) in items" :key="'f'+index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'FAQ ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                    <input type="text" :name="'faq['+index+'][question]'" x-model="item.question" class="panel-input" placeholder="Question">
                    <textarea :name="'faq['+index+'][answer]'" x-model="item.answer" class="panel-input" rows="2" placeholder="Answer"></textarea>
                </div>
            </template>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Bottom CTA</h3>
            <x-form-input label="CTA title" name="cta_title" :value="old('cta_title', $content['cta_title'] ?? '')" />
            <x-form-input label="CTA text" name="cta_text" type="textarea" :value="old('cta_text', $content['cta_text'] ?? '')" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Primary button label" name="cta_primary_label" :value="old('cta_primary_label', $content['cta_primary_label'] ?? 'Start Free Trial')" />
                <x-form-input label="Secondary button label" name="cta_secondary_label" :value="old('cta_secondary_label', $content['cta_secondary_label'] ?? 'Book a Demo')" />
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="panel-btn-primary">Save page</button>
            <a href="{{ route('platform.product-pages.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Cancel</a>
        </div>
    </form>
</div>

@include('platform.website-content.partials._repeater-script')
@endsection
