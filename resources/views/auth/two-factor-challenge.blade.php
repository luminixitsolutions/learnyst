@extends('layouts.app')

@section('title', 'Two-factor')
@section('page-title', 'Two-factor verification')

@section('content')
<div class="max-w-md mx-auto mt-16 glass-card rounded-2xl p-6 space-y-4">
    <h1 class="text-xl font-bold text-slate-800">Verify it’s you</h1>
    <p class="text-sm text-slate-500">Enter the 6-digit code from your authenticator app, or use an email OTP / recovery code.</p>

    @if(session('success'))
        <div class="text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">{{ session('success') }}</div>
    @endif
    @if(session('otp_debug'))
        <div class="text-xs text-amber-700">Local debug OTP: {{ session('otp_debug') }}</div>
    @endif

    <form method="POST" action="{{ route('auth.2fa.verify') }}" class="space-y-4">
        @csrf
        <x-form-input label="Method" name="method" type="select">
            <option value="totp">Authenticator (TOTP)</option>
            <option value="email">Email OTP</option>
        </x-form-input>
        <x-form-input label="Code" name="code" required autofocus />
        <button class="w-full panel-btn-primary">Verify</button>
    </form>

    <form method="POST" action="{{ route('auth.2fa.email') }}">
        @csrf
        <button class="text-sm text-brand-600">Send email OTP instead</button>
    </form>
</div>
@endsection
