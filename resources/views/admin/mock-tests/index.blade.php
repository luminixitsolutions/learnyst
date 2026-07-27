@extends('layouts.app')

@section('title', 'Mock test')
@section('page-title', 'Mock test')
@section('breadcrumb', 'Products')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Create and manage mock tests with online and offline quiz types.</p>
        <a href="{{ route('admin.mock-tests.create') }}" class="panel-btn-primary">Create Mock Test</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($mockTests->count())
        <div class="overflow-x-auto">
            <table id="mockTestsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Quiz Type</th>
                        <th class="px-6 py-4">Template</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mockTests as $mockTest)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $mockTest->title }}</p>
                            <p class="text-xs text-slate-500">{{ $mockTest->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $mockTest->quizTypeLabel() }}</td>
                        <td class="px-6 py-4">{{ $mockTest->templateLabel() }}</td>
                        <td class="px-6 py-4">{{ $mockTest->is_free ? 'Free' : '₹'.number_format($mockTest->price, 0) }}</td>
                        <td class="px-6 py-4">{{ ucfirst($mockTest->status) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions
                                :edit-url="route('admin.mock-tests.edit', $mockTest)"
                                :delete-url="route('admin.mock-tests.destroy', $mockTest)"
                                edit-title="Edit mock test"
                                delete-title="Delete mock test"
                            />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No mock tests yet" description="Create and manage mock tests with timed assessments." :action="route('admin.mock-tests.create')" actionLabel="Create Mock Test" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($mockTests->count())
    <x-admin.datatable-scripts table-id="mockTestsTable" entity="mock tests" :order-column="0" order-direction="desc" :action-column="5" export-file-name="mock-tests" />
@endif
@endpush
