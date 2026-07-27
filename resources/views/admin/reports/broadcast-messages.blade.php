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

    <x-admin.report-datatable table-id="broadcastReportTable" :has-records="$campaigns->count() > 0" entity="campaigns" :order-column="2" order-direction="desc" export-file-name="broadcast-messages-report" empty-title="No broadcast campaigns">
        <thead><tr class="text-left">
            <th>Campaign / Message</th><th>Status</th><th>Scheduled</th><th>Sent At</th><th>Channel</th>
        </tr></thead>
        <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td class="font-medium text-slate-800">{{ $campaign->title }}</td>
                <td><x-badge type="info">{{ ucfirst($campaign->status) }}</x-badge></td>
                <td class="text-slate-500" data-order="{{ $campaign->scheduled_at?->timestamp ?? 0 }}">{{ $campaign->scheduled_at?->format('M d, Y H:i') ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $campaign->sent_at?->timestamp ?? 0 }}">{{ $campaign->sent_at?->format('M d, Y H:i') ?? '—' }}</td>
                <td class="capitalize">{{ str_replace('_', ' ', $campaign->channel) }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
