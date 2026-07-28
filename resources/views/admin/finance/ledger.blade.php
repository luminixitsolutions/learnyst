@extends('layouts.app')
@section('title', 'Ledger')
@section('page-title', 'Income & Expense')
@section('breadcrumb', 'Finance / Ledger')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Add entry</h3>
        <form method="POST" action="{{ route('admin.finance.ledger.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Type" name="entry_type" type="select" required>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </x-form-input>
            <x-form-input label="Account" name="finance_account_id" type="select">
                <option value="">—</option>
                @foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->name }} ({{ $a->type }})</option>@endforeach
            </x-form-input>
            <x-form-input label="Category" name="category" placeholder="course_sales / rent / salary..." />
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Amount" name="amount" type="number" step="0.01" required />
            <x-form-input label="Date" name="entry_date" type="date" :value="now()->toDateString()" required />
            <x-form-input label="Payment mode" name="payment_mode" />
            <x-form-input label="Reference" name="reference" />
            <x-form-input label="Notes" name="description" type="textarea" class="md:col-span-3" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Save</button></div>
        </form>
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3">
        <x-form-input label="Type" name="type" type="select" :value="request('type')">
            <option value="">All</option>
            <option value="income">Income</option>
            <option value="expense">Expense</option>
        </x-form-input>
        <x-form-input label="From" name="from" type="date" :value="request('from')" />
        <x-form-input label="To" name="to" type="date" :value="request('to')" />
        <button class="self-end px-4 py-2.5 rounded-xl panel-btn-primary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Date</th><th class="px-6 py-4">Title</th><th class="px-6 py-4">Type</th>
                <th class="px-6 py-4">Account</th><th class="px-6 py-4">Amount</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($entries as $e)
                <tr>
                    <td class="px-6 py-4 text-slate-400">{{ $e->entry_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-white">{{ $e->title }}
                        @if($e->order)<div class="text-xs text-slate-500">Order {{ $e->order->order_number }}</div>@endif
                        @if($e->gst_invoice_id)<div class="text-xs text-emerald-500">GST linked</div>@endif
                    </td>
                    <td class="px-6 py-4 capitalize text-slate-400">{{ $e->entry_type }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $e->account?->name ?? '—' }}</td>
                    <td class="px-6 py-4 {{ $e->entry_type==='income'?'text-emerald-400':'text-red-400' }}">₹{{ number_format($e->amount,2) }}</td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.finance.ledger.destroy', $e) }}">@csrf @method('DELETE')
                            <button class="text-red-400 text-sm" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">No ledger entries.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $entries->links() }}</div>
    </div>
</div>
@endsection
