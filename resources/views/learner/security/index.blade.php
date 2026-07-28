@extends('layouts.app')

@section('title', 'Account security')
@section('page-title', 'Account security')
@section('breadcrumb', 'Security')

@section('content')
<div class="space-y-6 max-w-3xl">
    @if(session('recovery_codes'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm">
            <p class="font-semibold mb-2">Save recovery codes:</p>
            <ul class="font-mono text-xs grid grid-cols-2 gap-1">
                @foreach(session('recovery_codes') as $c)<li>{{ $c }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="glass-card rounded-2xl p-6 space-y-3">
        <h3 class="font-bold text-slate-800">Two-factor authentication</h3>
        @if($user->two_factor_enabled)
            <x-badge type="success">Enabled</x-badge>
            <form method="POST" action="{{ route('learner.security.2fa.disable') }}" class="space-y-3">
                @csrf
                <x-form-input label="Password" name="password" type="password" required />
                <button class="text-sm text-red-600">Disable 2FA</button>
            </form>
        @else
            <a href="{{ route('learner.security.2fa.setup') }}" class="panel-btn-primary inline-flex">Enable 2FA</a>
        @endif
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-slate-800 mb-4">Devices</h3>
        @foreach($devices as $device)
            <div class="flex items-center justify-between py-2 border-b border-slate-100 text-sm">
                <div>
                    <p>{{ $device->device_name }} · {{ $device->ip_address }}</p>
                    <p class="text-xs text-slate-400">{{ $device->last_seen_at?->diffForHumans() }}</p>
                </div>
                @if(! $device->revoked_at)
                <form method="POST" action="{{ route('learner.security.devices.revoke', $device) }}">@csrf @method('DELETE')
                    <button class="text-xs text-red-500">Revoke</button>
                </form>
                @endif
            </div>
        @endforeach
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-slate-800 mb-4">Recent logins</h3>
        @foreach($history as $row)
            <p class="text-sm py-1 text-slate-600">{{ $row->created_at }} · {{ $row->status }} · {{ $row->ip_address }} · {{ $row->provider }}</p>
        @endforeach
    </div>
</div>
@endsection
