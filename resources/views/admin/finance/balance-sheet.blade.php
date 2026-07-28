@extends('layouts.app')
@section('title', 'Balance Sheet')
@section('page-title', 'Balance Sheet Summary')
@section('breadcrumb', 'Finance / Balance Sheet')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-white mb-4">Assets</h3>
        <div class="flex justify-between text-sm py-2 text-slate-300"><span>Cash</span><span>₹{{ number_format($sheet['assets']['cash'],2) }}</span></div>
        <div class="flex justify-between text-sm py-2 text-slate-300"><span>Bank</span><span>₹{{ number_format($sheet['assets']['bank'],2) }}</span></div>
        <div class="flex justify-between font-bold text-white border-t border-slate-700 pt-3 mt-2"><span>Total assets</span><span>₹{{ number_format($sheet['assets']['total'],2) }}</span></div>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-white mb-4">Equity</h3>
        <div class="flex justify-between text-sm py-2 text-slate-300"><span>Retained earnings (P&L)</span><span>₹{{ number_format($sheet['equity']['retained_earnings'],2) }}</span></div>
        <div class="flex justify-between font-bold text-white border-t border-slate-700 pt-3 mt-2"><span>Total equity</span><span>₹{{ number_format($sheet['equity']['total'],2) }}</span></div>
        <p class="text-xs text-slate-500 mt-4">Simplified summary for institute ops — not a full statutory balance sheet.</p>
    </div>
</div>
@endsection
