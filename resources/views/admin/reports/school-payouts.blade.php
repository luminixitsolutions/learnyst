@extends('layouts.app')

@section('title', 'School Payouts Report')
@section('page-title', 'School Payouts Report')
@section('breadcrumb', 'Reports / School Payouts')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by transaction id..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($payouts->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Payout ID</th>
                    <th class="px-6 py-4">Transaction ID</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Payment Gateway</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                </tr></thead>
                <tbody>
                    @foreach($payouts as $payout)
                    <tr>
                        <td class="px-6 py-4">{{ $payout->id }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No payout records yet" description="Payout data will appear here once school payout tracking is configured." />
        @endif
    </div>
</div>
@endsection
