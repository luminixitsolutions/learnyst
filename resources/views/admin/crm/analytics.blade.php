@extends('layouts.app')

@section('title', 'CRM Analytics')
@section('page-title', 'CRM Analytics')
@section('breadcrumb', 'CRM / Analytics')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-4">Leads by source</h3>
            @forelse($bySource as $row)
            <div class="flex justify-between py-2 border-b border-slate-700/40 text-sm">
                <span class="text-slate-300">{{ $row->source ?: 'unknown' }}</span>
                <span class="text-white font-semibold">{{ $row->total }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500">No data.</p>
            @endforelse
        </div>
        <div class="glass-card rounded-2xl p-6">
            <h3 class="font-bold text-white mb-4">Stage conversion</h3>
            @foreach($stages as $key => $label)
            <div class="flex justify-between py-2 border-b border-slate-700/40 text-sm">
                <span class="text-slate-300">{{ $label }}</span>
                <span class="text-white font-semibold">{{ $byStage[$key]->total ?? 0 }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700"><h3 class="font-bold text-white">Counselor performance</h3></div>
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Counselor</th>
                <th class="px-6 py-4">Assigned</th>
                <th class="px-6 py-4">Admitted</th>
                <th class="px-6 py-4">Conv. rate</th>
            </tr></thead>
            <tbody>
                @forelse($counselorPerf as $row)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $row->counselor?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $row->total }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $row->converted }}</td>
                    <td class="px-6 py-4 text-emerald-400">{{ $row->total ? round(($row->converted / $row->total) * 100, 1) : 0 }}%</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No assignments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
