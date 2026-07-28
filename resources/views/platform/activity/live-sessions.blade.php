@extends('layouts.app')

@section('title', 'Live Sessions')
@section('page-title', 'Live Sessions')
@section('breadcrumb', 'Platform Admin / Activity / Live Sessions')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Users with session activity in the last 15 minutes (database sessions).</p>
        <div class="flex gap-2">
            <a href="{{ route('platform.activity.index') }}" class="panel-btn-secondary text-sm">← Monitor</a>
            <a href="{{ route('platform.activity.live-sessions') }}" class="panel-btn-primary text-sm">Refresh</a>
        </div>
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        <div class="min-w-[200px] flex-1">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, email, IP…" class="panel-input w-full">
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
            <select name="role" class="panel-input w-full">
                <option value="">All roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->slug }}" @selected(request('role') === $role->slug)>{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="panel-btn-primary text-sm">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Who’s online</h3>
            <span class="text-sm text-slate-500">{{ $sessions->count() }} active</span>
        </div>
        @if($sessions->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">IP</th>
                        <th class="px-6 py-4">Last activity</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.users.show', $session->user) }}" class="font-medium text-indigo-600 hover:underline">{{ $session->user->name }}</a>
                                <div class="text-xs text-slate-400">{{ $session->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 capitalize">{{ str_replace('-', ' ', $session->user->role?->slug ?? '—') }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $session->user->company?->name ?? '—' }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $session->ip_address ?? '—' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $session->last_activity_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($session->user->id !== auth()->id())
                                    <form method="POST" action="{{ route('platform.activity.sessions.revoke', $session->id) }}" class="inline">
                                        @csrf
                                        <button class="text-xs font-semibold text-red-600">Revoke</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">Current</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <x-empty-state title="No live sessions in the last 15 minutes" />
        @endif
    </div>
</div>
@endsection
