@extends('layouts.app')

@section('title', 'WhatsApp Workflow Messages')
@section('page-title', 'WhatsApp Workflow Messages Report')
@section('breadcrumb', 'Insights / Messenger / WhatsApp Workflow')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search workflow or template..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        <x-empty-state title="No results found" description="WhatsApp workflow message tracking will appear when automation is configured." />
    </div>
</div>
@endsection
