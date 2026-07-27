@extends('layouts.app')

@section('title', 'Tracks')
@section('page-title', 'Tracks')
@section('breadcrumb', 'Products')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Instructor Tracks</h3>
            <p class="text-sm text-slate-500 mt-1">Build learning tracks that guide learners through a path.</p>
        </div>
        <a href="{{ route('admin.tracks.create') }}" class="panel-btn-primary">Create Track</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($tracks->count())
        <div class="overflow-x-auto">
            <table id="tracksTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Instructor</th>
                        <th class="px-6 py-4">Content Security</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tracks as $track)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $track->title }}</p>
                            <p class="text-xs text-slate-500">{{ $track->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $track->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $track->contentSecurityLabel() }}</td>
                        <td class="px-6 py-4">{{ ucfirst($track->status) }}</td>
                        <td class="px-6 py-4">
                            <x-admin.table-actions :delete-url="route('admin.tracks.destroy', $track)" delete-title="Delete track" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No tracks yet" description="Create instructor tracks by adding a title and assigning an instructor." :action="route('admin.tracks.create')" actionLabel="Create Track" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($tracks->count())
    <x-admin.datatable-scripts table-id="tracksTable" entity="tracks" :order-column="0" order-direction="desc" :action-column="4" export-file-name="tracks" />
@endif
@endpush
