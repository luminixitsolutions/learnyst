@extends('layouts.app')

@section('title', 'Affiliate Products Report')
@section('page-title', 'Affiliate Products Report')
@section('breadcrumb', 'Reports / Sales / Affiliate Products')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search affiliate or product..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <table class="w-full text-sm panel-table"><thead><tr class="text-left">
            <th class="px-6 py-4">Affiliate</th><th class="px-6 py-4">Product</th><th class="px-6 py-4">Sales</th>
            <th class="px-6 py-4">Commission</th><th class="px-6 py-4">Conversion</th><th class="px-6 py-4">Payout Status</th>
        </tr></thead><tbody></tbody></table>
        @else
        <x-empty-state title="No affiliate product data" description="Affiliate product tracking is not configured yet." />
        @endif
    </div>
</div>
@endsection
