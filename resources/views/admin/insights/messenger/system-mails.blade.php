@extends('layouts.app')

@section('title', 'System Mails Report')
@section('page-title', 'System Mails Report')
@section('breadcrumb', 'Insights / Messenger / System Mails')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search email or trigger..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        <x-empty-state title="No results found" description="System mail tracking will appear when transactional email analytics are configured." />
    </div>
</div>
@endsection
