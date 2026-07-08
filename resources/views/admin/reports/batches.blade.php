@extends('layouts.app')

@section('title', 'Batches Report')
@section('page-title', 'Batches Report')
@section('breadcrumb', 'Reports / Batches')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by batch title...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['active','draft','completed','archived'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($batches->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Batch Name</th>
                    <th class="px-6 py-4">Product / Course</th>
                    <th class="px-6 py-4">Instructor</th>
                    <th class="px-6 py-4">Start Date</th>
                    <th class="px-6 py-4">End Date</th>
                    <th class="px-6 py-4">Total Learners</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($batches as $batch)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.batches.show', $batch) }}" class="text-indigo-600">{{ $batch->title }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $batch->course?->title }}</td>
                        <td class="px-6 py-4">{{ $batch->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $batch->start_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $batch->end_date?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $batch->learners_count }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($batch->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $batches->links() }}</div>
        @else
        <x-empty-state title="No batches found" />
        @endif
    </div>
</div>
@endsection
