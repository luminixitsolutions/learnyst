@extends('layouts.app')

@section('title', 'Affiliate Report')
@section('page-title', 'Affiliate Report')
@section('breadcrumb', 'Reports / Sales / Affiliates')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search affiliate name or email..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <table class="w-full text-sm panel-table"><thead><tr class="text-left">
            <th class="px-6 py-4">Affiliate</th><th class="px-6 py-4">Email</th><th class="px-6 py-4">Referrals</th>
            <th class="px-6 py-4">Total Sales</th><th class="px-6 py-4">Earned</th><th class="px-6 py-4">Paid</th><th class="px-6 py-4">Pending</th><th class="px-6 py-4">Status</th>
        </tr></thead><tbody></tbody></table>
        @else
        <x-empty-state title="No affiliate data" description="Affiliate program tracking is not configured yet." />
        @endif
    </div>
</div>
@endsection
