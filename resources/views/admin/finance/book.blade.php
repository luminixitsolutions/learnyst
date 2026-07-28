@extends('layouts.app')
@section('title', ucfirst($type).' Book')
@section('page-title', ucfirst($type).' Book')
@section('breadcrumb', 'Finance / '.ucfirst($type).' Book')

@push('styles')
    <x-admin.datatable-styles />
@endpush

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
        <div class="ml-auto text-slate-800 font-bold">Balance: ₹{{ number_format($balance, 2) }}</div>
    </form>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($account)
            @if($entries->count())
            <div class="overflow-x-auto">
                <table id="bookTable" class="w-full text-sm panel-table display" style="width:100%">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Particulars</th>
                            <th class="px-6 py-4">Income</th>
                            <th class="px-6 py-4">Expense</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $e)
                        <tr class="hover:bg-indigo-50/40">
                            <td class="px-6 py-4 text-slate-500">{{ $e->entry_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-800">{{ $e->title }}</td>
                            <td class="px-6 py-4 text-emerald-600">{{ $e->entry_type==='income' ? '₹'.number_format($e->amount,2) : '—' }}</td>
                            <td class="px-6 py-4 text-red-500">{{ $e->entry_type==='expense' ? '₹'.number_format($e->amount,2) : '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <x-empty-state title="No movements." description="No entries for this account in the selected range." />
            @endif
        @else
        <x-empty-state title="No {{ $type }} account" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($account && $entries->count())
    <x-admin.datatable-scripts table-id="bookTable" entity="book entries" :order-column="0" order-direction="desc" export-file-name="{{ $type }}-book" />
@endif
@endpush
