@extends('layouts.app')

@section('title', 'Platform Settings')
@section('page-title', 'Platform Settings')
@section('breadcrumb', 'Platform Admin / System / Settings')

@section('content')
@php
    $tab = request('tab', 'general');
    $tabs = [
        'general' => 'General',
        'payment' => 'Payment / Razorpay',
        'google' => 'Google Login',
        'email' => 'Email SMTP',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
        'security' => 'Security',
    ];
@endphp

<div class="space-y-6">
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-4">
        @foreach($tabs as $key => $label)
            <a href="{{ route('platform.settings.index', $key === 'general' ? [] : ['tab' => $key]) }}"
               class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $tab === $key ? 'bg-brand-600 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    @if($tab === 'general')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="general">
            <x-form-input label="Platform Name" name="site_name" :value="old('site_name', $settings['site_name'])" required />
            <x-form-input label="Support Email" name="support_email" type="email" :value="old('support_email', $settings['support_email'])" required />
            <p class="text-xs text-slate-500">Maintenance mode and IP allowlist are under the <a href="{{ route('platform.security.index') }}" class="text-indigo-600 hover:underline">Security</a> page.</p>
            <button type="submit" class="panel-btn-primary">Save Settings</button>
        </form>
    @endif

    @if($tab === 'payment')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="payment">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Razorpay Key ID and Secret for paid courses. Use <strong>test</strong> keys locally and <strong>live</strong> in production.
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Razorpay Key ID</label>
                <input type="text" name="razorpay_key" value="{{ old('razorpay_key', $payment['razorpay_key']) }}" placeholder="rzp_test_xxxxxxxx" class="panel-input w-full">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Razorpay Secret</label>
                <input type="password" name="razorpay_secret" value="" placeholder="{{ $payment['razorpay_secret'] ? '•••••••• (leave blank to keep)' : 'Enter secret' }}" class="panel-input w-full">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $payment['currency']) }}" class="panel-input w-full">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Payment Mode</label>
                <select name="payment_mode" class="panel-input w-full">
                    <option value="test" @selected(old('payment_mode', $payment['payment_mode']) === 'test')>Test</option>
                    <option value="live" @selected(old('payment_mode', $payment['payment_mode']) === 'live')>Live</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Manual Payment Enabled (1/0)</label>
                <input type="text" name="manual_payment_enabled" value="{{ old('manual_payment_enabled', $payment['manual_payment_enabled']) }}" class="panel-input w-full">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">GST Enabled (1/0)</label>
                <input type="text" name="gst_enabled" value="{{ old('gst_enabled', $payment['gst_enabled']) }}" class="panel-input w-full">
            </div>
            <button type="submit" class="panel-btn-primary">Save Razorpay Settings</button>
        </form>
    @endif

    @if($tab === 'google')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="google">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 space-y-2">
                <p>Create OAuth credentials in <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-indigo-600 hover:underline">Google Cloud Console</a>.</p>
                <p>Authorized redirect URI:</p>
                <code class="block break-all rounded-lg bg-white border border-slate-200 px-3 py-2 text-xs">{{ $oauth['redirect_uri'] }}</code>
                <p class="text-xs">Status: @if($oauth['is_configured'])<span class="text-emerald-700 font-medium">Configured</span>@else<span class="text-amber-700 font-medium">Not configured</span>@endif</p>
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="google_oauth_enabled" value="0">
                <input type="checkbox" name="google_oauth_enabled" value="1" @checked(old('google_oauth_enabled', $oauth['google_oauth_enabled']) === '1') class="rounded border-slate-300 text-brand-600">
                <span class="text-sm text-slate-700">Enable Google signup &amp; login</span>
            </label>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Google Client ID</label>
                <input type="text" name="google_client_id" value="{{ old('google_client_id', $oauth['google_client_id']) }}" class="panel-input w-full">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Google Client Secret</label>
                <input type="password" name="google_client_secret" value="" placeholder="{{ $oauth['google_client_secret'] ? '•••••••• (leave blank to keep)' : 'Enter client secret' }}" class="panel-input w-full">
            </div>
            <button type="submit" class="panel-btn-primary">Save Google Settings</button>
        </form>
    @endif

    @if($tab === 'email')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="email">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Platform-level SMTP for system emails. Stored in settings (not .env).</div>
            <x-form-input label="SMTP Host" name="smtp_host" :value="old('smtp_host', $email['smtp_host'])" />
            <x-form-input label="SMTP Port" name="smtp_port" :value="old('smtp_port', $email['smtp_port'])" />
            <x-form-input label="Username" name="smtp_username" :value="old('smtp_username', $email['smtp_username'])" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Password</label>
                <input type="password" name="smtp_password" value="" placeholder="{{ $email['smtp_password'] ? '•••••••• (leave blank to keep)' : 'SMTP password' }}" class="panel-input w-full">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Encryption</label>
                <select name="smtp_encryption" class="panel-input w-full">
                    @foreach(['tls','ssl','none'] as $enc)
                        <option value="{{ $enc }}" @selected(old('smtp_encryption', $email['smtp_encryption']) === $enc)>{{ strtoupper($enc) }}</option>
                    @endforeach
                </select>
            </div>
            <x-form-input label="From Address" name="mail_from_address" type="email" :value="old('mail_from_address', $email['mail_from_address'])" />
            <x-form-input label="From Name" name="mail_from_name" :value="old('mail_from_name', $email['mail_from_name'])" />
            <button type="submit" class="panel-btn-primary">Save Email Settings</button>
        </form>
    @endif

    @if($tab === 'sms')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="sms">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">Provider-ready SMS fields (MSG91, Twilio, etc.). Wiring to a specific SDK can follow later.</div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="sms_enabled" value="0">
                <input type="checkbox" name="sms_enabled" value="1" @checked(old('sms_enabled', $sms['sms_enabled']) === '1') class="rounded border-slate-300 text-brand-600">
                <span class="text-sm text-slate-700">Enable SMS integration</span>
            </label>
            <x-form-input label="Provider" name="sms_provider" :value="old('sms_provider', $sms['sms_provider'])" placeholder="msg91 / twilio / …" />
            <x-form-input label="API Key" name="sms_api_key" :value="old('sms_api_key', $sms['sms_api_key'])" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">API Secret</label>
                <input type="password" name="sms_api_secret" value="" placeholder="{{ $sms['sms_api_secret'] ? '•••••••• (leave blank to keep)' : 'Optional secret' }}" class="panel-input w-full">
            </div>
            <x-form-input label="Sender ID" name="sms_sender_id" :value="old('sms_sender_id', $sms['sms_sender_id'])" />
            <button type="submit" class="panel-btn-primary">Save SMS Settings</button>
        </form>
    @endif

    @if($tab === 'whatsapp')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="whatsapp">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">WhatsApp Business Cloud API (Meta) fields — provider-ready.</div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="whatsapp_enabled" value="0">
                <input type="checkbox" name="whatsapp_enabled" value="1" @checked(old('whatsapp_enabled', $whatsapp['whatsapp_enabled']) === '1') class="rounded border-slate-300 text-brand-600">
                <span class="text-sm text-slate-700">Enable WhatsApp integration</span>
            </label>
            <x-form-input label="Provider" name="whatsapp_provider" :value="old('whatsapp_provider', $whatsapp['whatsapp_provider'])" />
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">API Key / Access Token</label>
                <input type="password" name="whatsapp_api_key" value="" placeholder="{{ $whatsapp['whatsapp_api_key'] ? '•••••••• (leave blank to keep)' : 'Access token' }}" class="panel-input w-full">
            </div>
            <x-form-input label="Phone Number ID" name="whatsapp_phone_number_id" :value="old('whatsapp_phone_number_id', $whatsapp['whatsapp_phone_number_id'])" />
            <x-form-input label="Business Account ID" name="whatsapp_business_account_id" :value="old('whatsapp_business_account_id', $whatsapp['whatsapp_business_account_id'])" />
            <x-form-input label="Webhook Verify Token" name="whatsapp_webhook_token" :value="old('whatsapp_webhook_token', $whatsapp['whatsapp_webhook_token'])" />
            <button type="submit" class="panel-btn-primary">Save WhatsApp Settings</button>
        </form>
    @endif

    @if($tab === 'security')
        @include('platform.security._form', ['security' => $security, 'action' => route('platform.settings.update'), 'section' => 'security'])
    @endif
</div>
@endsection
