@extends('layouts.app')

@section('title', 'Discussions')
@section('page-title', 'Discussions')
@section('breadcrumb', 'Course discussions')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <form method="GET">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search discussions..."
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm">
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($discussions->count())
        <div class="overflow-x-auto">
            <table id="discussionsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Author</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discussions as $discussion)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4"><a href="{{ route('admin.discussions.show', $discussion) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $discussion->title }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $discussion->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $discussion->user?->name }}</td>
                        <td class="px-6 py-4">
                            @if($discussion->is_locked)<x-badge type="danger">Locked</x-badge>@else<x-badge type="success">Open</x-badge>@endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $discussion->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.discussions.destroy', $discussion) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No discussions found" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($discussions->count())
    <x-admin.datatable-scripts table-id="discussionsTable" entity="discussions" :order-column="0" order-direction="desc" :action-column="5" export-file-name="discussions" />
@endif
@endpush
