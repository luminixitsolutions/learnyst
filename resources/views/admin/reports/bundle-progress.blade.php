@extends('layouts.app')

@section('title', 'Bundle Progress Report')
@section('page-title', 'Bundle Progress Report')
@section('breadcrumb', 'Reports / Bundle Progress')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by title, learner or email...">
        <x-slot:filters>
            <select name="bundle_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Bundles</option>
                @foreach($bundles as $bundle)
                    <option value="{{ $bundle->id }}" @selected(request('bundle_id') == $bundle->id)>{{ $bundle->title }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <x-admin.report-datatable table-id="bundleProgressReportTable" :has-records="$records->count() > 0" entity="bundle progress records" :order-column="6" order-direction="desc" export-file-name="bundle-progress-report" empty-title="No bundle progress data">
        <thead><tr class="text-left">
            <th>Bundle Name</th><th>Learner</th><th>Email</th><th>Courses Completed</th><th>Total Courses</th><th>Progress %</th><th>Last Activity</th><th>Status</th>
        </tr></thead>
        <tbody>
            @foreach($records as $record)
            @php $meta = $record->meta ?? []; @endphp
            <tr>
                <td class="text-slate-800">{{ $record->bundle?->title }}</td>
                <td>{{ $record->user?->name }}</td>
                <td class="text-slate-500">{{ $record->user?->email }}</td>
                <td data-order="{{ $meta['courses_completed'] ?? 0 }}">{{ $meta['courses_completed'] ?? '—' }}</td>
                <td>{{ $meta['total_courses'] ?? $record->bundle?->courses()->count() }}</td>
                <td data-order="{{ $record->progress ?? 0 }}">{{ number_format($record->progress ?? 0, 0) }}%</td>
                <td class="text-slate-500" data-order="{{ $record->updated_at?->timestamp ?? 0 }}">{{ $record->updated_at?->format('M d, Y') }}</td>
                <td><x-badge type="info">{{ ucfirst($record->status) }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
