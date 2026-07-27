@extends('layouts.app')

@section('title', 'Affiliate Report')
@section('page-title', 'Affiliate Report')
@section('breadcrumb', 'Reports / Sales / Affiliates')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search affiliate name or email..." :showDateRange="true" />

    <x-admin.report-datatable table-id="affiliatesReportTable" :has-records="$records->count() > 0" entity="affiliates" :order-column="3" order-direction="desc" export-file-name="affiliates-report" empty-title="No affiliate data" empty-description="Affiliate program tracking is not configured yet.">
        <thead><tr class="text-left">
            <th>Affiliate</th><th>Email</th><th>Total Referrals</th><th>Total Sales</th><th>Commission Earned</th><th>Commission Paid</th><th>Pending</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($records as $row)
            <tr>
                <td class="font-medium text-slate-800">{{ $row->name ?? '—' }}</td>
                <td class="text-slate-500">{{ $row->email ?? '—' }}</td>
                <td data-order="{{ $row->total_referrals ?? 0 }}">{{ $row->total_referrals ?? 0 }}</td>
                <td data-order="{{ $row->total_sales ?? 0 }}">₹{{ number_format($row->total_sales ?? 0, 0) }}</td>
                <td data-order="{{ $row->commission_earned ?? 0 }}">₹{{ number_format($row->commission_earned ?? 0, 2) }}</td>
                <td data-order="{{ $row->commission_paid ?? 0 }}">₹{{ number_format($row->commission_paid ?? 0, 2) }}</td>
                <td data-order="{{ $row->pending ?? 0 }}">₹{{ number_format($row->pending ?? 0, 2) }}</td>
                <td><x-badge :type="($row->status ?? '') === 'Active' ? 'success' : 'default'">{{ $row->status ?? '—' }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
