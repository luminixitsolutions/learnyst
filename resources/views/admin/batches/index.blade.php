@extends('layouts.app')

@section('title', 'Batches')
@section('page-title', 'Batches')
@section('breadcrumb', 'Manage batches')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Manage course batches, schedules, and enrolled learners.</p>
        <a href="{{ route('admin.batches.create') }}" class="panel-btn-primary">Create Batch</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($batches->count())
        <div class="overflow-x-auto">
            <table id="batchesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Batch</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Instructor</th>
                        <th class="px-6 py-4">Dates</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($batches as $batch)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $batch->title }}</td>
                        <td class="px-6 py-4">{{ $batch->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $batch->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ optional($batch->start_date)->timestamp ?? 0 }}">
                            {{ $batch->start_date?->format('M d') ?? '—' }} — {{ $batch->end_date?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4">{{ ucfirst($batch->status) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.batches.edit', $batch)"
                                :delete-url="route('admin.batches.destroy', $batch)"
                                edit-title="Edit batch"
                                delete-title="Delete batch"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No batches yet" :action="route('admin.batches.create')" actionLabel="Create Batch" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($batches->count())
    <x-admin.datatable-scripts table-id="batchesTable" entity="batches" :order-column="3" order-direction="desc" :action-column="5" export-file-name="batches" />
@endif
@endpush
