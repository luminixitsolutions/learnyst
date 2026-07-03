@extends('layouts.app')

@section('title', 'Assignments')
@section('page-title', 'Assignments')
@section('breadcrumb', 'Course Management / Assignments')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search assignments..." class="panel-input w-64">
            <button class="panel-btn-secondary">Search</button>
        </form>
        <a href="{{ route('admin.assignments.create') }}" class="panel-btn-primary">Create Assignment</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($assignments->count())
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left"><th class="px-6 py-4">Assignment</th><th class="px-6 py-4">Course</th><th class="px-6 py-4">Due Date</th><th class="px-6 py-4">Marks</th></tr></thead>
            <tbody>
                @foreach($assignments as $assignment)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $assignment->title }}</td>
                    <td class="px-6 py-4">{{ $assignment->section?->course?->title ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $assignment->quiz_data['due_date'] ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $assignment->quiz_data['marks'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">{{ $assignments->links() }}</div>
        @else
        <x-empty-state title="No assignments yet" />
        @endif
    </div>
</div>
@endsection
