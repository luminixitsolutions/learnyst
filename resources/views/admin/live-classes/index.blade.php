@extends('layouts.app')

@section('title', 'Live Classes')
@section('page-title', 'Live Classes')
@section('breadcrumb', 'Course Management / Live Classes')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Schedule and manage live classroom sessions.</p>
        <a href="{{ route('admin.live-classes.create') }}" class="panel-btn-primary">Schedule Class</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($classes->count())
        <div class="overflow-x-auto">
            <table id="liveClassesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Instructor</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Platform</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $class->title }}</td>
                        <td class="px-6 py-4">{{ $class->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $class->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ optional($class->starts_at)->timestamp ?? 0 }}">
                            {{ $class->starts_at?->format('M d, Y H:i') ?? '—' }}
                        </td>
                        <td class="px-6 py-4">{{ str_replace('_', ' ', ucfirst($class->platform ?? 'zoom')) }}</td>
                        <td class="px-6 py-4" data-order="{{ $class->status ?? 'scheduled' }}">
                            <x-badge type="info">{{ ucfirst($class->status ?? 'scheduled') }}</x-badge>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.live-classes.attendance', $class) }}"
                                   class="action-icon-btn action-icon-btn--edit"
                                   title="Mark attendance">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </a>
                                <a href="{{ route('admin.live-classes.edit', $class) }}"
                                   class="action-icon-btn action-icon-btn--edit"
                                   title="Edit live class">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.live-classes.destroy', $class) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="action-icon-btn action-icon-btn--delete"
                                            title="Delete live class"
                                            @click="deleteForm = $el.closest('form'); deleteModal = true">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No live classes scheduled" description="Create your first live class session." :action="route('admin.live-classes.create')" actionLabel="Schedule Class" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($classes->count())
    <x-admin.datatable-scripts
        table-id="liveClassesTable"
        entity="live classes"
        :order-column="3"
        order-direction="desc"
        :action-column="6"
        export-file-name="live-classes"
    />
@endif
@endpush
