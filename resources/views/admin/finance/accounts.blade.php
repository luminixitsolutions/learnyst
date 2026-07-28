@extends('layouts.app')
@section('title', 'Accounts')
@section('page-title', 'Cash & Bank Accounts')
@section('breadcrumb', 'Finance / Accounts')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.finance.accounts.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Type" name="type" type="select" required>
                <option value="cash">Cash</option>
                <option value="bank">Bank</option>
                <option value="other">Other</option>
            </x-form-input>
            <x-form-input label="Opening balance" name="opening_balance" type="number" step="0.01" :value="0" />
            <x-form-input label="Bank name" name="bank_name" />
            <x-form-input label="Account number" name="account_number" />
            <x-form-input label="IFSC" name="ifsc" />
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Add account</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($accounts->count())
        <div class="overflow-x-auto">
            <table id="accountsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Opening</th>
                        <th class="px-6 py-4">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $a)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $a->name }}</td>
                        <td class="px-6 py-4 text-slate-600 capitalize">{{ $a->type }}</td>
                        <td class="px-6 py-4 text-slate-600">₹{{ number_format($a->opening_balance,2) }}</td>
                        <td class="px-6 py-4 text-emerald-600 font-semibold">₹{{ number_format($a->balance(),2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No accounts yet." description="Add a cash or bank account above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($accounts->count())
    <x-admin.datatable-scripts table-id="accountsTable" entity="accounts" :order-column="0" order-direction="asc" export-file-name="finance-accounts" />
@endif
@endpush
