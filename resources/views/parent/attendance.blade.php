@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('breadcrumb', 'Parent / Attendance')
@section('content')
<div class="space-y-4">
    <form method="GET" class="flex gap-2 items-end">
        <div>
            <label class="text-xs text-slate-500">Child</label>
            <select name="learner_id" class="panel-input text-sm">
                <option value="">All linked</option>
                @foreach($learners as $l)
                    <option value="{{ $l->id }}" @selected(request('learner_id') == $l->id)>{{ $l->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="panel-btn-secondary text-sm">Filter</button>
    </form>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr><th class="px-6 py-3 text-left">Learner</th><th class="px-6 py-3 text-left">Class</th><th class="px-6 py-3 text-left">Course</th><th class="px-6 py-3 text-left">Attended</th></tr></thead>
            <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="px-6 py-3">{{ $row->user?->name }}</td>
                    <td class="px-6 py-3">{{ $row->event?->title }}</td>
                    <td class="px-6 py-3">{{ $row->event?->course?->title }}</td>
                    <td class="px-6 py-3">{{ $row->attended_at?->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No attendance records.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $rows->links() }}</div>
    </div>
</div>
@endsection
