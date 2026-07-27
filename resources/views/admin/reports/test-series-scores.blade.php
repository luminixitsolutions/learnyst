@extends('layouts.app')

@section('title', 'Test Series Scores')
@section('page-title', 'Test Series Scores Report')
@section('breadcrumb', 'Reports / Test Series Scores')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by name or email..." :showDateRange="true" />

    <x-admin.report-datatable table-id="testSeriesScoresReportTable" :has-records="$records->count() > 0" entity="test series scores" :order-column="6" order-direction="desc" export-file-name="test-series-scores-report" empty-title="No test series scores" empty-description="Scores appear when assigned via enrollment records.">
        <thead><tr class="text-left">
            <th>Learner</th><th>Email</th><th>Test Series</th><th>Score</th><th>Total Marks</th><th>Percentage</th><th>Attempt Date</th><th>Pass/Fail</th>
        </tr></thead>
        <tbody>
            @foreach($records as $record)
            @php
                $score = $record->meta['test_series_score'] ?? 0;
                $total = $record->meta['test_series_total'] ?? 100;
                $pct = $total > 0 ? round(($score / $total) * 100, 1) : 0;
            @endphp
            <tr>
                <td>{{ $record->user?->name }}</td>
                <td class="text-slate-500">{{ $record->user?->email }}</td>
                <td>{{ $record->course?->title ?? $record->bundle?->title ?? '—' }}</td>
                <td data-order="{{ $score }}">{{ $score }}</td>
                <td data-order="{{ $total }}">{{ $total }}</td>
                <td data-order="{{ $pct }}">{{ $pct }}%</td>
                <td class="text-slate-500" data-order="{{ $record->updated_at?->timestamp ?? 0 }}">{{ $record->updated_at?->format('M d, Y') }}</td>
                <td><x-badge :type="$pct >= 40 ? 'success' : 'danger'">{{ $pct >= 40 ? 'Pass' : 'Fail' }}</x-badge></td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
