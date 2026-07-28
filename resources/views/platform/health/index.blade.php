@extends('layouts.app')

@section('title', 'System Health')
@section('page-title', 'System Health')
@section('breadcrumb', 'Platform Admin / System / Health')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-500">Safe diagnostics and cache actions only — no migrate, down, or optimize:force.</p>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Failed jobs" :value="number_format($failedJobsCount)" />
        <x-stat-card title="Queue" :value="$info['queue']" />
        <x-stat-card title="Cache" :value="$info['cache']" />
        <x-stat-card title="PHP" :value="$info['php']" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Environment</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            @foreach($info as $label => $value)
                <div class="flex justify-between gap-4 border-b border-slate-100 py-2">
                    <dt class="text-slate-500 capitalize">{{ str_replace('_', ' ', $label) }}</dt>
                    <dd class="font-medium text-slate-800">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-4">Safe actions</h3>
        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('platform.health.clear-cache') }}">@csrf
                <button class="panel-btn-primary text-sm" onclick="return confirm('Clear application cache?')">Clear app cache</button>
            </form>
            <form method="POST" action="{{ route('platform.health.clear-config') }}">@csrf
                <button class="panel-btn-secondary text-sm">Clear config cache</button>
            </form>
            <form method="POST" action="{{ route('platform.health.clear-views') }}">@csrf
                <button class="panel-btn-secondary text-sm">Clear view cache</button>
            </form>
            <form method="POST" action="{{ route('platform.health.clear-routes') }}">@csrf
                <button class="panel-btn-secondary text-sm">Clear route cache</button>
            </form>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-sm font-semibold text-slate-700">Recent failed jobs</h3>
        </div>
        @if($failedJobs->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-3">Failed at</th>
                            <th class="px-6 py-3">Queue</th>
                            <th class="px-6 py-3">Exception</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($failedJobs as $job)
                            <tr>
                                <td class="px-6 py-3 whitespace-nowrap text-slate-500">{{ $job->failed_at }}</td>
                                <td class="px-6 py-3">{{ $job->queue }}</td>
                                <td class="px-6 py-3">
                                    <code class="text-xs text-rose-700 break-all">{{ \Illuminate\Support\Str::limit(strtok((string) $job->exception, "\n"), 160) }}</code>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8"><x-empty-state title="No failed jobs" /></div>
        @endif
    </div>
</div>
@endsection
