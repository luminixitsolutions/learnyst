@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')
@section('breadcrumb', 'Platform configuration')

@section('content')
<div x-data="{ tab: '{{ request('tab', 'general') }}' }" class="space-y-6">
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-4">
        @foreach(['general' => 'General', 'payment' => 'Payment / Razorpay', 'email' => 'Email SMTP', 'tax' => 'Tax', 'whatsapp' => 'WhatsApp', 'branding' => 'Theme & Branding'] as $key => $label)
            <button @click="tab = '{{ $key }}'" :class="tab === '{{ $key }}' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:text-white hover:bg-slate-100'"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition">{{ $label }}</button>
        @endforeach
    </div>

    {{-- General Tab --}}
    <div x-show="tab === 'general'" x-cloak class="space-y-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Logo</h3>
            <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data" class="flex items-center gap-4">
                @csrf
                @if($groups['general']['logo'] ?? null)
                    <img src="{{ Storage::url($groups['general']['logo']) }}" alt="Logo" class="h-12">
                @endif
                <input type="file" name="logo" accept="image/*" required class="text-sm text-slate-500">
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm">Upload</button>
            </form>
        </div>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf @method('PUT')
            @foreach(['site_name' => 'Site Name', 'site_tagline' => 'Tagline', 'theme_color' => 'Theme Color'] as $key => $label)
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-300">{{ $label }}</label>
                    <input type="{{ $key === 'theme_color' ? 'color' : 'text' }}" name="settings[{{ $loop->index }}][value]"
                           value="{{ $groups['general'][$key] ?? '' }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <input type="hidden" name="settings[{{ $loop->index }}][group]" value="general">
                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $key }}">
                </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-medium">Save General Settings</button>
        </form>
    </div>

    {{-- Payment Tab --}}
    <div x-show="tab === 'payment'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf @method('PUT')
            @foreach(['razorpay_key' => 'Razorpay Key ID', 'razorpay_secret' => 'Razorpay Secret', 'currency' => 'Currency', 'payment_mode' => 'Payment Mode (test/live)', 'manual_payment_enabled' => 'Manual Payment (1/0)', 'gst_enabled' => 'GST Enable (1/0)'] as $key => $label)
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-300">{{ $label }}</label>
                    <input type="{{ str_contains($key, 'secret') ? 'password' : 'text' }}" name="settings[{{ $loop->index }}][value]"
                           value="{{ $groups['payment'][$key] ?? '' }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <input type="hidden" name="settings[{{ $loop->index }}][group]" value="payment">
                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $key }}">
                </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-medium">Save Payment Settings</button>
        </form>
    </div>

    {{-- Email Tab --}}
    <div x-show="tab === 'email'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf @method('PUT')
            @php $emailKeys = ['smtp_host' => 'SMTP Host', 'smtp_port' => 'SMTP Port', 'smtp_username' => 'Username', 'smtp_password' => 'Password', 'smtp_encryption' => 'Encryption', 'mail_from_address' => 'From Address', 'mail_from_name' => 'From Name']; @endphp
            @foreach($emailKeys as $key => $label)
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-300">{{ $label }}</label>
                    <input type="{{ str_contains($key, 'password') ? 'password' : 'text' }}" name="settings[{{ $loop->index }}][value]"
                           value="{{ $groups['email'][$key] ?? '' }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <input type="hidden" name="settings[{{ $loop->index }}][group]" value="email">
                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $key }}">
                </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-medium">Save Email Settings</button>
        </form>
    </div>

    {{-- Tax Tab --}}
    <div x-show="tab === 'tax'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf @method('PUT')
            @foreach(['gst_rate' => 'GST Rate (%)', 'gst_number' => 'GST Number', 'company_name' => 'Company Name', 'company_address' => 'Company Address'] as $key => $label)
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-300">{{ $label }}</label>
                    <input type="text" name="settings[{{ $loop->index }}][value]" value="{{ $groups['tax'][$key] ?? '' }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <input type="hidden" name="settings[{{ $loop->index }}][group]" value="tax">
                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $key }}">
                </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-medium">Save Tax Settings</button>
        </form>
    </div>

    {{-- WhatsApp Tab --}}
    <div x-show="tab === 'whatsapp'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf @method('PUT')
            @foreach(['whatsapp_api_key' => 'API Key', 'whatsapp_phone_number_id' => 'Phone Number ID', 'whatsapp_business_account_id' => 'Business Account ID', 'whatsapp_webhook_token' => 'Webhook Token'] as $key => $label)
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-300">{{ $label }}</label>
                    <input type="{{ str_contains($key, 'key') || str_contains($key, 'token') ? 'password' : 'text' }}" name="settings[{{ $loop->index }}][value]"
                           value="{{ $groups['whatsapp'][$key] ?? '' }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <input type="hidden" name="settings[{{ $loop->index }}][group]" value="whatsapp">
                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $key }}">
                </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm font-medium">Save WhatsApp Settings</button>
        </form>
    </div>

    {{-- Branding Tab --}}
    <div x-show="tab === 'branding'" x-cloak>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf @method('PUT')
            @foreach(['secondary_color' => 'Secondary Color', 'button_color' => 'Button Color', 'header_style' => 'Header Style', 'footer_text' => 'Footer Text', 'custom_domain' => 'Custom Domain', 'favicon' => 'Favicon Path/URL'] as $key => $label)
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-slate-700">{{ $label }}</label>
                    <input type="{{ str_contains($key, 'color') ? 'color' : 'text' }}" name="settings[{{ $loop->index }}][value]"
                           value="{{ $groups['general'][$key] ?? ($key === 'secondary_color' ? '#0d9488' : '') }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <input type="hidden" name="settings[{{ $loop->index }}][group]" value="general">
                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $key }}">
                </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary text-sm font-medium">Save Branding Settings</button>
        </form>
    </div>
</div>
@endsection
