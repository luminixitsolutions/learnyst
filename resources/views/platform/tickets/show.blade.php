@extends('layouts.app')

@section('title', 'Ticket #'.$ticket->id)
@section('page-title', 'Ticket #'.$ticket->id)
@section('breadcrumb', 'Platform Admin / Support / Tickets')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('platform.tickets.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Inbox</a>
        <div class="flex flex-wrap gap-2">
            @if($ticket->company?->is_active)
                <form method="POST" action="{{ route('platform.companies.enter-panel', $ticket->company) }}">@csrf
                    <button class="panel-btn-secondary text-sm">Open institute panel</button>
                </form>
            @endif
            @if($ticket->isClosed())
                <form method="POST" action="{{ route('platform.tickets.reopen', $ticket) }}">@csrf
                    <button class="panel-btn-primary text-sm">Reopen</button>
                </form>
            @else
                <form method="POST" action="{{ route('platform.tickets.close', $ticket) }}" onsubmit="return confirm('Close this ticket?')">@csrf
                    <button class="panel-btn-secondary text-sm text-rose-700">Close ticket</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h2 class="text-xl font-bold text-slate-800">{{ $ticket->subject }}</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500">Institute</dt>
                <dd class="font-medium">
                    @if($ticket->company)
                        <a href="{{ route('platform.companies.show', $ticket->company) }}" class="text-indigo-600 hover:underline">{{ $ticket->company->name }}</a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-slate-500">Requester</dt>
                <dd class="font-medium">{{ $ticket->requester?->name }} · {{ $ticket->requester?->email }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Created</dt>
                <dd class="font-medium">{{ $ticket->created_at?->format('M d, Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Closed</dt>
                <dd class="font-medium">{{ $ticket->closed_at?->format('M d, Y H:i') ?? '—' }}</dd>
            </div>
        </dl>

        <form method="POST" action="{{ route('platform.tickets.meta', $ticket) }}" class="flex flex-wrap gap-3 items-end border-t border-slate-100 pt-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="panel-input">
                    @foreach(['open','pending','closed'] as $st)
                        <option value="{{ $st }}" @selected($ticket->status === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Priority</label>
                <select name="priority" class="panel-input">
                    @foreach(['low','normal','high','urgent'] as $pr)
                        <option value="{{ $pr }}" @selected($ticket->priority === $pr)>{{ ucfirst($pr) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="panel-btn-secondary text-sm">Update</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="text-sm font-semibold text-slate-700">Conversation</h3>
        <div class="space-y-4">
            @forelse($ticket->messages as $message)
                <div class="rounded-xl border {{ $message->is_staff ? 'border-teal-200 bg-teal-50/50' : 'border-slate-200 bg-slate-50' }} p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <div class="text-sm font-semibold text-slate-800">
                            {{ $message->author?->name ?? 'Unknown' }}
                            @if($message->is_staff)
                                <span class="text-xs font-medium text-teal-700 ml-1">Staff</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400">{{ $message->created_at?->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="text-sm text-slate-700 whitespace-pre-line">{{ $message->body }}</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No messages yet.</p>
            @endforelse
        </div>
    </div>

    @unless($ticket->isClosed())
        <form method="POST" action="{{ route('platform.tickets.reply', $ticket) }}" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf
            <h3 class="text-sm font-semibold text-slate-700">Reply</h3>
            <textarea name="body" rows="5" required class="panel-input w-full" placeholder="Write a reply to the institute…"></textarea>
            @error('body')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            <button class="panel-btn-primary text-sm">Send reply</button>
        </form>
    @else
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
            Ticket is closed. Reopen to reply.
        </div>
    @endunless
</div>
@endsection
