@extends('layouts.app')
@section('title', 'Branch Reports')
@section('page-title', 'Branch Reports')
@section('breadcrumb', 'Branches / Reports')

@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm panel-table">
        <thead><tr class="text-left">
            <th class="px-6 py-4">Branch</th><th class="px-6 py-4">Learners</th><th class="px-6 py-4">Revenue</th>
            <th class="px-6 py-4">Branch share</th><th class="px-6 py-4">HQ share</th>
        </tr></thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td class="px-6 py-4 text-white">{{ $row['branch']->name }}</td>
                <td class="px-6 py-4 text-slate-400">{{ $row['learners'] }}</td>
                <td class="px-6 py-4 text-slate-400">₹{{ number_format($row['revenue'],2) }}</td>
                <td class="px-6 py-4 text-emerald-400">₹{{ number_format($row['branch_share'],2) }}</td>
                <td class="px-6 py-4 text-sky-400">₹{{ number_format($row['hq_share'],2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No branch data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
