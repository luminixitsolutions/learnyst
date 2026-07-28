@extends('layouts.app')

@section('title', 'White Label')
@section('page-title', 'White Label (Web)')
@section('breadcrumb', 'Settings / White Label')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="rounded-xl border border-amber-700/40 bg-amber-950/30 px-4 py-3 text-sm text-amber-200">
        Web white-label only. Custom mobile apps (Flutter / iOS / Android) are not included.
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Branding</h3>
        <form method="POST" action="{{ route('admin.whitelabel.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <x-form-input label="Primary color" name="primary_color" :value="$company->primary_color ?? '#059669'" />
            <x-form-input label="Secondary color" name="secondary_color" :value="$company->secondary_color ?? '#0f172a'" />
            <x-form-input label="Accent" name="accent" :value="$company->theme_tokens['accent'] ?? ''" />
            <x-form-input label="Custom domain" name="custom_domain" :value="$company->custom_domain" placeholder="learn.yourinstitute.com" />
            <x-form-input label="Email from name" name="email_from_name" :value="$company->email_from_name" />
            <x-form-input label="Email from address" name="email_from_address" type="email" :value="$company->email_from_address" />
            <div>
                <label class="text-sm text-slate-300">Logo</label>
                <input type="file" name="logo" accept="image/*" class="mt-1 block w-full text-sm text-slate-400">
                @if($company->logo)<p class="text-xs text-slate-500 mt-1">Current: {{ $company->logo }}</p>@endif
            </div>
            <div>
                <label class="text-sm text-slate-300">Favicon</label>
                <input type="file" name="favicon" accept="image/*" class="mt-1 block w-full text-sm text-slate-400">
            </div>
            <div class="md:col-span-2"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Save branding</button></div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex justify-between items-start gap-3 mb-4">
            <div>
                <h3 class="text-lg font-bold text-white">Domain verification</h3>
                <p class="text-sm text-slate-400 mt-1">
                    Status:
                    @if($company->domain_verified_at)
                        <span class="text-emerald-400">Verified {{ $company->domain_verified_at->format('M d, Y') }}</span>
                    @else
                        <span class="text-amber-400">Pending</span>
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ route('admin.whitelabel.verify') }}">@csrf
                <button class="text-sm text-sky-400">Mark verified</button>
            </form>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500">
                <th class="py-2">Type</th><th>Host</th><th>Value</th><th>Note</th>
            </tr></thead>
            <tbody>
                @foreach($dns as $row)
                <tr class="border-t border-slate-700/50">
                    <td class="py-3 text-white font-mono">{{ $row['type'] }}</td>
                    <td class="py-3 text-slate-300 font-mono text-xs">{{ $row['host'] }}</td>
                    <td class="py-3 text-emerald-300 font-mono text-xs break-all">{{ $row['value'] }}</td>
                    <td class="py-3 text-slate-500 text-xs">{{ $row['note'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
