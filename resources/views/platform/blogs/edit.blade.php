@extends('layouts.app')

@section('title', 'Blogs')
@section('page-title', 'Blogs')
@section('breadcrumb', 'Platform Admin / Blogs')

@section('content')
@php
    $posts = old('posts', $content['posts'] ?? []);
    if (!is_array($posts) || count($posts) === 0) {
        $posts = [['slug'=>'','title'=>'','excerpt'=>'','body'=>'','tag'=>'','date'=>'','read'=>'','author'=>'StudyNest Team','featured'=>false,'is_active'=>true]];
    }
@endphp

<div class="space-y-6 max-w-4xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-sm text-slate-500">Manage blog listing content and individual posts. Detail pages use each post slug.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('website.blogs') }}" target="_blank" class="panel-btn-secondary">Preview blogs</a>
            <form method="POST" action="{{ route('platform.blogs.reset') }}" onsubmit="return confirm('Reset blogs to defaults?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="panel-btn-secondary text-red-600">Reset</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('platform.blogs.update') }}" class="glass-card rounded-2xl p-6 space-y-8">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-slate-800">Listing page</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Page title" name="title" :value="old('title', $content['title'] ?? '')" required />
                <x-form-input label="Eyebrow" name="eyebrow" :value="old('eyebrow', $content['eyebrow'] ?? '')" />
                <x-form-input label="Caption" name="caption" :value="old('caption', $content['caption'] ?? 'Resources')" />
                <x-form-input label="Hero gradient (CSS)" name="hero_gradient" :value="old('hero_gradient', $content['hero_gradient'] ?? '')" />
            </div>
            <x-form-input label="Summary" name="summary" type="textarea" :value="old('summary', $content['summary'] ?? '')" required />
            <x-form-input label="Body" name="body" type="textarea" :value="old('body', $content['body'] ?? '')" required />
        </div>

        <div x-data="websiteRepeater(@js($posts))" class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-800">Blog posts</h3>
                <button type="button" class="panel-btn-secondary" @click="add({slug:'',title:'',excerpt:'',body:'',tag:'',date:'',read:'',author:'StudyNest Team',featured:false,is_active:true})">+ Add post</button>
            </div>
            @error('posts')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <template x-for="(item, index) in items" :key="'p'+index">
                <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-slate-700" x-text="'Post ' + (index + 1)"></h4>
                        <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                    </div>
                    <input type="text" :name="'posts['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Post title" required>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" :name="'posts['+index+'][slug]'" x-model="item.slug" class="panel-input" placeholder="url-slug (auto if blank)">
                        <input type="text" :name="'posts['+index+'][tag]'" x-model="item.tag" class="panel-input" placeholder="Tag">
                    </div>
                    <textarea :name="'posts['+index+'][excerpt]'" x-model="item.excerpt" class="panel-input" rows="2" placeholder="Excerpt"></textarea>
                    <textarea :name="'posts['+index+'][body]'" x-model="item.body" class="panel-input" rows="6" placeholder="Full article body (paragraphs separated by blank lines)"></textarea>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" :name="'posts['+index+'][author]'" x-model="item.author" class="panel-input" placeholder="Author">
                        <input type="text" :name="'posts['+index+'][date]'" x-model="item.date" class="panel-input" placeholder="Date">
                        <input type="text" :name="'posts['+index+'][read]'" x-model="item.read" class="panel-input" placeholder="5 min read">
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" :name="'posts['+index+'][featured]'" value="1" x-model="item.featured" class="rounded border-slate-300 text-indigo-600">
                            Featured on listing
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" :name="'posts['+index+'][is_active]'" value="1" x-model="item.is_active" class="rounded border-slate-300 text-indigo-600">
                            Active
                        </label>
                    </div>
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
            <button type="submit" class="panel-btn-primary">Save blogs</button>
        </div>
    </form>
</div>

@include('platform.website-content.partials._repeater-script')
@endsection
