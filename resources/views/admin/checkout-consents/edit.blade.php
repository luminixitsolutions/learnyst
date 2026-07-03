@extends('layouts.app')

@section('title', 'Edit Consent')
@section('page-title', 'Edit Checkout Consent')
@section('breadcrumb', $checkoutConsent->title)

@section('content')
<div class="max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.checkout-consents.update', $checkoutConsent) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-form-input label="Title" name="title" :value="old('title', $checkoutConsent->title)" required />
            <x-form-input label="Description" name="description" :value="old('description', $checkoutConsent->description)" />
            <x-form-input label="Consent Body" name="body" type="textarea" :value="old('body', $checkoutConsent->body)" required />
            <x-form-input label="Sort Order" name="sort_order" type="number" :value="old('sort_order', $checkoutConsent->sort_order)" />
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" value="1" @checked(old('is_required', $checkoutConsent->is_required)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                    <span class="text-sm text-slate-300">Required at checkout</span>
                </label>
                <label class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $checkoutConsent->is_active)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                    <span class="text-sm text-slate-300">Active</span>
                </label>
            </div>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.checkout-consents.index') }}" class="text-sm text-slate-500 hover:text-white">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
