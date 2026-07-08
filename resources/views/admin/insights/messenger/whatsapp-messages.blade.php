@extends('layouts.app')

@section('title', 'WhatsApp Messages Report')
@section('page-title', 'WhatsApp Messages Report')
@section('breadcrumb', 'Insights / Messenger / WhatsApp Messages')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search campaign..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Campaign</th><th class="px-6 py-4">Sent</th><th class="px-6 py-4">Delivered</th>
                    <th class="px-6 py-4">Read</th><th class="px-6 py-4">Failed</th><th class="px-6 py-4">Date</th><th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $row)
                    <tr>
                        <td class="px-6 py-4 font-medium">{{ $row->title }}</td>
                        <td class="px-6 py-4">—</td><td class="px-6 py-4">—</td><td class="px-6 py-4">—</td><td class="px-6 py-4">—</td>
                        <td class="px-6 py-4 text-slate-500">{{ ($row->sent_at ?? $row->scheduled_at)?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($row->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No results found" />
        @endif
    </div>
</div>
@endsection
