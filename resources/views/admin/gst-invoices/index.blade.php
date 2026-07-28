@extends('layouts.app')

@section('title', 'GST Invoices')
@section('page-title', 'GST Invoices')
@section('breadcrumb', 'Sales / GST Invoices')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <p class="text-sm text-slate-500">Tax invoices with CGST/SGST (intra-state) or IGST (inter-state). Generate from a paid order.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Invoices" :value="number_format($stats['count'])" />
        <x-stat-card title="Issued" :value="number_format($stats['issued'])" />
        <x-stat-card title="Invoiced Total" :value="'₹' . number_format($stats['total'], 2)" />
        <x-stat-card title="Tax Collected" :value="'₹' . number_format($stats['tax'], 2)" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice #, order, learner..." class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach(['draft','issued','cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <button type="submit" class="panel-btn-primary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($invoices->count())
        <div class="overflow-x-auto">
            <table id="gstInvoicesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Order</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-emerald-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4">
                            <p class="text-slate-800">{{ $invoice->billing_name ?: $invoice->user?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $invoice->billing_email ?: $invoice->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($invoice->order)
                                <a href="{{ route('admin.orders.show', $invoice->order) }}" class="text-emerald-700">{{ $invoice->order->order_number }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500" data-order="{{ $invoice->invoice_date?->timestamp }}">{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 font-semibold text-emerald-700" data-order="{{ $invoice->total }}">₹{{ number_format($invoice->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($invoice->status) { 'issued' => 'success', 'cancelled' => 'danger', default => 'info' }">{{ $invoice->statusLabel() }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3">
                            <a href="{{ route('admin.gst-invoices.show', $invoice) }}" class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">View</a>
                            <a href="{{ route('admin.gst-invoices.download', $invoice) }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium" target="_blank">Print</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $invoices->links() }}</div>
        @else
        <x-empty-state title="No GST invoices" description="Open a paid order and click Generate GST Invoice." :action="route('admin.orders.index')" actionLabel="View Orders" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($invoices->count())
    <x-admin.datatable-scripts table-id="gstInvoicesTable" entity="gst-invoices" :order-column="3" order-direction="desc" :action-column="6" export-file-name="gst-invoices" />
@endif
@endpush
