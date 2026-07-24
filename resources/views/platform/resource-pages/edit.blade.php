@extends('layouts.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('breadcrumb', 'Platform Admin / Resource Pages / ' . $meta['label'])

@section('content')
@php
    $stats = old('stats', $content['stats'] ?? [['value'=>'','label'=>'']]);
    if (!is_array($stats) || count($stats) === 0) $stats = [['value'=>'','label'=>'']];
    $faq = old('faq', $content['faq'] ?? [['question'=>'','answer'=>'']]);
    if (!is_array($faq) || count($faq) === 0) $faq = [['question'=>'','answer'=>'']];
    $items = old('items', $content['items'] ?? []);
    if (!is_array($items) || count($items) === 0) {
        $items = match($slug) {
            'whats-new' => [['title'=>'','type'=>'New','date'=>'','summary'=>'','highlights'=>'']],
            'guides' => [['title'=>'','tag'=>'','read'=>'','desc'=>'']],
            default => [['icon'=>'fa-star','title'=>'','desc'=>'']],
        };
    }
    $featuresText = old('features', implode("\n", $content['features'] ?? []));
    $previewRoute = match($slug) {
        'help-center' => route('website.help-center'),
        'whats-new' => route('website.whats-new'),
        default => route('website.page', $slug),
    };
@endphp

<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('platform.resource-pages.index') }}" class="text-sm text-indigo-600 hover:underline">← All resource pages</a>
            <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ $previewRoute }}" target="_blank" class="panel-btn-secondary">Preview page</a>
            <form method="POST" action="{{ route('platform.resource-pages.reset', $slug) }}" onsubmit="return confirm('Reset this resource page to defaults?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="panel-btn-secondary text-red-600">Reset</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('platform.resource-pages.update', $slug) }}" class="glass-card rounded-2xl p-6 space-y-8">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Hero</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Page title" name="title" :value="old('title', $content['title'] ?? '')" required />
                <x-form-input label="Eyebrow" name="eyebrow" :value="old('eyebrow', $content['eyebrow'] ?? '')" />
                <x-form-input label="Caption" name="caption" :value="old('caption', $content['caption'] ?? 'Resources')" />
                <x-form-input label="Hero gradient (CSS)" name="hero_gradient" :value="old('hero_gradient', $content['hero_gradient'] ?? '')" />
            </div>
            <x-form-input label="Summary" name="summary" type="textarea" :value="old('summary', $content['summary'] ?? '')" required />
            <x-form-input label="Body" name="body" type="textarea" :value="old('body', $content['body'] ?? '')" required />
            @if(in_array($slug, ['product-demo', 'support-migration'], true))
                <x-form-input label="Features (one per line)" name="features" type="textarea" :value="$featuresText" />
            @endif
        </div>

        <div x-data="websiteRepeater(@js($items))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">
                    @if($slug === 'whats-new') Releases
                    @elseif($slug === 'guides') Guides
                    @elseif($slug === 'help-center') Help topics
                    @else Steps
                    @endif
                </h3>
                <button type="button" class="panel-btn-secondary" @click="add(
                    @if($slug === 'whats-new')
                        {title:'', type:'New', date:'', summary:'', highlights:''}
                    @elseif($slug === 'guides')
                        {title:'', tag:'', read:'', desc:''}
                    @else
                        {icon:'fa-star', title:'', desc:''}
                    @endif
                )">+ Add item</button>
            </div>
            <template x-for="(item, index) in items" :key="'i'+index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'Item ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                    @if($slug === 'whats-new')
                        <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Release title">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" :name="'items['+index+'][type]'" x-model="item.type" class="panel-input" placeholder="New">
                            <input type="text" :name="'items['+index+'][date]'" x-model="item.date" class="panel-input" placeholder="Date">
                        </div>
                        <textarea :name="'items['+index+'][summary]'" x-model="item.summary" class="panel-input" rows="2" placeholder="Summary"></textarea>
                        <textarea :name="'items['+index+'][highlights]'" x-model="item.highlights" class="panel-input" rows="4" placeholder="Highlights (one per line)"></textarea>
                    @elseif($slug === 'guides')
                        <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Guide title">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="text" :name="'items['+index+'][tag]'" x-model="item.tag" class="panel-input" placeholder="Tag">
                            <input type="text" :name="'items['+index+'][read]'" x-model="item.read" class="panel-input" placeholder="5 min">
                        </div>
                        <textarea :name="'items['+index+'][desc]'" x-model="item.desc" class="panel-input" rows="2" placeholder="Description"></textarea>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" :name="'items['+index+'][icon]'" x-model="item.icon" class="panel-input" placeholder="fa-star">
                            <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input md:col-span-2" placeholder="Title">
                        </div>
                        <textarea :name="'items['+index+'][desc]'" x-model="item.desc" class="panel-input" rows="2" placeholder="Description"></textarea>
                    @endif
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
                    <input type="text" :name="'stats['+index+'][value]'" x-model="item.value" class="panel-input" placeholder="30 min">
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
            <a href="{{ route('platform.resource-pages.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Cancel</a>
        </div>
    </form>
</div>

@include('platform.website-content.partials._repeater-script')
@endsection
