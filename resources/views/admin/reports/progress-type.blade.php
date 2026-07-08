@extends('layouts.app')

@section('title', $reportType['title'])
@section('page-title', $reportType['title'])
@section('breadcrumb', 'Reports / Product Progress / ' . $reportType['title'])

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by learner or product..." :showDateRange="true">
        <x-slot:filters>
            <select name="course_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Products</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
            <select name="learner_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Learners</option>
                @foreach($learners as $learner)
                    <option value="{{ $learner->id }}" @selected(request('learner_id') == $learner->id)>{{ $learner->name }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Product / Course</th>
                    <th class="px-6 py-4">Learner</th>
                    <th class="px-6 py-4">Progress %</th>
                    <th class="px-6 py-4">Completed Lessons</th>
                    <th class="px-6 py-4">Total Lessons</th>
                    <th class="px-6 py-4">Last Activity</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($records as $record)
                    @php $meta = $record->meta ?? []; @endphp
                    <tr>
                        <td class="px-6 py-4 text-slate-800">{{ $record->course?->title ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $record->user?->name }}</td>
                        <td class="px-6 py-4">{{ number_format($record->progress ?? 0, 0) }}%</td>
                        <td class="px-6 py-4">{{ $meta['completed_lessons'] ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $meta['total_lessons'] ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->updated_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4"><x-badge :type="$record->status === 'active' ? 'success' : 'warning'">{{ ucfirst($record->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No progress data" description="Progress records will appear when learners are enrolled and active." />
        @endif
    </div>
</div>
@endsection
