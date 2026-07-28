@extends('layouts.app')
@section('title', 'Finance Dashboard')
@section('page-title', 'Finance')
@section('breadcrumb', 'Finance / Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-3 items-end">
        <form method="GET" class="flex flex-wrap gap-3 items-end glass-card rounded-2xl p-4">
            <x-form-input label="From" name="from" type="date" :value="$from" />
            <x-form-input label="To" name="to" type="date" :value="$to" />
            <button class="px-4 py-2.5 rounded-xl panel-btn-primary">Apply</button>
        </form>
        <form method="POST" action="{{ route('admin.finance.sync') }}" class="glass-card rounded-2xl p-4">
            @csrf
            <button class="px-4 py-2.5 rounded-xl text-sm text-sky-400">Sync paid orders → income</button>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Income', '₹'.number_format($pnl['income'], 2)],
            ['Expense', '₹'.number_format($pnl['expense'], 2)],
            ['Net profit', '₹'.number_format($pnl['profit'], 2)],
            ['GST invoices', $gstCount],
        ] as [$label, $val])
        <div class="glass-card rounded-2xl p-5">
            <p class="text-xs text-slate-400">{{ $label }}</p>
            <p class="text-2xl font-bold text-white mt-1">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-3">Assets snapshot</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-slate-300"><span>Cash</span><span>₹{{ number_format($sheet['assets']['cash'], 2) }}</span></div>
                <div class="flex justify-between text-slate-300"><span>Bank</span><span>₹{{ number_format($sheet['assets']['bank'], 2) }}</span></div>
                <div class="flex justify-between text-white font-semibold border-t border-slate-700 pt-2"><span>Total</span><span>₹{{ number_format($sheet['assets']['total'], 2) }}</span></div>
            </div>
        </div>
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-700 font-bold text-white">Recent ledger</div>
            <table class="w-full text-sm panel-table">
                <tbody>
                @forelse($recent as $e)
                    <tr>
                        <td class="px-4 py-3 text-white">{{ $e->title }}</td>
                        <td class="px-4 py-3 text-slate-400 capitalize">{{ $e->entry_type }}</td>
                        <td class="px-4 py-3 {{ $e->entry_type==='income'?'text-emerald-400':'text-red-400' }}">₹{{ number_format($e->amount,2) }}</td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-8 text-center text-slate-500" colspan="3">No entries yet. Sync payments or add manually.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
