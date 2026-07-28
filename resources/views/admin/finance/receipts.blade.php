@extends('layouts.app')
@section('title', 'Receipts')
@section('page-title', 'Receipts')
@section('breadcrumb', 'Finance / Receipts')

@section('content')
<div class="space-y-4">
    <p class="text-sm text-slate-400">Payment acknowledgements (RCPT-…). Tax invoices use existing GST numbering (INV-…) under Sales → GST Invoices.</p>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Receipt #</th><th class="px-6 py-4">Date</th><th class="px-6 py-4">Payer</th><th class="px-6 py-4">Amount</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($receipts as $r)
                <tr>
                    <td class="px-6 py-4 text-white font-mono">{{ $r->receipt_number }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $r->receipt_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $r->payer_name ?? $r->user?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-emerald-400">₹{{ number_format($r->amount,2) }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.finance.receipts.show', $r) }}" class="text-emerald-400 text-sm" target="_blank">Print</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No receipts. Sync payments from dashboard.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $receipts->links() }}</div>
    </div>
</div>
@endsection
