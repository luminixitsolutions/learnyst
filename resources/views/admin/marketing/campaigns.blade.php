@extends('layouts.app')

@section('title', 'Campaigns')
@section('page-title', 'Campaigns')
@section('breadcrumb', 'Marketing / Campaigns')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Create Campaign</h3>
        <form method="POST" action="{{ route('admin.marketing.campaigns.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Title" name="title" required />
                <x-form-input label="Email Subject" name="subject" placeholder="Optional subject line" />
            </div>
            <x-form-input label="Content" name="content" type="textarea" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Channel" name="channel" type="select" required>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="both">Email + WhatsApp</option>
                    <option value="email_sms">Email + SMS</option>
                    <option value="all">All channels</option>
                </x-form-input>
                <x-form-input label="Audience Segment" name="segment_id" type="select">
                    <option value="">All open leads</option>
                    @foreach($segments as $segment)
                        <option value="{{ $segment->id }}">{{ $segment->title }}</option>
                    @endforeach
                </x-form-input>
                <x-form-input label="Schedule At" name="scheduled_at" type="datetime-local" />
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Campaign</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($campaigns->count())
        <div class="overflow-x-auto">
            <table id="campaignsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Channel</th>
                        <th class="px-6 py-4">Segment</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Sent / Fail</th>
                        <th class="px-6 py-4">Scheduled</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-800 font-medium">{{ $campaign->title }}</td>
                        <td class="px-6 py-4 text-slate-600 capitalize">{{ str_replace('_', ' + ', $campaign->channel) }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $campaign->segment?->title ?? 'Open leads' }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($campaign->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-600">{{ $campaign->sent_count }}/{{ $campaign->failed_count }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $campaign->scheduled_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td class="px-6 py-4 space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.marketing.campaigns.sends', $campaign) }}" class="text-emerald-600 text-sm hover:underline">Logs</a>
                            @if(! in_array($campaign->status, ['sent', 'sending'], true))
                            <form method="POST" action="{{ route('admin.marketing.campaigns.send', $campaign) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sky-600 text-sm hover:underline" onclick="return confirm('Dispatch this campaign now?')">Send</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No campaigns yet" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($campaigns->count())
    <x-admin.datatable-scripts table-id="campaignsTable" entity="campaigns" :order-column="0" order-direction="desc" :action-column="6" export-file-name="campaigns" />
@endif
@endpush
