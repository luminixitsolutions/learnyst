@extends('layouts.app')

@section('title', $plan->exists ? 'Edit Plan' : 'Create Plan')
@section('page-title', $plan->exists ? 'Edit Subscription Plan' : 'Create Subscription Plan')
@section('breadcrumb', 'Sales / Subscriptions / ' . ($plan->exists ? 'Edit' : 'Create'))

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ $plan->exists ? route('admin.subscriptions.plans.update', $plan) : route('admin.subscriptions.plans.store') }}" class="space-y-5">
            @csrf
            @if($plan->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Title" name="title" required :value="old('title', $plan->title)" />
                <x-form-input label="Slug" name="slug" :value="old('slug', $plan->slug)" placeholder="auto-generated if blank" />
            </div>

            <x-form-input label="Description" name="description" type="textarea" :value="old('description', $plan->description)" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form-input label="Plan Type" name="plan_type" type="select" required>
                    @foreach(['course','bundle','test_series','platform'] as $type)
                        <option value="{{ $type }}" @selected(old('plan_type', $plan->plan_type) === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Billing Cycle" name="billing_cycle" type="select" required>
                    @foreach(['monthly','quarterly','yearly','custom'] as $cycle)
                        <option value="{{ $cycle }}" @selected(old('billing_cycle', $plan->billing_cycle) === $cycle)>{{ ucfirst($cycle) }}</option>
                    @endforeach
                </x-form-input>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="Billing Days (custom)" name="billing_days" type="number" :value="old('billing_days', $plan->billing_days)" />
                <x-form-input label="Price (₹)" name="price" type="number" step="0.01" required :value="old('price', $plan->price)" />
                <x-form-input label="Setup Fee (₹)" name="setup_fee" type="number" step="0.01" :value="old('setup_fee', $plan->setup_fee ?? 0)" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-form-input label="Currency" name="currency" :value="old('currency', $plan->currency ?? 'INR')" />
                <x-form-input label="Trial Days" name="trial_days" type="number" :value="old('trial_days', $plan->trial_days ?? 0)" />
                <x-form-input label="Product ID (optional)" name="product_id" type="number" :value="old('product_id', $plan->product_id)" />
            </div>

            <x-form-input label="Product Type (optional)" name="product_type" :value="old('product_type', $plan->product_type)" placeholder="e.g. course, bundle" />

            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="auto_renew" value="1" class="rounded border-slate-300 text-emerald-600" @checked(old('auto_renew', $plan->auto_renew))>
                    Auto renew
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-emerald-600" @checked(old('is_active', $plan->is_active ?? true))>
                    Active
                </label>
            </div>

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.subscriptions.plans') }}" class="text-sm text-slate-500 hover:text-slate-800">Cancel</a>
                <button type="submit" class="panel-btn-primary">{{ $plan->exists ? 'Update Plan' : 'Create Plan' }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
