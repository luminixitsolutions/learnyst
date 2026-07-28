@extends('layouts.app')
@section('title', 'Applications')
@section('page-title', 'Applications')
@section('breadcrumb', 'Placements / Applications')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($applications->count())
        <div class="overflow-x-auto">
            <table id="placementAppsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Candidate</th>
                        <th class="px-6 py-4">Job</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Interview</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $app->user?->name }}</p>
                            @if($app->resumeUrl())
                                <a href="{{ $app->resumeUrl() }}" target="_blank" class="text-xs text-emerald-600">Resume</a>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $app->job?->title }}</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ $app->status }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $app->interview_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.placements.applications.update', $app) }}" class="space-y-2 min-w-[200px]">
                                @csrf
                                <select name="status" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 text-xs">
                                    @foreach(['applied','shortlisted','interview','offered','rejected','hired'] as $st)
                                        <option value="{{ $st }}" @selected($app->status===$st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                                <input type="datetime-local" name="interview_at" value="{{ $app->interview_at?->format('Y-m-d\TH:i') }}" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 text-xs">
                                <input type="text" name="interview_mode" value="{{ $app->interview_mode }}" placeholder="Zoom / campus" class="w-full rounded-lg border border-slate-200 bg-white text-slate-800 text-xs">
                                <button class="text-emerald-600 text-xs">Save</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No applications." description="Applications will appear when candidates apply to listings." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($applications->count())
    <x-admin.datatable-scripts table-id="placementAppsTable" entity="applications" :order-column="0" order-direction="desc" :action-column="4" export-file-name="placement-applications" />
@endif
@endpush
