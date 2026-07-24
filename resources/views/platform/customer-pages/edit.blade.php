@extends('layouts.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('breadcrumb', 'Platform Admin / Customer Pages / ' . $meta['label'])

@section('content')
@php
    $stats = old('stats', $content['stats'] ?? [['value' => '', 'label' => '']]);
    if (!is_array($stats) || count($stats) === 0) {
        $stats = [['value' => '', 'label' => '']];
    }
    $items = old('items', $content['items'] ?? []);
    if (!is_array($items) || count($items) === 0) {
        $items = $slug === 'success-stories'
            ? [['title' => '', 'tag' => '', 'date' => '', 'read' => '', 'summary' => '', 'metric' => '', 'metric_label' => '']]
            : ($slug === 'wall-of-love'
                ? [['quote' => '', 'name' => '', 'role' => '', 'source' => '']]
                : [['quote' => '', 'name' => '', 'role' => '', 'result' => '', 'rating' => 5, 'featured' => false]]);
    }
@endphp

<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('platform.customer-pages.index') }}" class="text-sm text-indigo-600 hover:underline">← All customer pages</a>
            <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('website.customer', $slug) }}" target="_blank" class="panel-btn-secondary">Preview page</a>
            <form method="POST" action="{{ route('platform.customer-pages.reset', $slug) }}" onsubmit="return confirm('Reset this customer page to defaults?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="panel-btn-secondary text-red-600">Reset</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('platform.customer-pages.update', $slug) }}" class="glass-card rounded-2xl p-6 space-y-8">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Hero</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Page title" name="title" :value="old('title', $content['title'] ?? '')" required />
                <x-form-input label="Eyebrow" name="eyebrow" :value="old('eyebrow', $content['eyebrow'] ?? '')" />
                <x-form-input label="Caption" name="caption" :value="old('caption', $content['caption'] ?? 'Customers')" />
                <x-form-input label="Hero gradient (CSS)" name="hero_gradient" :value="old('hero_gradient', $content['hero_gradient'] ?? '')" />
            </div>
            <x-form-input label="Summary" name="summary" type="textarea" :value="old('summary', $content['summary'] ?? '')" required />
            <x-form-input label="Body" name="body" type="textarea" :value="old('body', $content['body'] ?? '')" required />
        </div>

        <div x-data="websiteRepeater(@js($stats))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Stats</h3>
                <button type="button" class="panel-btn-secondary" @click="add({value:'', label:''})">+ Add stat</button>
            </div>
            <template x-for="(item, index) in items" :key="'s'+index">
                <div class="rounded-xl border border-slate-200 p-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-slate-50/60">
                    <input type="text" :name="'stats['+index+'][value]'" x-model="item.value" class="panel-input" placeholder="12,000+">
                    <div class="flex gap-2">
                        <input type="text" :name="'stats['+index+'][label]'" x-model="item.label" class="panel-input flex-1" placeholder="Label">
                        <button type="button" class="text-xs text-red-600 px-2" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                </div>
            </template>
        </div>

        <div x-data="websiteRepeater(@js($items))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">
                    @if($slug === 'testimonials') Testimonials
                    @elseif($slug === 'success-stories') Success stories
                    @else Wall of love notes
                    @endif
                </h3>
                <button type="button" class="panel-btn-secondary" @click="add(
                    @if($slug === 'success-stories')
                        {title:'', tag:'', date:'', read:'', summary:'', metric:'', metric_label:''}
                    @elseif($slug === 'wall-of-love')
                        {quote:'', name:'', role:'', source:''}
                    @else
                        {quote:'', name:'', role:'', result:'', rating:5, featured:false}
                    @endif
                )">+ Add item</button>
            </div>

            <template x-for="(item, index) in items" :key="'i'+index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'Item ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>

                    @if($slug === 'success-stories')
                        <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Story title">
                        <textarea :name="'items['+index+'][summary]'" x-model="item.summary" class="panel-input" rows="2" placeholder="Short summary"></textarea>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" :name="'items['+index+'][tag]'" x-model="item.tag" class="panel-input" placeholder="Tag">
                            <input type="text" :name="'items['+index+'][date]'" x-model="item.date" class="panel-input" placeholder="Date">
                            <input type="text" :name="'items['+index+'][read]'" x-model="item.read" class="panel-input" placeholder="5 min read">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" :name="'items['+index+'][metric]'" x-model="item.metric" class="panel-input" placeholder="Metric (e.g. 50x)">
                            <input type="text" :name="'items['+index+'][metric_label]'" x-model="item.metric_label" class="panel-input" placeholder="Metric label">
                        </div>
                    @elseif($slug === 'wall-of-love')
                        <textarea :name="'items['+index+'][quote]'" x-model="item.quote" class="panel-input" rows="3" placeholder="Love note"></textarea>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" :name="'items['+index+'][name]'" x-model="item.name" class="panel-input" placeholder="Name">
                            <input type="text" :name="'items['+index+'][role]'" x-model="item.role" class="panel-input" placeholder="Role">
                            <input type="text" :name="'items['+index+'][source]'" x-model="item.source" class="panel-input" placeholder="Source">
                        </div>
                    @else
                        <textarea :name="'items['+index+'][quote]'" x-model="item.quote" class="panel-input" rows="3" placeholder="Testimonial quote"></textarea>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" :name="'items['+index+'][name]'" x-model="item.name" class="panel-input" placeholder="Name">
                            <input type="text" :name="'items['+index+'][role]'" x-model="item.role" class="panel-input" placeholder="Role / Institute">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" :name="'items['+index+'][result]'" x-model="item.result" class="panel-input" placeholder="Result highlight (e.g. 50x growth)">
                            <input type="number" min="1" max="5" :name="'items['+index+'][rating]'" x-model="item.rating" class="panel-input" placeholder="Rating 1-5">
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" :name="'items['+index+'][featured]'" value="1" x-model="item.featured" class="rounded border-slate-300 text-indigo-600">
                            Feature this quote at the top
                        </label>
                    @endif
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
            <a href="{{ route('platform.customer-pages.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Cancel</a>
        </div>
    </form>
</div>

@include('platform.website-content.partials._repeater-script')
@endsection
