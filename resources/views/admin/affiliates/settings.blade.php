@extends('layouts.app')

@section('title', 'Affiliate Settings')
@section('page-title', 'Affiliate Settings')
@section('breadcrumb', 'Sales / Affiliates / Settings')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.affiliates.settings.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="enabled" value="1" @checked(old('enabled', ($settings['enabled'] ?? '1') === '1')) class="rounded border-slate-300 text-emerald-600">
                <span class="text-sm text-slate-800">Enable affiliate program</span>
            </label>
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="auto_approve" value="1" @checked(old('auto_approve', ($settings['auto_approve'] ?? '0') === '1')) class="rounded border-slate-300 text-emerald-600">
                <span class="text-sm text-slate-800">Auto-approve new affiliates</span>
            </label>

            <x-form-input label="Default Commission (%)" name="default_commission_percent" type="number" step="0.01" :value="old('default_commission_percent', $settings['default_commission_percent'] ?? 10)" required />
            <x-form-input label="Cookie Attribution Days" name="cookie_days" type="number" :value="old('cookie_days', $settings['cookie_days'] ?? 30)" required />

            <div class="flex justify-between pt-2">
                <a href="{{ route('admin.affiliates.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Back</a>
                <button type="submit" class="panel-btn-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
