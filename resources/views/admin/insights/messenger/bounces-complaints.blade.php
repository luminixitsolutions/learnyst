@extends('layouts.app')

@section('title', 'Bounces & Complaints Report')
@section('page-title', 'Bounces & Complaints Report')
@section('breadcrumb', 'Insights / Messenger / Bounces & Complaints')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search email..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        <x-empty-state title="No results found" description="Bounce and complaint data will appear when email webhooks are configured." />
    </div>
</div>
@endsection
