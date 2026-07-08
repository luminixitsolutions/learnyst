@extends('layouts.app')

@section('title', 'Live Class Attendance')
@section('page-title', 'Live Class Attendance Report')
@section('breadcrumb', 'Reports / Live Class Attendance')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by class title..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($events->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Class Title</th>
                    <th class="px-6 py-4">Course / Batch</th>
                    <th class="px-6 py-4">Scheduled Start</th>
                    <th class="px-6 py-4">Scheduled End</th>
                    <th class="px-6 py-4">Learner Attendance</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($events as $event)
                    <tr>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $event->title }}</td>
                        <td class="px-6 py-4">{{ $event->course?->title ?? $event->batch?->title ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $event->starts_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $event->ends_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-slate-400">—</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($event->status ?? 'scheduled') }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $events->links() }}</div>
        @else
        <x-empty-state title="No live classes scheduled" />
        @endif
    </div>
</div>
@endsection
