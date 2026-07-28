@extends('layouts.app')

@section('title', 'Test Takes Insight')
@section('page-title', 'Test Takes Insight')
@section('breadcrumb', 'Insights / Live / Test Takes')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-2 mb-2">
        <span class="px-2 py-1 rounded-lg bg-amber-100 text-amber-700 text-xs font-semibold">Upcoming</span>
        <span class="text-sm text-slate-500">Test attempt analytics</span>
    </div>

    <x-insight-toolbar :backRoute="route('admin.insights.live.index')" searchPlaceholder="Search learner or test..." />

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($records->count())
        <div class="overflow-x-auto">
            <table id="testTakesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Test Name</th>
                        <th class="px-6 py-4">Score</th>
                        <th class="px-6 py-4">Attempt Date</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $row)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->meta['mock_test_score'] ?? $row->meta['test_series_score'] ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $row->updated_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($row->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No results found" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($records->count())
    <x-admin.datatable-scripts table-id="testTakesTable" entity="test takes" :order-column="3" order-direction="desc" export-file-name="test-takes" />
@endif
@endpush
