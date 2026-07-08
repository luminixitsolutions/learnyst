@extends('layouts.app')

@section('title', 'Super Live Lessons Report')
@section('page-title', 'Super Live Lessons Report')
@section('breadcrumb', 'Reports / Super Live Lessons')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by learner name..." :showDateRange="true" />

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Lesson / Course</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Attendance</th>
                    <th class="px-6 py-4">Watch Duration</th>
                    <th class="px-6 py-4">Join Time</th>
                    <th class="px-6 py-4">Leave Time</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $record)
                    <tr>
                        <td class="px-6 py-4">{{ $record->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $record->user?->name }}</td>
                        <td class="px-6 py-4 text-slate-400">—</td>
                        <td class="px-6 py-4 text-slate-400">—</td>
                        <td class="px-6 py-4 text-slate-400">—</td>
                        <td class="px-6 py-4 text-slate-400">—</td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($record->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No super live lesson data" />
        @endif
    </div>
</div>
@endsection
