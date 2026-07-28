@extends('layouts.app')
@section('title', 'My Courses')
@section('page-title', 'My Courses')
@section('breadcrumb', 'Instructor / Courses')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between"><p class="text-sm text-slate-500">Courses assigned to you.</p>
        <a href="{{ route('instructor.courses.create') }}" class="panel-btn-primary text-sm">New course</a></div>
    @if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($courses->count())
        <table class="w-full text-sm panel-table"><thead><tr class="text-left"><th class="px-6 py-3">Course</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Enrollments</th><th class="px-6 py-3 text-right">Actions</th></tr></thead>
        <tbody>@foreach($courses as $course)<tr>
            <td class="px-6 py-3 font-medium">{{ $course->title }}</td>
            <td class="px-6 py-3"><x-badge type="info">{{ $course->status }}</x-badge></td>
            <td class="px-6 py-3">{{ $course->enrollments_count }}</td>
            <td class="px-6 py-3 text-right space-x-2"><a href="{{ route('instructor.courses.show', $course) }}" class="text-indigo-600 text-xs font-semibold">Curriculum</a>
            <a href="{{ route('instructor.courses.edit', $course) }}" class="text-slate-600 text-xs font-semibold">Edit</a></td>
        </tr>@endforeach</tbody></table>
        <div class="px-6 py-4">{{ $courses->links() }}</div>
        @else<x-empty-state title="No assigned courses" />@endif
    </div>
</div>
@endsection
