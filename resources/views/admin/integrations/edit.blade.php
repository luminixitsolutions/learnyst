@extends('layouts.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('breadcrumb', 'Integrations / '.$meta['label'])

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <x-badge :type="$status === 'connected' ? 'success' : ($status === 'incomplete' ? 'warning' : 'danger')">{{ $status }}</x-badge>
        <a href="{{ route('admin.integrations.index') }}" class="text-sm text-slate-500">← All integrations</a>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.integrations.update', $provider) }}" class="space-y-4">
            @csrf
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="enabled" value="1" @checked(old('enabled', ($config['enabled'] ?? '0') === '1')) class="rounded border-slate-300 text-brand-600">
                Enabled
            </label>
            @foreach($meta['keys'] as $key)
                @continue($key === 'enabled')
                @php $isSecret = str_contains($key, 'secret') || str_contains($key, 'password') || str_contains($key, 'token') || $key === 'api_key' || $key === 'bot_token'; @endphp
                <x-form-input
                    :label="ucwords(str_replace('_', ' ', $key))"
                    :name="$key"
                    :type="$isSecret ? 'password' : 'text'"
                    :value="$isSecret ? '' : old($key, $config[$key] ?? '')"
                    :placeholder="$isSecret && !empty($config[$key.'_set']) ? '•••••••• (leave blank to keep)' : ''"
                />
            @endforeach
            <div class="flex flex-wrap gap-3 pt-2">
                <button class="panel-btn-primary">Save</button>
                <button formaction="{{ route('admin.integrations.test', $provider) }}" class="panel-btn-secondary">Test connection</button>
            </div>
        </form>

        @if($provider === 'telegram')
            <form method="POST" action="{{ route('admin.integrations.telegram.test') }}" class="mt-6 pt-6 border-t space-y-3">
                @csrf
                <p class="text-sm font-medium text-slate-700">Send test announcement</p>
                <x-form-input label="Chat ID (optional override)" name="chat_id" :value="old('chat_id', $config['default_chat_id'] ?? '')" />
                <button class="panel-btn-secondary">Send test</button>
            </form>
        @endif
    </div>
</div>
@endsection
