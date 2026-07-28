@extends('layouts.app')

@section('title', 'Automations')
@section('page-title', 'Automations')
@section('breadcrumb', 'Marketing / Automations')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-400">Trigger workflows on signup, webinars, inactivity, and more.</p>
        <a href="{{ route('admin.automations.create') }}" class="px-4 py-2 rounded-xl panel-btn-primary">New automation</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Trigger</th>
                <th class="px-6 py-4">Runs</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @forelse($workflows as $wf)
                <tr>
                    <td class="px-6 py-4 text-white font-medium">{{ $wf->name }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $triggers[$wf->trigger_key] ?? $wf->trigger_key }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $wf->run_count }}</td>
                    <td class="px-6 py-4"><x-badge :type="$wf->is_active ? 'success' : 'danger'">{{ $wf->is_active ? 'Active' : 'Off' }}</x-badge></td>
                    <td class="px-6 py-4 space-x-3 whitespace-nowrap">
                        <a href="{{ route('admin.automations.runs', $wf) }}" class="text-emerald-400 text-sm">Logs</a>
                        <form method="POST" action="{{ route('admin.automations.test', $wf) }}" class="inline">@csrf
                            <button class="text-sky-400 text-sm">Test</button>
                        </form>
                        <form method="POST" action="{{ route('admin.automations.destroy', $wf) }}" class="inline">@csrf @method('DELETE')
                            <button class="text-red-400 text-sm" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No automations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $workflows->links() }}</div>
    </div>
</div>
@endsection
