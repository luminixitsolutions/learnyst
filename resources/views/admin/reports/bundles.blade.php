@extends('layouts.app')

@section('title', 'Bundle Report')
@section('page-title', 'Bundle Report')
@section('breadcrumb', 'Reports / Bundles')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search bundle title..." />

    <x-admin.report-datatable table-id="bundlesReportTable" :has-records="$bundles->count() > 0" entity="bundles" :order-column="3" order-direction="desc" export-file-name="bundles-report" empty-title="No bundles found">
        <thead><tr class="text-left">
            <th>Bundle</th><th>Status</th><th>Courses</th><th>Enrollments</th><th>Price</th>
        </tr></thead>
        <tbody>
            @foreach($bundles as $bundle)
            <tr>
                <td><a href="{{ route('admin.bundles.show', $bundle) }}" class="text-indigo-600">{{ $bundle->title }}</a></td>
                <td><x-badge type="info">{{ ucfirst($bundle->status) }}</x-badge></td>
                <td data-order="{{ $bundle->courses_count }}">{{ $bundle->courses_count }}</td>
                <td data-order="{{ $bundle->enrollments_count }}">{{ $bundle->enrollments_count }}</td>
                <td data-order="{{ $bundle->price ?? 0 }}">₹{{ number_format($bundle->price ?? 0, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-admin.report-datatable>
</div>
@endsection
