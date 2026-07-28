@extends('layouts.app')

@section('title', 'Enable 2FA')
@section('page-title', 'Enable 2FA')

@section('content')
<div class="max-w-xl glass-card rounded-2xl p-6 space-y-4">
    <p class="text-sm text-slate-600">Add this secret to your authenticator app, then enter a code.</p>
    <p class="font-mono text-lg tracking-widest">{{ $secret }}</p>
    <form method="POST" action="{{ route('learner.security.2fa.confirm') }}" class="space-y-4">
        @csrf
        <x-form-input label="Code" name="code" required />
        <button class="panel-btn-primary">Confirm</button>
    </form>
</div>
@endsection
