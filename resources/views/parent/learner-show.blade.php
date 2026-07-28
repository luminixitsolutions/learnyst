@extends('layouts.app')
@section('title', $learner->name)
@section('page-title', $learner->name)
@section('breadcrumb', 'Parent / Children / Detail')
@section('content')
<div class="space-y-6">
    <a href="{{ route('parent.learners') }}" class="text-sm text-slate-500">← Children</a>
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Courses" :value="number_format($enrollments->count())" />
        <x-stat-card title="Attendance records" :value="number_format($attendanceCount)" />
        <x-stat-card title="Certificates" :value="number_format($certificates)" />
        <x-stat-card title="Avg progress" :value="round((float) ($enrollments->avg('progress') ?? 0), 1).'%'" />
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b font-semibold">Enrolled courses</div>
        <table class="w-full text-sm panel-table">
            <thead><tr><th class="px-6 py-3 text-left">Course</th><th class="px-6 py-3 text-left">Status</th><th class="px-6 py-3 text-left">Progress</th></tr></thead>
            <tbody>
            @forelse($enrollments as $e)
                <tr>
                    <td class="px-6 py-3">{{ $e->course?->title ?? '—' }}</td>
                    <td class="px-6 py-3">{{ $e->status }}</td>
                    <td class="px-6 py-3">{{ round((float) ($e->progress ?? 0), 1) }}%</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">No enrollments.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
