@extends('layouts.app')
@section('title', ucfirst($type).' Book')
@section('page-title', ucfirst($type).' Book')
@section('breadcrumb', 'Finance / '.ucfirst($type).' Book')

@section('content')
<div class="space-y-6">
    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        <x-form-input label="Account" name="account_id" type="select" :value="$account?->id">
            @foreach($accounts as $a)
                <option value="{{ $a->id }}" @selected($account?->id==$a->id)>{{ $a->name }}</option>
            @endforeach
        </x-form-input>
        <x-form-input label="From" name="from" type="date" :value="request('from')" />
        <x-form-input label="To" name="to" type="date" :value="request('to')" />
        <button class="px-4 py-2.5 rounded-xl panel-btn-primary">View</button>
        <div class="ml-auto text-white font-bold">Balance: ₹{{ number_format($balance, 2) }}</div>
    </form>
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($account)
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Date</th><th class="px-6 py-4">Particulars</th><th class="px-6 py-4">Income</th><th class="px-6 py-4">Expense</th>
            </tr></thead>
            <tbody>
            @forelse($entries as $e)
                <tr>
                    <td class="px-6 py-4 text-slate-400">{{ $e->entry_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-white">{{ $e->title }}</td>
                    <td class="px-6 py-4 text-emerald-400">{{ $e->entry_type==='income' ? '₹'.number_format($e->amount,2) : '—' }}</td>
                    <td class="px-6 py-4 text-red-400">{{ $e->entry_type==='expense' ? '₹'.number_format($e->amount,2) : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No movements.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $entries->links() }}</div>
        @else
        <x-empty-state title="No {{ $type }} account" />
        @endif
    </div>
</div>
@endsection
