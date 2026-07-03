@extends('layouts.app')

@section('title', 'Campaigns')
@section('page-title', 'Campaigns')
@section('breadcrumb', 'Marketing / Campaigns')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Create Campaign</h3>
        <form method="POST" action="{{ route('admin.marketing.campaigns.store') }}" class="space-y-4">
            @csrf
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Content" name="content" type="textarea" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Channel" name="channel" type="select" required>
                    <option value="email">Email</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="both">Both</option>
                </x-form-input>
                <x-form-input label="Schedule At" name="scheduled_at" type="datetime-local" />
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Campaign</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($campaigns->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Channel</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Scheduled</th>
                        <th class="px-6 py-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $campaign)
                    <tr>
                        <td class="px-6 py-4 text-white font-medium">{{ $campaign->title }}</td>
                        <td class="px-6 py-4 text-slate-500 capitalize">{{ $campaign->channel }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($campaign->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $campaign->scheduled_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $campaign->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $campaigns->links() }}</div>
        @else
        <x-empty-state title="No campaigns yet" />
        @endif
    </div>
</div>
@endsection
