@extends('layouts.app')

@section('title', 'My Wallet')
@section('page-title', 'My Wallet')
@section('breadcrumb', 'Student Panel / Wallet')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-slate-900 to-emerald-900 text-white">
        <p class="text-sm text-white/70">Available balance</p>
        <h2 class="text-3xl font-bold mt-1">₹{{ number_format($wallet->balance, 2) }}</h2>
        <p class="text-sm text-white/80 mt-2">
            Status: {{ $wallet->statusLabel() }}.
            Use wallet credits at checkout when enabled by your institute.
        </p>
        <div class="mt-4">
            <a href="{{ route('learner.dashboard') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-semibold">Back to dashboard</a>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Transaction History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left text-slate-500">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Details</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td class="px-6 py-3 text-slate-500">{{ $txn->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-3"><x-badge :type="$txn->isCredit() ? 'success' : 'danger'">{{ ucfirst($txn->type) }}</x-badge></td>
                        <td class="px-6 py-3 text-slate-700">{{ $txn->sourceLabel() }}@if($txn->notes) — {{ $txn->notes }}@endif</td>
                        <td class="px-6 py-3 font-medium {{ $txn->isCredit() ? 'text-emerald-700' : 'text-red-600' }}">
                            {{ $txn->isCredit() ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                        </td>
                        <td class="px-6 py-3">₹{{ number_format($txn->balance_after, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">No wallet activity yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
