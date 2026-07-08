@extends('layouts.app')

@section('title', 'Test Series Scores')
@section('page-title', 'Test Series Scores Report')
@section('breadcrumb', 'Reports / Test Series Scores')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by name or email..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Test Series</th>
                    <th class="px-6 py-4">Score</th>
                    <th class="px-6 py-4">Total Marks</th>
                    <th class="px-6 py-4">Percentage</th>
                    <th class="px-6 py-4">Attempt Date</th>
                    <th class="px-6 py-4">Pass/Fail</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $record)
                    @php
                        $score = $record->meta['test_series_score'] ?? 0;
                        $total = $record->meta['test_series_total'] ?? 100;
                        $pct = $total > 0 ? round(($score / $total) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td class="px-6 py-4">{{ $record->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->user?->email }}</td>
                        <td class="px-6 py-4">{{ $record->course?->title ?? $record->bundle?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $score }}</td>
                        <td class="px-6 py-4">{{ $total }}</td>
                        <td class="px-6 py-4">{{ $pct }}%</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->updated_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><x-badge :type="$pct >= 40 ? 'success' : 'danger'">{{ $pct >= 40 ? 'Pass' : 'Fail' }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No test series scores" description="Scores appear when assigned via enrollment records." />
        @endif
    </div>
</div>
@endsection
