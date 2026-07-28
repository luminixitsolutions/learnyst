@extends('layouts.app')

@section('title', 'Integrations')
@section('page-title', 'Integrations Hub')
@section('breadcrumb', 'Platform Admin / System / Integrations')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-500">Status overview for platform integrations. Configure each from Settings.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($cards as $card)
            <div class="glass-card rounded-2xl p-6 flex flex-col gap-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">{{ $card['label'] }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $card['description'] }}</p>
                    </div>
                    @if($card['configured'] && $card['enabled'])
                        <x-badge type="success">Ready</x-badge>
                    @elseif($card['configured'])
                        <x-badge type="warning">Configured</x-badge>
                    @else
                        <x-badge type="danger">Missing</x-badge>
                    @endif
                </div>
                <div class="text-xs text-slate-400">{{ $card['meta'] }}</div>
                <div class="mt-auto pt-2">
                    <a href="{{ $card['route'] }}" class="panel-btn-secondary text-sm inline-flex">Configure</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
