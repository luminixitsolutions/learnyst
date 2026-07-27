@extends('layouts.app')

@section('title', 'Question Pools')
@section('page-title', 'Question Pools')
@section('breadcrumb', 'Products / Question Pools')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Organize reusable question banks for quizzes and mock tests.</p>
        <a href="{{ route('admin.question-pools.create') }}" class="panel-btn-primary">Create Question Pool</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($questionPools->count())
        <div class="overflow-x-auto">
            <table id="questionPoolsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Questions</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questionPools as $pool)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $pool->title }}</p>
                            @if($pool->description)
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $pool->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">{{ $pool->questions_count ?? 0 }}</td>
                        <td class="px-6 py-4">{{ ucfirst($pool->status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap" data-order="{{ $pool->created_at->timestamp }}">{{ $pool->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions :delete-url="route('admin.question-pools.destroy', $pool)" delete-title="Delete question pool" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No question pools yet" :action="route('admin.question-pools.create')" actionLabel="Create Question Pool" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($questionPools->count())
    <x-admin.datatable-scripts table-id="questionPoolsTable" entity="question pools" :order-column="3" order-direction="desc" :action-column="4" export-file-name="question-pools" />
@endif
@endpush
