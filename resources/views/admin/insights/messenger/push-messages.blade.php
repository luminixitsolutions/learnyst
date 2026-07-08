@extends('layouts.app')

@section('title', 'Push Messages Report')
@section('page-title', 'Push Messages Report')
@section('breadcrumb', 'Insights / Messenger / Push Messages')

@section('content')
<div class="space-y-6">
    <x-insight-toolbar :backRoute="route('admin.insights.messenger.index')" searchPlaceholder="Search message title..." />
    <div class="glass-card rounded-2xl overflow-hidden">
        <x-empty-state title="No results found" description="Push message analytics will appear when push notifications are configured." />
    </div>
</div>
@endsection
