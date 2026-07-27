@extends('layouts.app')

@section('title', 'Test Series')
@section('page-title', 'Test Series')
@section('breadcrumb', 'Products')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Organize grouped test series and practice exams for learners.</p>
        <a href="{{ route('admin.test-series.create') }}" class="panel-btn-primary">Create Test Series</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($testSeries->count())
        <div class="overflow-x-auto">
            <table id="testSeriesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testSeries as $series)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $series->title }}</p>
                            <p class="text-xs text-slate-500">{{ $series->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $series->is_free ? 'Free' : '₹'.number_format($series->price, 0) }}</td>
                        <td class="px-6 py-4">{{ ucfirst($series->status) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.test-series.edit', $series)"
                                :delete-url="route('admin.test-series.destroy', $series)"
                                edit-title="Edit test series"
                                delete-title="Delete test series"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No test series yet" description="Organize grouped test series and practice exams for your learners." :action="route('admin.test-series.create')" actionLabel="Create Test Series" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($testSeries->count())
    <x-admin.datatable-scripts table-id="testSeriesTable" entity="test series" :order-column="0" order-direction="desc" :action-column="3" export-file-name="test-series" />
@endif
@endpush
