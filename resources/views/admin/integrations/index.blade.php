@extends('layouts.app')

@section('title', 'Integrations')
@section('page-title', 'Integrations')
@section('breadcrumb', 'Integrations')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-500">Configure third-party providers. Secrets are stored encrypted. Use Test connection where available.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($providers as $key => $provider)
            <a href="{{ route('admin.integrations.edit', $key) }}" class="glass-card rounded-2xl p-5 hover:border-brand-300 border border-transparent transition">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $provider['label'] }}</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $key }}</p>
                    </div>
                    @php
                        $type = match($provider['status']) {
                            'connected' => 'success',
                            'incomplete' => 'warning',
                            default => 'danger',
                        };
                    @endphp
                    <x-badge :type="$type">{{ $provider['status'] }}</x-badge>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
