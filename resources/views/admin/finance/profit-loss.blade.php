@extends('layouts.app')
@section('title', 'Profit & Loss')
@section('page-title', 'Profit & Loss')
@section('breadcrumb', 'Finance / P&L')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex gap-3 glass-card rounded-2xl p-4 items-end">
        <x-form-input label="From" name="from" type="date" :value="$from" />
        <x-form-input label="To" name="to" type="date" :value="$to" />
        <button class="px-4 py-2.5 rounded-xl panel-btn-primary">Refresh</button>
    </form>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-3">Income</h3>
            @forelse($pnl['income_by_category'] as $cat => $total)
                <div class="flex justify-between text-sm py-2 border-b border-slate-700/40"><span class="text-slate-300">{{ $cat ?: 'uncategorized' }}</span><span class="text-emerald-400">₹{{ number_format($total,2) }}</span></div>
            @empty
                <p class="text-slate-500 text-sm">No income in range.</p>
            @endforelse
            <div class="flex justify-between font-bold text-white mt-3"><span>Total</span><span>₹{{ number_format($pnl['income'],2) }}</span></div>
        </div>
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-3">Expenses</h3>
            @forelse($pnl['expense_by_category'] as $cat => $total)
                <div class="flex justify-between text-sm py-2 border-b border-slate-700/40"><span class="text-slate-300">{{ $cat ?: 'uncategorized' }}</span><span class="text-red-400">₹{{ number_format($total,2) }}</span></div>
            @empty
                <p class="text-slate-500 text-sm">No expenses in range.</p>
            @endforelse
            <div class="flex justify-between font-bold text-white mt-3"><span>Total</span><span>₹{{ number_format($pnl['expense'],2) }}</span></div>
        </div>
    </div>
    <div class="glass-card rounded-2xl p-6 text-center">
        <p class="text-slate-400 text-sm">Net profit / loss</p>
        <p class="text-3xl font-bold {{ $pnl['profit']>=0?'text-emerald-400':'text-red-400' }}">₹{{ number_format($pnl['profit'],2) }}</p>
    </div>
</div>
@endsection
