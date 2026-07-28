@extends('layouts.app')

@section('title', 'Automation Runs')
@section('page-title', 'Run Logs')
@section('breadcrumb', 'Marketing / Automations / Logs')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between">
        <h3 class="text-white font-bold">{{ $automation->name }}</h3>
        <a href="{{ route('admin.automations.index') }}" class="text-emerald-400 text-sm">← Back</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">When</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Result</th>
                <th class="px-6 py-4">Error</th>
            </tr></thead>
            <tbody>
                @forelse($runs as $run)
                <tr>
                    <td class="px-6 py-4 text-slate-400">{{ $run->created_at->format('M d H:i') }}</td>
                    <td class="px-6 py-4"><x-badge type="info">{{ $run->status }}</x-badge></td>
                    <td class="px-6 py-4 text-xs text-slate-400 max-w-md truncate">{{ json_encode($run->result) }}</td>
                    <td class="px-6 py-4 text-xs text-red-400">{{ $run->error ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No runs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $runs->links() }}</div>
    </div>
</div>
@endsection
