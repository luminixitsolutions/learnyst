@extends('layouts.app')

@section('title', 'Segments')
@section('page-title', 'Segments')
@section('breadcrumb', 'Classification / Segments')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.classification.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Segments</h3>
            <p class="text-sm text-slate-500 mt-1">Create segments and add learners or products within them.</p>
        </div>
        <a href="{{ route('admin.segments.create') }}" class="panel-btn-primary">Create Segment</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($segments->count())
        <div class="overflow-x-auto">
            <table id="segmentsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Learners</th>
                        <th class="px-6 py-4">Products</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($segments as $segment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $segment->title }}</p>
                            @if($segment->description)
                                <p class="text-xs text-slate-500 truncate max-w-xs mt-0.5">{{ $segment->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $segment->users_count }}</td>
                        <td class="px-6 py-4">{{ $segment->courses_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $segment->created_at->timestamp }}">{{ $segment->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.segments.show', $segment)"
                                :delete-url="route('admin.segments.destroy', $segment)"
                                edit-title="Manage segment"
                                delete-title="Delete segment"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No segments yet" description="Create segments and add learners or products within." :action="route('admin.segments.create')" actionLabel="Create Segment" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($segments->count())
    <x-admin.datatable-scripts table-id="segmentsTable" entity="segments" :order-column="3" order-direction="desc" :action-column="4" export-file-name="segments" />
@endif
@endpush
