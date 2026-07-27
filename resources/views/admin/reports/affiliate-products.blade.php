@extends('layouts.app')

@section('title', 'Affiliate Products Report')
@section('page-title', 'Affiliate Products Report')
@section('breadcrumb', 'Reports / Sales / Affiliate Products')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search affiliate or product..." :showDateRange="true" />

    <x-admin.report-datatable table-id="affiliateProductsReportTable" :has-records="$records->count() > 0" entity="affiliate products" :order-column="2" order-direction="desc" export-file-name="affiliate-products-report" empty-title="No affiliate product data" empty-description="Affiliate product tracking is not configured yet.">
        <thead><tr class="text-left">
            <th>Affiliate</th><th>Product</th><th>Sales Count</th><th>Commission</th><th>Conversion</th><th>Payout Status</th><th>Date</th>
        </tr></thead>
        <tbody>
            @foreach($records as $row)
            <tr>
                <td class="font-medium text-slate-800">{{ $row->affiliate ?? '—' }}</td>
                <td>{{ $row->product ?? '—' }}</td>
                <td data-order="{{ $row->sales_count ?? 0 }}">{{ $row->sales_count ?? 0 }}</td>
                <td data-order="{{ $row->commission ?? 0 }}">₹{{ number_format($row->commission ?? 0, 2) }}</td>
                <td>{{ $row->conversion ?? '—' }}</td>
                <td><x-badge :type="($row->payout_status ?? '') === 'Paid' ? 'success' : 'warning'">{{ $row->payout_status ?? '—' }}</x-badge></td>
                <td class="text-slate-500">{{ $row->date ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
