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

    <x-admin.report-datatable table-id="batchesReportTable" :has-records="$batches->count() > 0" entity="batches" :order-column="3" order-direction="desc" export-file-name="batches-report" empty-title="No batches found">
        <thead><tr class="text-left">
            <th>Batch Name</th><th>Product / Course</th><th>Instructor</th><th>Start Date</th><th>End Date</th><th>Total Learners</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($batches as $batch)
            <tr>
                <td><a href="{{ route('admin.batches.show', $batch) }}" class="text-indigo-600">{{ $batch->title }}</a></td>
                <td class="text-slate-500">{{ $batch->course?->title }}</td>
                <td>{{ $batch->instructor?->name ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $batch->start_date?->timestamp ?? 0 }}">{{ $batch->start_date?->format('M d, Y') ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $batch->end_date?->timestamp ?? 0 }}">{{ $batch->end_date?->format('M d, Y') ?? '—' }}</td>
                <td data-order="{{ $batch->learners_count }}">{{ $batch->learners_count }}</td>
                <td><x-badge type="info">{{ ucfirst($batch->status) }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
