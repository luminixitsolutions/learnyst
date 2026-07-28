@extends('layouts.app')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')
@section('breadcrumb', 'Platform Admin / Support / Tickets')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Inbox for institute support tickets.</p>
        <a href="{{ route('platform.tickets.create') }}" class="panel-btn-primary text-sm">New ticket</a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Open" :value="number_format($stats['open'])" />
        <x-stat-card title="Pending" :value="number_format($stats['pending'])" />
        <x-stat-card title="Closed" :value="number_format($stats['closed'])" />
        <x-stat-card title="Urgent (open)" :value="number_format($stats['urgent'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" class="panel-input w-full" placeholder="Subject, requester, institute…">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute</label>
            <select name="company_id" class="panel-input w-full">
                <option value="">All</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['open','pending','closed'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Priority</label>
            <select name="priority" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['low','normal','high','urgent'] as $pr)
                    <option value="{{ $pr }}" @selected(request('priority') === $pr)>{{ ucfirst($pr) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.tickets.index') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($tickets->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Ticket</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Requester</th>
                        <th class="px-6 py-4">Priority</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Updated</th>
                        <th class="px-6 py-4 text-right">Open</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        @php
                            $statusBadge = match ($ticket->status) {
                                'open' => 'success',
                                'pending' => 'warning',
                                'closed' => 'default',
                                default => 'info',
                            };
                            $priorityBadge = match ($ticket->priority) {
                                'urgent' => 'danger',
                                'high' => 'warning',
                                'low' => 'info',
                                default => 'default',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.tickets.show', $ticket) }}" class="font-medium text-indigo-600 hover:underline">#{{ $ticket->id }} · {{ $ticket->subject }}</a>
                                <div class="text-xs text-slate-400">{{ $ticket->messages_count }} message(s)</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->company)
                                    <a href="{{ route('platform.companies.show', $ticket->company) }}" class="text-indigo-600 hover:underline">{{ $ticket->company->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $ticket->requester?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $ticket->requester?->email }}</div>
                            </td>
                            <td class="px-6 py-4"><x-badge :type="$priorityBadge">{{ $ticket->priority }}</x-badge></td>
                            <td class="px-6 py-4"><x-badge :type="$statusBadge">{{ $ticket->status }}</x-badge></td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ ($ticket->last_reply_at ?? $ticket->updated_at)?->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('platform.tickets.show', $ticket) }}" class="text-xs font-semibold text-indigo-600">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $tickets->links() }}</div>
        @else
            <x-empty-state title="No tickets found" />
        @endif
    </div>
</div>
@endsection
