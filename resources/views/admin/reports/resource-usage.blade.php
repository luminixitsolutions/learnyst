@extends('layouts.app')

@section('title', 'Resource Usage Report')
@section('page-title', 'Resource Usage Report')
@section('breadcrumb', 'Reports / Resource Usage')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search resource or learner..." :showDateRange="true" />

    <x-admin.report-datatable table-id="resourceUsageReportTable" :has-records="$downloads->count() > 0" entity="resource downloads" :order-column="3" order-direction="desc" export-file-name="resource-usage-report" empty-title="No resource usage data">
        <thead><tr class="text-left">
            <th>Resource</th><th>Learner</th><th>Downloads</th><th>Last Accessed</th><th>Category</th>
        </tr></thead>
        <tbody>
            @foreach($downloads as $download)
            <tr>
                <td class="text-slate-800">{{ $download->resource?->title ?? '—' }}</td>
                <td>{{ $download->user?->name ?? 'Guest' }}</td>
                <td>1</td>
                <td class="text-slate-500" data-order="{{ $download->created_at->timestamp }}">{{ $download->created_at->format('M d, Y H:i') }}</td>
                <td class="text-slate-500">{{ $download->resource?->category?->name ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
