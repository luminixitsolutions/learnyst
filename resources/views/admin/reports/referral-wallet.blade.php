@extends('layouts.app')

@section('title', 'Referral & Wallet Report')
@section('page-title', 'Referral & Wallet Report')
@section('breadcrumb', 'Reports / Sales / Referral & Wallet')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by user or referral code..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Referral Code</th>
                    <th class="px-6 py-4">Referred User</th>
                    <th class="px-6 py-4">Wallet Amount</th>
                    <th class="px-6 py-4">Credit/Debit</th>
                    <th class="px-6 py-4">Transaction Type</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>@foreach($records as $row)<tr></tr>@endforeach</tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No referral or wallet data" description="Referral and wallet tracking is not configured yet." />
        @endif
    </div>
</div>
@endsection
