@extends('layouts.app')

@section('title', 'Wallets')
@section('page-title', 'Learner Wallets')
@section('breadcrumb', 'Sales / Wallets')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage learner wallet balances, top-ups, freezes, and ledger activity.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.wallets.transactions') }}" class="panel-btn-secondary">All Transactions</a>
            <a href="{{ route('admin.wallets.settings') }}" class="panel-btn-secondary">Settings</a>
            <a href="{{ route('admin.wallets.create') }}" class="panel-btn-primary">Create Wallet</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <x-stat-card title="Total Balance" :value="'₹' . number_format($stats['total_balance'], 2)" />
        <x-stat-card title="Wallets" :value="number_format($stats['wallet_count'])" />
        <x-stat-card title="Frozen" :value="number_format($stats['frozen_count'])" />
        <x-stat-card title="Credits (Month)" :value="'₹' . number_format($stats['credits_month'], 2)" />
        <x-stat-card title="Debits (Month)" :value="'₹' . number_format($stats['debits_month'], 2)" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search learner..." class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="frozen" @selected(request('status') === 'frozen')>Frozen</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="panel-btn-primary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($wallets->count())
        <div class="overflow-x-auto">
            <table id="walletsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Balance</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Transactions</th>
                        <th class="px-6 py-4">Updated</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($wallets as $wallet)
                    <tr class="hover:bg-emerald-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $wallet->user?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $wallet->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4 font-semibold text-emerald-700" data-order="{{ $wallet->balance }}">₹{{ number_format($wallet->balance, 2) }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$wallet->is_frozen ? 'warning' : ($wallet->is_active ? 'success' : 'danger')">{{ $wallet->statusLabel() }}</x-badge>
                        </td>
                        <td class="px-6 py-4">{{ $wallet->transactions_count }}</td>
                        <td class="px-6 py-4 text-slate-500" data-order="{{ $wallet->updated_at?->timestamp }}">{{ $wallet->updated_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.wallets.show', $wallet) }}" class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $wallets->links() }}</div>
        @else
        <x-empty-state title="No wallets yet" description="Create a wallet for a learner to start tracking credits and debits." :action="route('admin.wallets.create')" actionLabel="Create Wallet" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($wallets->count())
    <x-admin.datatable-scripts table-id="walletsTable" entity="wallets" :order-column="1" order-direction="desc" :action-column="5" export-file-name="wallets" />
@endif
@endpush
