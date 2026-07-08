@extends('layouts.app')

@section('title', 'Courses Report')
@section('page-title', 'Course Report')
@section('breadcrumb', 'Reports / Courses')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search course title...">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['published','draft','archived'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($courses->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4">Course</th>
                    <th class="px-6 py-4">Enrollments</th>
                    <th class="px-6 py-4">Avg Progress</th>
                    <th class="px-6 py-4">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.courses.show', $course) }}" class="text-indigo-600">{{ $course->title }}</a></td>
                        <td class="px-6 py-4">{{ $course->enrollments_count }}</td>
                        <td class="px-6 py-4">{{ round($course->avg_progress ?? 0) }}%</td>
                        <td class="px-6 py-4"><x-badge :type="$course->status === 'published' ? 'success' : 'warning'">{{ ucfirst($course->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $courses->links() }}</div>
        @else
        <x-empty-state title="No courses found" />
        @endif
    </div>
</div>
@endsection
