@extends('layouts.app')
@section('title', 'Course Progress')
@section('page-title', 'Course Progress')
@section('breadcrumb', 'Parent / Progress')
@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <table class="w-full text-sm panel-table">
        <thead><tr><th class="px-6 py-3 text-left">Learner</th><th class="px-6 py-3 text-left">Course</th><th class="px-6 py-3 text-left">Progress</th><th class="px-6 py-3 text-left">Status</th><th class="px-6 py-3 text-left">Updated</th></tr></thead>
        <tbody>
        @forelse($enrollments as $e)
            <tr>
                <td class="px-6 py-3">{{ $e->user?->name }}</td>
                <td class="px-6 py-3">{{ $e->course?->title }}</td>
                <td class="px-6 py-3">{{ round((float) ($e->progress ?? 0), 1) }}%</td>
                <td class="px-6 py-3">{{ $e->status }}</td>
                <td class="px-6 py-3 text-slate-500">{{ $e->updated_at?->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No enrollments for linked learners.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4">{{ $enrollments->links() }}</div>
</div>
@endsection
