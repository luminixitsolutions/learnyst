@extends('layouts.app')

@section('title', 'Compliance Center')
@section('page-title', 'DPDP / GDPR Compliance')
@section('breadcrumb', 'Settings / Compliance')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('admin.compliance.update') }}" class="glass-card rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')
        <h3 class="font-bold text-slate-800">Privacy & retention</h3>

        <label class="flex items-center gap-3">
            <input type="hidden" name="dpdp_enabled" value="0">
            <input type="checkbox" name="dpdp_enabled" value="1" @checked($settings['dpdp_enabled']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm">Enable DPDP (India) compliance mode</span>
        </label>
        <label class="flex items-center gap-3">
            <input type="hidden" name="gdpr_enabled" value="0">
            <input type="checkbox" name="gdpr_enabled" value="1" @checked($settings['gdpr_enabled']) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm">Enable GDPR compliance mode</span>
        </label>
        <x-form-input label="Data retention (days)" name="data_retention_days" type="number" :value="$settings['data_retention_days']" required />
        <x-form-input label="Consent policy version" name="consent_version" :value="$settings['consent_version']" required />

        <button type="submit" class="panel-btn-primary">Save Compliance Settings</button>
    </form>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="font-bold text-slate-800">Consent templates</h3>
        <p class="text-sm text-slate-600">Checkout consents are versioned separately. Export/delete request workflows expand in Phase 6.</p>
        <ul class="divide-y divide-slate-100">
            @forelse($consentTemplates as $consent)
                <li class="py-3 flex items-center justify-between gap-3">
                    <div>
                        <p class="font-medium text-slate-800">{{ $consent->title }}</p>
                        <p class="text-xs text-slate-500">{{ $consent->is_required ? 'Required' : 'Optional' }} · {{ $consent->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                    <a href="{{ route('admin.checkout-consents.index') }}" class="text-xs text-indigo-600">Manage</a>
                </li>
            @empty
                <li class="py-6 text-sm text-slate-500">No consent templates configured.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
