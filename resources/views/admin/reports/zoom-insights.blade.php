@extends('layouts.app')

@section('title', 'Zoom Insights Report')
@section('page-title', 'Integrated Zoom Insights')
@section('breadcrumb', 'Reports / Zoom Insights')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by class or learner..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($events->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Zoom Class</th>
                    <th class="px-6 py-4">Course</th>
                    <th class="px-6 py-4">Instructor</th>
                    <th class="px-6 py-4">Start Time</th>
                    <th class="px-6 py-4">End Time</th>
                    <th class="px-6 py-4">Attendance Status</th>
                </tr></thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $event->title }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $event->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $event->instructor?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $event->starts_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $event->ends_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-400">No attendance data</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $events->links() }}</div>
        @else
        <x-empty-state title="No Zoom sessions found" description="Schedule live classes with Zoom platform to see insights here." />
        @endif
    </div>
</div>
@endsection
