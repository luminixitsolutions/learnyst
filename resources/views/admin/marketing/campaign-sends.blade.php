@extends('layouts.app')

@section('title', 'Campaign Send Logs')
@section('page-title', 'Send Logs')
@section('breadcrumb', 'Marketing / Campaigns / Logs')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-white">{{ $campaign->title }}</h3>
            <p class="text-sm text-slate-400 mt-1">Channel: {{ $campaign->channel }} · Status: {{ $campaign->status }} · Audience: {{ $campaign->audience_count }}</p>
        </div>
        <a href="{{ route('admin.marketing.campaigns') }}" class="text-sm text-emerald-400 hover:underline">← Back to campaigns</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($sends->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Recipient</th>
                        <th class="px-6 py-4">Channel</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Sent At</th>
                        <th class="px-6 py-4">Error</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sends as $send)
                    <tr>
                        <td class="px-6 py-4 text-white">
                            {{ $send->recipient ?? '—' }}
                            <div class="text-xs text-slate-500">{{ $send->user?->name ?? $send->lead?->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-slate-400 capitalize">{{ $send->channel }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$send->status === 'sent' ? 'success' : ($send->status === 'failed' ? 'danger' : 'info')">
                                {{ ucfirst($send->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-400">{{ $send->sent_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500 text-xs max-w-xs truncate">{{ $send->error ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-700">{{ $sends->links() }}</div>
        @else
        <x-empty-state title="No send logs yet" description="Dispatch the campaign to generate logs." />
        @endif
    </div>
</div>
@endsection
