@extends('layouts.app')

@section('title', 'Broadcast Messages Report')
@section('page-title', 'Broadcast Messages Report')
@section('breadcrumb', 'Reports / Sales / Broadcast')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search campaign title..." :showDateRange="true">
        <x-slot:filters>
            <select name="channel" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Channels</option>
                @foreach(['email','whatsapp','both'] as $ch)
                    <option value="{{ $ch }}" @selected(request('channel') === $ch)>{{ ucfirst($ch) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($campaigns->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Campaign / Message</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Scheduled</th>
                    <th class="px-6 py-4">Sent At</th>
                    <th class="px-6 py-4">Channel</th>
                </tr></thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $campaign->title }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($campaign->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $campaign->scheduled_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $campaign->sent_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $campaign->channel) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $campaigns->links() }}</div>
        @else
        <x-empty-state title="No broadcast campaigns" />
        @endif
    </div>
</div>
@endsection
