@extends('layouts.app')

@section('title', 'Activity detail')
@section('page-title', 'Activity detail')
@section('breadcrumb', 'Platform Admin / Activity / Detail')

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('platform.activity.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Activity Monitor</a>

    <div class="glass-card rounded-2xl p-6 space-y-5">
        <div class="flex flex-wrap items-center gap-2">
            <x-badge type="info">{{ $log->action }}</x-badge>
            <span class="text-sm text-slate-500">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
        </div>

        <div>
            <h3 class="text-lg font-bold text-slate-800">{{ $log->description ?? 'No description' }}</h3>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500">User</dt>
                <dd class="font-medium text-slate-800">
                    @if($log->user)
                        <a href="{{ route('platform.users.show', $log->user) }}" class="text-indigo-600 hover:underline">{{ $log->user->name }}</a>
                        <div class="text-xs text-slate-400">{{ $log->user->email }}</div>
                    @else
                        System / anonymous
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-slate-500">Institute</dt>
                <dd class="font-medium text-slate-800">
                    @if($log->company)
                        <a href="{{ route('platform.companies.show', $log->company) }}" class="text-indigo-600 hover:underline">{{ $log->company->name }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-slate-500">Subject</dt>
                <dd class="font-medium text-slate-800">{{ $log->subjectLabel() }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">IP address</dt>
                <dd class="font-mono text-xs text-slate-700">{{ $log->ip_address ?? '—' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-slate-500">User agent</dt>
                <dd class="text-xs text-slate-600 break-all">{{ $log->user_agent ?? '—' }}</dd>
            </div>
        </dl>

        <div>
            <h4 class="text-sm font-semibold text-slate-800 mb-2">Properties (JSON)</h4>
            <pre class="rounded-xl bg-slate-900 text-slate-100 text-xs p-4 overflow-x-auto">{{ $log->properties ? json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{}' }}</pre>
        </div>

        @if($log->relationLoaded('subject') && $log->subject)
            <div>
                <h4 class="text-sm font-semibold text-slate-800 mb-2">Subject snapshot</h4>
                <pre class="rounded-xl bg-slate-50 border border-slate-200 text-xs p-4 overflow-x-auto text-slate-700">@php
                    try { echo e(json_encode($log->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); }
                    catch (\Throwable) { echo 'Unable to serialize subject.'; }
                @endphp</pre>
            </div>
        @endif
    </div>
</div>
@endsection
