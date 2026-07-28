@extends('layouts.app')

@section('title', 'Enable 2FA')
@section('page-title', 'Enable 2FA')
@section('breadcrumb', 'Security / 2FA')

@section('content')
<div class="max-w-xl glass-card rounded-2xl p-6 space-y-4">
    <p class="text-sm text-slate-600">Scan this secret in Google Authenticator / Authy (or enter manually), then confirm with a 6-digit code.</p>
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
        <p class="text-xs text-slate-500 mb-1">Secret</p>
        <p class="font-mono text-lg tracking-widest">{{ $secret }}</p>
        <p class="text-xs text-slate-400 mt-3 break-all">{{ $uri }}</p>
    </div>
    <form method="POST" action="{{ route('admin.security.2fa.confirm') }}" class="space-y-4">
        @csrf
        <x-form-input label="Authenticator code" name="code" required />
        <button class="panel-btn-primary">Confirm & enable</button>
    </form>
</div>
@endsection
