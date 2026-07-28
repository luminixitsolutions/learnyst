@extends('layouts.app')
@section('title', 'Payroll '.$run->periodLabel())
@section('page-title', 'Payroll '.$run->periodLabel())
@section('breadcrumb', 'HR / Payroll')

@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm panel-table">
        <thead><tr class="text-left">
            <th class="px-6 py-4">Employee</th><th class="px-6 py-4">Present</th><th class="px-6 py-4">Net</th><th class="px-6 py-4"></th>
        </tr></thead>
        <tbody>
        @foreach($run->slips as $slip)
            <tr>
                <td class="px-6 py-4 text-white">{{ $slip->employee?->name }}<div class="text-xs text-slate-500">{{ $slip->slip_number }}</div></td>
                <td class="px-6 py-4 text-slate-400">{{ $slip->present_days }}d / leave {{ $slip->leave_days }}</td>
                <td class="px-6 py-4 text-emerald-400">₹{{ number_format($slip->net_pay,2) }}</td>
                <td class="px-6 py-4"><a href="{{ route('admin.hr.slips.show', $slip) }}" target="_blank" class="text-emerald-400 text-sm">Slip PDF</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
