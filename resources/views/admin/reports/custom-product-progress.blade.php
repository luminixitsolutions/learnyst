@extends('layouts.app')

@section('title', 'Custom Product Progress')
@section('page-title', 'Custom Product Progress Report')
@section('breadcrumb', 'Reports / Custom Product Progress')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by title, learner or email..." :showDateRange="true" />

    <x-admin.report-datatable table-id="customProductProgressReportTable" :has-records="$records->count() > 0" entity="custom product progress records" :order-column="5" order-direction="desc" export-file-name="custom-product-progress-report" empty-title="No custom product progress">
        <thead><tr class="text-left">
            <th>Product Name</th><th>Learner</th><th>Email</th><th>Progress</th><th>Completed Content</th><th>Last Activity</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($records as $record)
            <tr>
                <td>{{ $record->course?->title ?? '—' }}</td>
                <td>{{ $record->user?->name }}</td>
                <td class="text-slate-500">{{ $record->user?->email }}</td>
                <td data-order="{{ $record->progress ?? 0 }}">{{ number_format($record->progress ?? 0, 0) }}%</td>
                <td>{{ $record->meta['completed_content'] ?? '—' }}</td>
                <td class="text-slate-500" data-order="{{ $record->updated_at?->timestamp ?? 0 }}">{{ $record->updated_at?->format('M d, Y') }}</td>
                <td><x-badge type="info">{{ ucfirst($record->status) }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
