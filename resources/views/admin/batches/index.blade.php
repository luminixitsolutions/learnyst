@extends('layouts.app')

@section('title', 'Batches')
@section('page-title', 'Batches')
@section('breadcrumb', 'Manage batches')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex gap-3">
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
                <option value="">All Status</option>
                @foreach(['upcoming','active','completed','cancelled'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.batches.create') }}" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Batch</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($batches->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
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
                        <td class="px-6 py-4"><a href="{{ route('admin.batches.show', $batch) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $batch->title }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $batch->course?->title }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $batch->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $batch->start_date?->format('M d') ?? '—' }} — {{ $batch->end_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4"><x-badge :type="match($batch->status) { 'active' => 'success', 'upcoming' => 'info', 'cancelled' => 'danger', default => 'default' }">{{ ucfirst($batch->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.batches.edit', $batch) }}" class="text-indigo-600 text-sm mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.batches.destroy', $batch) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-400 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $batches->links() }}</div>
        @else
        <x-empty-state title="No batches yet" :action="route('admin.batches.create')" actionLabel="Create Batch" />
        @endif
    </div>
</div>
@endsection
