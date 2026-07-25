@extends('layouts.app')

@section('title', $mode === 'create' ? 'Add Package' : 'Edit Package')
@section('page-title', $mode === 'create' ? 'Add subscription package' : 'Edit '.$package->name)
@section('breadcrumb', 'Platform Admin / Pricing Packages / '.($mode === 'create' ? 'New' : 'Edit'))

@section('content')
@php
    $featureRows = old('features', $package->featureList() ?: ['']);
    if (empty($featureRows)) {
        $featureRows = [''];
    }
@endphp

<div class="max-w-3xl" x-data="{
    features: @js(array_values($featureRows)),
    addFeature() { this.features.push('') },
    removeFeature(i) { if (this.features.length > 1) this.features.splice(i, 1) }
}">
    <form method="POST"
          action="{{ $mode === 'create' ? route('platform.subscription-packages.store') : route('platform.subscription-packages.update', $package) }}"
          class="space-y-6">
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Basics</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" required class="panel-input" placeholder="Growth">
                    @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $package->slug) }}" class="panel-input" placeholder="auto-from-name">
                    @error('slug')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Sort order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $package->sort_order ?? 0) }}" class="panel-input">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tagline</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $package->tagline) }}" class="panel-input" placeholder="For growing academies">
                    @error('tagline')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="panel-input" placeholder="Short plan summary shown on the pricing card.">{{ old('description', $package->description) }}</textarea>
                    @error('description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Pricing</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                    <select name="currency" class="panel-input">
                        @foreach(['INR', 'USD', 'EUR', 'GBP'] as $code)
                            <option value="{{ $code }}" @selected(old('currency', $package->currency ?: 'INR') === $code)>{{ $code }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Monthly price</label>
                    <input type="number" step="0.01" min="0" name="price_monthly" value="{{ old('price_monthly', $package->price_monthly) }}" class="panel-input" placeholder="2999">
                    @error('price_monthly')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Yearly price</label>
                    <input type="number" step="0.01" min="0" name="price_yearly" value="{{ old('price_yearly', $package->price_yearly) }}" class="panel-input" placeholder="29990">
                    @error('price_yearly')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Trial days</label>
                    <input type="number" min="0" max="365" name="trial_days" value="{{ old('trial_days', $package->trial_days ?? 0) }}" class="panel-input">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Badge</label>
                    <input type="text" name="badge" value="{{ old('badge', $package->badge) }}" class="panel-input" placeholder="Most Popular">
                </div>
            </div>

            <div class="flex flex-wrap gap-5 pt-1">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_free" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_free', $package->is_free))>
                    Free plan
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_custom" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_custom', $package->is_custom))>
                    Custom / contact sales
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_featured', $package->is_featured))>
                    Featured (highlight on pricing page)
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('is_active', $package->is_active ?? true))>
                    Active on public pricing page
                </label>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Features</h3>
                <button type="button" @click="addFeature()" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Add feature</button>
            </div>
            <div class="space-y-2">
                <template x-for="(feature, index) in features" :key="index">
                    <div class="flex gap-2">
                        <input type="text" :name="'features['+index+']'" x-model="features[index]" class="panel-input" placeholder="Unlimited courses">
                        <button type="button" @click="removeFeature(index)" class="px-3 rounded-xl border border-slate-200 text-slate-400 hover:text-rose-600 hover:border-rose-200 shrink-0" title="Remove">×</button>
                    </div>
                </template>
            </div>
            @error('features')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-5">
            <h3 class="text-sm font-semibold text-slate-900 uppercase tracking-wide">Call to action</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Button label <span class="text-rose-500">*</span></label>
                    <input type="text" name="cta_label" value="{{ old('cta_label', $package->cta_label ?: 'Start Free Trial') }}" required class="panel-input">
                    @error('cta_label')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Button URL</label>
                    <input type="text" name="cta_url" value="{{ old('cta_url', $package->cta_url) }}" class="panel-input" placeholder="/signup (default)">
                    <p class="text-xs text-slate-400 mt-1">Leave blank to use signup (or demo for custom plans).</p>
                    @error('cta_url')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-soft">
                {{ $mode === 'create' ? 'Create package' : 'Save changes' }}
            </button>
            <a href="{{ route('platform.subscription-packages.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
        </div>
    </form>
</div>
@endsection
