@extends('layouts.app')

@section('title', 'Email Delivery Report')
@section('page-title', 'Email Delivery Report')
@section('breadcrumb', 'Insights / Messenger / Email Delivery')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search email..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        <x-empty-state title="No results found" description="Per-recipient delivery stats will appear when email delivery tracking is enabled." />
    </div>
</div>
@endsection
