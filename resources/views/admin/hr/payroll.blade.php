@extends('layouts.app')
@section('title', 'Payroll')
@section('page-title', 'Payroll')
@section('breadcrumb', 'HR / Payroll')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.hr.payroll.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <x-form-input label="Year" name="year" type="number" :value="now()->year" required />
            <x-form-input label="Month" name="month" type="number" :value="now()->month" min="1" max="12" required />
            <x-form-input label="Notes" name="notes" />
            <div class="flex items-end"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Run payroll</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Period</th><th class="px-6 py-4">Status</th><th class="px-6 py-4">Slips</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($runs as $run)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $run->periodLabel() }}</td>
                    <td class="px-6 py-4"><x-badge type="info">{{ $run->status }}</x-badge></td>
                    <td class="px-6 py-4 text-slate-400">{{ $run->slips_count }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.hr.payroll.show', $run) }}" class="text-emerald-400 text-sm">View</a></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No payroll runs.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $runs->links() }}</div>
    </div>
</div>
@endsection
