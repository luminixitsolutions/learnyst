@extends('layouts.app')

@section('title', 'Wallet — ' . ($wallet->user?->name ?? 'Learner'))
@section('page-title', 'Wallet Details')
@section('breadcrumb', 'Sales / Wallets / Details')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $wallet->user?->name }}</h2>
            <p class="text-sm text-slate-500">{{ $wallet->user?->email }}</p>
        </div>
        <a href="{{ route('admin.wallets.index') }}" class="panel-btn-secondary">Back to wallets</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6 lg:col-span-1 space-y-4">
            <div>
                <p class="text-sm text-slate-500">Current Balance</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">₹{{ number_format($wallet->balance, 2) }}</p>
                <div class="mt-3"><x-badge :type="$wallet->is_frozen ? 'warning' : ($wallet->is_active ? 'success' : 'danger')">{{ $wallet->statusLabel() }}</x-badge></div>
            </div>

            <form method="POST" action="{{ route('admin.wallets.top-up', $wallet) }}" class="space-y-3 border-t border-slate-200 pt-4">
                @csrf
                <p class="text-sm font-semibold text-slate-800">Top-up</p>
                <input type="number" step="0.01" min="0.01" name="amount" required placeholder="Amount" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input type="text" name="notes" placeholder="Notes (optional)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="panel-btn-primary w-full">Add Credit</button>
            </form>

            <form method="POST" action="{{ route('admin.wallets.adjust', $wallet) }}" class="space-y-3 border-t border-slate-200 pt-4">
                @csrf
                <p class="text-sm font-semibold text-slate-800">Adjust Balance</p>
                <p class="text-xs text-slate-500">Use positive to credit, negative to debit.</p>
                <input type="number" step="0.01" name="amount" required placeholder="e.g. 100 or -50" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input type="text" name="notes" placeholder="Reason" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="panel-btn-secondary w-full">Apply Adjustment</button>
            </form>

            <form method="POST" action="{{ route('admin.wallets.freeze', $wallet) }}" class="space-y-3 border-t border-slate-200 pt-4">
                @csrf
                <input type="hidden" name="freeze" value="{{ $wallet->is_frozen ? 0 : 1 }}">
                <input type="text" name="notes" placeholder="Freeze/unfreeze note" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="w-full px-4 py-2 rounded-xl text-sm font-semibold {{ $wallet->is_frozen ? 'bg-emerald-600 text-white' : 'bg-amber-500/15 text-amber-700 hover:bg-amber-500/25' }}">
                    {{ $wallet->is_frozen ? 'Unfreeze Wallet' : 'Freeze Wallet' }}
                </button>
            </form>
        </div>

        <div class="glass-card rounded-2xl overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-bold text-slate-800">Ledger</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead>
                        <tr class="text-left text-slate-500">
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">Source</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Balance</th>
                            <th class="px-6 py-3">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td class="px-6 py-3 text-slate-500">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-3">
                                <x-badge :type="$txn->isCredit() ? 'success' : 'danger'">{{ ucfirst($txn->type) }}</x-badge>
                            </td>
                            <td class="px-6 py-3 text-slate-700">{{ $txn->sourceLabel() }}</td>
                            <td class="px-6 py-3 font-medium {{ $txn->isCredit() ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ $txn->isCredit() ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                            </td>
                            <td class="px-6 py-3">₹{{ number_format($txn->balance_after, 2) }}</td>
                            <td class="px-6 py-3 text-slate-500">{{ $txn->notes ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">No transactions yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</div>
@endsection
