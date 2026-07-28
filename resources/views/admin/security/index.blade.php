@extends('layouts.app')

@section('title', 'Security')
@section('page-title', 'Security')
@section('breadcrumb', 'Security')

@section('content')
<div class="space-y-6">
    @if(session('recovery_codes'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-semibold mb-2">Save these recovery codes now — they will not be shown again:</p>
            <ul class="grid grid-cols-2 gap-1 font-mono text-xs">
                @foreach(session('recovery_codes') as $code)
                    <li>{{ $code }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Two-factor authentication</h3>
            <p class="text-sm text-slate-500">TOTP authenticator apps, with email OTP as backup at challenge time.</p>
            @if($user->two_factor_enabled)
                <x-badge type="success">Enabled</x-badge>
                <form method="POST" action="{{ route('admin.security.2fa.disable') }}" class="space-y-3">
                    @csrf
                    <x-form-input label="Confirm password" name="password" type="password" required />
                    <button class="panel-btn-secondary text-red-600">Disable 2FA</button>
                </form>
            @else
                <a href="{{ route('admin.security.2fa.setup') }}" class="panel-btn-primary inline-flex">Enable 2FA</a>
            @endif
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Session limits</h3>
            <form method="POST" action="{{ route('admin.security.settings') }}" class="space-y-4">
                @csrf
                <x-form-input label="Max parallel devices" name="max_parallel_devices" type="number" :value="old('max_parallel_devices', $settings['max_parallel_devices'])" required />
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="require_2fa_admins" value="1" @checked(old('require_2fa_admins', $settings['require_2fa_admins']) == '1') class="rounded border-slate-300 text-brand-600">
                    Encourage 2FA for institute staff
                </label>
                <button class="panel-btn-primary">Save</button>
            </form>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Your devices</h3>
            <a href="{{ route('admin.security.login-history') }}" class="text-sm text-brand-600">Login history →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-slate-500 border-b"><th class="py-2">Device</th><th>IP</th><th>Last seen</th><th></th></tr></thead>
                <tbody>
                @forelse($devices as $device)
                    <tr class="border-b border-slate-100">
                        <td class="py-2">{{ $device->device_name }} @if($device->revoked_at)<x-badge type="danger">Revoked</x-badge>@endif</td>
                        <td>{{ $device->ip_address }}</td>
                        <td>{{ $device->last_seen_at?->diffForHumans() }}</td>
                        <td>
                            @if(! $device->revoked_at)
                            <form method="POST" action="{{ route('admin.security.devices.revoke', $device) }}">@csrf @method('DELETE')
                                <button class="text-xs text-red-500">Revoke</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-6 text-slate-500">No devices recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">IP allow / deny</h3>
        <p class="text-sm text-slate-500">If any allow rules exist, only matching IPs can access. Deny rules always block.</p>
        <form method="POST" action="{{ route('admin.security.ip-rules.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @csrf
            <x-form-input label="Type" name="rule_type" type="select" required>
                <option value="allow">Allow</option>
                <option value="deny">Deny</option>
            </x-form-input>
            <x-form-input label="IP / CIDR" name="ip_cidr" placeholder="203.0.113.0/24" required />
            <x-form-input label="Label" name="label" />
            <button class="panel-btn-primary">Add rule</button>
        </form>
        <ul class="divide-y divide-slate-100">
            @forelse($ipRules as $rule)
                <li class="py-3 flex items-center justify-between text-sm">
                    <span>
                        <x-badge :type="$rule->rule_type === 'allow' ? 'success' : 'danger'">{{ $rule->rule_type }}</x-badge>
                        <span class="font-mono ml-2">{{ $rule->ip_cidr }}</span>
                        @if($rule->label)<span class="text-slate-400 ml-2">{{ $rule->label }}</span>@endif
                    </span>
                    <form method="POST" action="{{ route('admin.security.ip-rules.destroy', $rule) }}">@csrf @method('DELETE')
                        <button class="text-xs text-red-500">Remove</button>
                    </form>
                </li>
            @empty
                <li class="py-4 text-slate-500 text-sm">No IP rules — all IPs allowed.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
