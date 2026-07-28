@extends('layouts.app')

@section('title', 'Automations')
@section('page-title', 'Automations')
@section('breadcrumb', 'Marketing / Automations')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-500">Trigger workflows on signup, webinars, inactivity, and more.</p>
        <a href="{{ route('admin.automations.create') }}" class="px-4 py-2 rounded-xl panel-btn-primary">New automation</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($workflows->count())
        <div class="overflow-x-auto">
            <table id="automationsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Trigger</th>
                        <th class="px-6 py-4">Runs</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workflows as $wf)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-800 font-medium">{{ $wf->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $triggers[$wf->trigger_key] ?? $wf->trigger_key }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $wf->run_count }}</td>
                        <td class="px-6 py-4"><x-badge :type="$wf->is_active ? 'success' : 'danger'">{{ $wf->is_active ? 'Active' : 'Off' }}</x-badge></td>
                        <td class="px-6 py-4 space-x-3 whitespace-nowrap">
                            <a href="{{ route('admin.automations.runs', $wf) }}" class="text-emerald-600 text-sm">Logs</a>
                            <form method="POST" action="{{ route('admin.automations.test', $wf) }}" class="inline">@csrf
                                <button class="text-sky-600 text-sm">Test</button>
                            </form>
                            <form method="POST" action="{{ route('admin.automations.destroy', $wf) }}" class="inline">@csrf @method('DELETE')
                                <button class="text-red-500 text-sm" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No automations yet." description="Create a workflow to automate marketing actions." :action="route('admin.automations.create')" actionLabel="New automation" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($workflows->count())
    <x-admin.datatable-scripts table-id="automationsTable" entity="automations" :order-column="0" order-direction="desc" :action-column="4" export-file-name="automations" />
@endif
@endpush
