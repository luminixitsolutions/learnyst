@extends('layouts.app')

@section('title', 'Follow-ups')
@section('page-title', 'Follow-ups')
@section('breadcrumb', 'CRM / Follow-ups')

@section('content')
<div class="space-y-6">
    <div class="flex gap-2">
        <a href="{{ route('admin.crm.follow-ups', ['scope'=>'today']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('scope')==='today' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300' }}">Today</a>
        <a href="{{ route('admin.crm.follow-ups', ['scope'=>'mine']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('scope')==='mine' ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300' }}">Mine</a>
        <a href="{{ route('admin.crm.follow-ups') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !request('scope') ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-300' }}">All pending</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Task</th>
                <th class="px-6 py-4">Lead</th>
                <th class="px-6 py-4">Due</th>
                <th class="px-6 py-4">Assignee</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @forelse($followUps as $fu)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $fu->title }}</td>
                    <td class="px-6 py-4"><a href="{{ route('admin.crm.leads.show', $fu->lead) }}" class="text-emerald-400">{{ $fu->lead?->name }}</a></td>
                    <td class="px-6 py-4 text-slate-400">{{ $fu->due_at?->format('M d, Y H:i') ?? '—' }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $fu->assignee?->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.crm.follow-ups.complete', $fu) }}">@csrf
                            <button class="text-sm text-emerald-400">Complete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No follow-ups.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $followUps->links() }}</div>
    </div>
</div>
@endsection
