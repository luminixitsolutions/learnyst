<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PlatformTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::query()
            ->with(['company', 'requester'])
            ->withCount('messages')
            ->latest('last_reply_at')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', (int) $request->company_id);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('requester', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $stats = [
            'open' => SupportTicket::where('status', 'open')->count(),
            'pending' => SupportTicket::where('status', 'pending')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->where('status', '!=', 'closed')->count(),
        ];

        $tickets = $query->paginate(25)->withQueryString();
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('platform.tickets.index', compact('tickets', 'stats', 'companies'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get(['id', 'name', 'owner_user_id']);

        return view('platform.tickets.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'company_id' => ['required', 'exists:companies,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $requesterId = $validated['user_id'] ?? $company->owner_user_id;

        $ticket = SupportTicket::create([
            'subject' => $validated['subject'],
            'company_id' => $company->id,
            'user_id' => $requesterId,
            'status' => 'open',
            'priority' => $validated['priority'],
            'last_reply_at' => now(),
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $requesterId ?: Auth::id(),
            'body' => $validated['body'],
            'is_staff' => false,
        ]);

        ActivityLogger::log('support_ticket_created', "Support ticket #{$ticket->id}: {$ticket->subject}", $ticket, [
            'company_id' => $company->id,
        ]);

        return redirect()
            ->route('platform.tickets.show', $ticket)
            ->with('success', 'Ticket created.');
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['company', 'requester', 'closer', 'messages.author']);

        return view('platform.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        if ($ticket->isClosed()) {
            return back()->with('error', 'This ticket is closed. Reopen it before replying.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:10000'],
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'is_staff' => true,
        ]);

        $ticket->update([
            'status' => $ticket->status === 'open' ? 'pending' : $ticket->status,
            'last_reply_at' => now(),
        ]);

        ActivityLogger::log('support_ticket_replied', "Replied to ticket #{$ticket->id}", $ticket, [
            'company_id' => $ticket->company_id,
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function close(SupportTicket $ticket)
    {
        if ($ticket->isClosed()) {
            return back()->with('error', 'Ticket is already closed.');
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        ActivityLogger::log('support_ticket_closed', "Closed ticket #{$ticket->id}: {$ticket->subject}", $ticket, [
            'company_id' => $ticket->company_id,
        ]);

        return back()->with('success', 'Ticket closed.');
    }

    public function reopen(SupportTicket $ticket)
    {
        if (! $ticket->isClosed()) {
            return back()->with('error', 'Ticket is not closed.');
        }

        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
            'closed_by' => null,
        ]);

        ActivityLogger::log('support_ticket_reopened', "Reopened ticket #{$ticket->id}", $ticket, [
            'company_id' => $ticket->company_id,
        ]);

        return back()->with('success', 'Ticket reopened.');
    }

    public function updateMeta(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['open', 'pending', 'closed'])],
        ]);

        $payload = [
            'priority' => $validated['priority'],
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'closed' && ! $ticket->isClosed()) {
            $payload['closed_at'] = now();
            $payload['closed_by'] = Auth::id();
        }

        if ($validated['status'] !== 'closed') {
            $payload['closed_at'] = null;
            $payload['closed_by'] = null;
        }

        $ticket->update($payload);

        ActivityLogger::log('support_ticket_updated', "Updated ticket #{$ticket->id}", $ticket, [
            'company_id' => $ticket->company_id,
        ]);

        return back()->with('success', 'Ticket updated.');
    }
}
