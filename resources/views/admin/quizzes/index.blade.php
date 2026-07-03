@extends('layouts.app')

@section('title', 'Quizzes')
@section('page-title', 'Quizzes')
@section('breadcrumb', 'Course Management / Quizzes')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search quizzes..." class="panel-input w-64">
            <button class="panel-btn-secondary">Search</button>
        </form>
        <a href="{{ route('admin.quizzes.create') }}" class="panel-btn-primary">Create Quiz</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($quizzes->count())
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left"><th class="px-6 py-4">Quiz</th><th class="px-6 py-4">Course</th><th class="px-6 py-4">Section</th><th class="px-6 py-4">Marks</th></tr></thead>
            <tbody>
                @foreach($quizzes as $quiz)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $quiz->title }}</td>
                    <td class="px-6 py-4">{{ $quiz->section?->course?->title ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $quiz->section?->title ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $quiz->quiz_data['total_marks'] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">{{ $quizzes->links() }}</div>
        @else
        <x-empty-state title="No quizzes yet" description="Create quiz lessons linked to course sections." />
        @endif
    </div>
</div>
@endsection
