@extends('layouts.app')

@section('title', 'Wallet Transactions')
@section('page-title', 'Wallet Transactions')
@section('breadcrumb', 'Sales / Wallets / Transactions')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Institute-wide wallet credit and debit ledger.</p>
        <a href="{{ route('admin.wallets.index') }}" class="panel-btn-secondary">Back to wallets</a>
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search learner or code..." class="lg:col-span-2 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <select name="type" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All types</option>
            <option value="credit" @selected(request('type') === 'credit')>Credit</option>
            <option value="debit" @selected(request('type') === 'debit')>Debit</option>
        </select>
        <select name="source" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All sources</option>
            @foreach(['manual','topup','adjustment','order_payment','refund','referral_bonus','signup_reward','affiliate'] as $src)
                <option value="{{ $src }}" @selected(request('source') === $src)>{{ str_replace('_', ' ', ucfirst($src)) }}</option>
            @endforeach
        </select>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <button type="submit" class="panel-btn-primary lg:col-span-6 sm:col-span-2">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($transactions->count())
        <div class="overflow-x-auto">
            <table id="walletTxnTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Balance After</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $txn)
                    <tr>
                        <td class="px-6 py-4" data-order="{{ $txn->created_at->timestamp }}">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $txn->user?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $txn->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4"><x-badge :type="$txn->isCredit() ? 'success' : 'danger'">{{ ucfirst($txn->type) }}</x-badge></td>
                        <td class="px-6 py-4">{{ $txn->sourceLabel() }}</td>
                        <td class="px-6 py-4 font-medium {{ $txn->isCredit() ? 'text-emerald-700' : 'text-red-600' }}" data-order="{{ $txn->amount }}">
                            {{ $txn->isCredit() ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                        </td>
                        <td class="px-6 py-4">₹{{ number_format($txn->balance_after, 2) }}</td>
                        <td class="px-6 py-4">{{ ucfirst($txn->status) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $transactions->links() }}</div>
        @else
        <x-empty-state title="No transactions" description="Wallet ledger entries will appear here after credits or debits." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($transactions->count())
    <x-admin.datatable-scripts table-id="walletTxnTable" entity="wallet transactions" :order-column="0" order-direction="desc" export-file-name="wallet-transactions" />
@endif
@endpush
