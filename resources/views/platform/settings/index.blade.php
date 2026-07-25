@extends('layouts.app')

@section('title', 'Platform Settings')
@section('page-title', 'Platform Settings')
@section('breadcrumb', 'Platform Admin / Settings')

@section('content')
@php $tab = request('tab', 'general'); @endphp

<div class="space-y-6">
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-4">
        <a href="{{ route('platform.settings.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'general' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">General</a>
        <a href="{{ route('platform.settings.index', ['tab' => 'payment']) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'payment' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">Payment / Razorpay</a>
        <a href="{{ route('platform.settings.index', ['tab' => 'google']) }}"
           class="px-4 py-2 rounded-xl text-sm font-medium transition {{ $tab === 'google' ? 'bg-brand-600 text-white' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100' }}">Google Login</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($tab === 'general')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="general">
            <x-form-input label="Platform Name" name="site_name" :value="old('site_name', $settings['site_name'])" required />
            <x-form-input label="Support Email" name="support_email" type="email" :value="old('support_email', $settings['support_email'])" required />
            <label class="flex items-center gap-3">
                <input type="hidden" name="maintenance_mode" value="0">
                <input type="checkbox" name="maintenance_mode" value="1" @checked(old('maintenance_mode', $settings['maintenance_mode']) === '1') class="rounded border-slate-300 text-indigo-600">
                <span class="text-sm text-slate-700">Maintenance Mode</span>
            </label>
            <button type="submit" class="panel-btn-primary">Save Settings</button>
        </form>
    @endif

    @if($tab === 'payment')
        <form method="POST" action="{{ route('platform.settings.update') }}" class="glass-card rounded-2xl p-6 space-y-4 max-w-2xl">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="payment">

            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Add your Razorpay Key ID and Secret so students can pay for paid courses on the website.
                Use <strong>test</strong> keys for local testing and <strong>live</strong> keys in production.
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Razorpay Key ID</label>
                <input type="text" name="razorpay_key" value="{{ old('razorpay_key', $payment['razorpay_key']) }}"
                       placeholder="rzp_test_xxxxxxxx" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Razorpay Secret</label>
                <input type="password" name="razorpay_secret" value="{{ old('razorpay_secret', $payment['razorpay_secret']) }}"
                       placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $payment['currency']) }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Payment Mode</label>
                <select name="payment_mode" class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                    <option value="test" @selected(old('payment_mode', $payment['payment_mode']) === 'test')>Test</option>
                    <option value="live" @selected(old('payment_mode', $payment['payment_mode']) === 'live')>Live</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Manual Payment Enabled (1/0)</label>
                <input type="text" name="manual_payment_enabled" value="{{ old('manual_payment_enabled', $payment['manual_payment_enabled']) }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">GST Enabled (1/0)</label>
                <input type="text" name="gst_enabled" value="{{ old('gst_enabled', $payment['gst_enabled']) }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
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
                <p>Create OAuth credentials in <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="text-indigo-600 hover:underline">Google Cloud Console</a> (OAuth 2.0 Client ID → Web application).</p>
                <p>Add this <strong>Authorized redirect URI</strong>:</p>
                <code class="block break-all rounded-lg bg-white border border-slate-200 px-3 py-2 text-xs text-slate-800">{{ $oauth['redirect_uri'] }}</code>
                <p class="text-xs text-slate-500">
                    Status:
                    @if($oauth['is_configured'])
                        <span class="text-emerald-700 font-medium">Configured</span>
                    @else
                        <span class="text-amber-700 font-medium">Not configured — Google buttons stay disabled until keys are saved</span>
                    @endif
                </p>
            </div>

            <label class="flex items-center gap-3">
                <input type="hidden" name="google_oauth_enabled" value="0">
                <input type="checkbox" name="google_oauth_enabled" value="1" @checked(old('google_oauth_enabled', $oauth['google_oauth_enabled']) === '1') class="rounded border-slate-300 text-indigo-600">
                <span class="text-sm text-slate-700">Enable Google signup &amp; login</span>
            </label>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Google Client ID</label>
                <input type="text" name="google_client_id" value="{{ old('google_client_id', $oauth['google_client_id']) }}"
                       placeholder="xxxxx.apps.googleusercontent.com"
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
            </div>

            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Google Client Secret</label>
                <input type="password" name="google_client_secret" value=""
                       placeholder="{{ $oauth['google_client_secret'] ? '•••••••• (leave blank to keep current)' : 'Enter client secret' }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm">
                @if($oauth['google_client_secret'])
                    <p class="text-xs text-slate-400">A secret is already saved. Leave blank to keep it unchanged.</p>
                @endif
            </div>

            <button type="submit" class="panel-btn-primary">Save Google Settings</button>
        </form>
    @endif
</div>
@endsection
