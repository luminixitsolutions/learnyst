@extends('layouts.app')

@section('title', 'Workflow Mails Report')
@section('page-title', 'Workflow Mails Report')
@section('breadcrumb', 'Insights / Messenger / Workflow Mails')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search workflow or email..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        <x-empty-state title="No results found" description="Workflow mail tracking will appear when email automation is configured." />
    </div>
</div>
@endsection
