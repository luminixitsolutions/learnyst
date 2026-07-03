@extends('layouts.app')

@section('title', 'Courses Report')
@section('page-title', 'Courses Report')
@section('breadcrumb', 'Reports / Courses')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white">← All Reports</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Enrollments</th>
                        <th class="px-6 py-4">Avg Progress</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.courses.show', $course) }}" class="text-white hover:text-indigo-600">{{ $course->title }}</a></td>
                        <td class="px-6 py-4 text-white">{{ $course->enrollments_count }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ round($course->avg_progress ?? 0) }}%</td>
                        <td class="px-6 py-4"><x-badge :type="$course->status === 'published' ? 'success' : 'warning'">{{ ucfirst($course->status) }}</x-badge></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $courses->links() }}</div>
    </div>
</div>
@endsection
