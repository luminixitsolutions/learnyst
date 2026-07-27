@extends('layouts.app')

@section('title', 'Polls')
@section('page-title', 'Polls')
@section('breadcrumb', 'Products')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
        Polls created here can be used in SuperLive to boost live class engagement.
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Polls</h3>
            <p class="text-sm text-slate-500 mt-1">Create and manage polls to engage your audience.</p>
        </div>
        <a href="{{ route('admin.polls.create') }}" class="panel-btn-primary">Create Poll</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($polls->count())
        <div class="overflow-x-auto">
            <table id="pollsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Poll Type</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($polls as $poll)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $poll->title }}</p>
                            @if($poll->description)
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $poll->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $poll->pollTypeLabel() }}</td>
                        <td class="px-6 py-4">{{ ucfirst($poll->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $poll->created_at->timestamp }}">{{ $poll->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions :delete-url="route('admin.polls.destroy', $poll)" delete-title="Delete poll" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No polls yet" description="Boost engagement with interactive polls." :action="route('admin.polls.create')" actionLabel="Create Poll" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($polls->count())
    <x-admin.datatable-scripts table-id="pollsTable" entity="polls" :order-column="3" order-direction="desc" :action-column="4" export-file-name="polls" />
@endif
@endpush
