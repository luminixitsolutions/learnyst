@extends('layouts.app')

@section('title', 'Referral & Wallet Report')
@section('page-title', 'Referral & Wallet Report')
@section('breadcrumb', 'Reports / Sales / Referral & Wallet')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by user or referral code..." :showDateRange="true" />

    <x-admin.report-datatable table-id="referralWalletReportTable" :has-records="$records->count() > 0" entity="referral wallet records" :order-column="6" order-direction="desc" export-file-name="referral-wallet-report" empty-title="No referral or wallet data" empty-description="Referral and wallet tracking is not configured yet.">
        <thead><tr class="text-left">
            <th>Learner</th><th>Referral Code</th><th>Referred User</th><th>Wallet Amount</th><th>Credit/Debit</th><th>Transaction Type</th><th>Date</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($records as $row)
            <tr>
                <td class="font-medium text-slate-800">{{ $row->learner ?? '—' }}</td>
                <td class="font-mono text-indigo-600">{{ $row->referral_code ?? '—' }}</td>
                <td>{{ $row->referred_user ?? '—' }}</td>
                <td data-order="{{ $row->wallet_amount ?? 0 }}">₹{{ number_format($row->wallet_amount ?? 0, 2) }}</td>
                <td><x-badge :type="($row->credit_debit ?? '') === 'Credit' ? 'success' : 'warning'">{{ $row->credit_debit ?? '—' }}</x-badge></td>
                <td>{{ $row->transaction_type ?? '—' }}</td>
                <td class="text-slate-500">{{ $row->date ?? '—' }}</td>
                <td><x-badge type="info">{{ $row->status ?? '—' }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
