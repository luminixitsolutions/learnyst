@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')
@section('breadcrumb', 'Student Panel / Leaderboard')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <x-form-input label="Scope" name="course_id" type="select" :value="$courseId">
                <option value="">Global</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected($courseId == $course->id)>{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <button type="submit" class="px-4 py-2.5 rounded-xl panel-btn-primary">Filter</button>
        </form>
        <a href="{{ route('learner.gamification.profile') }}" class="text-sm text-emerald-600 hover:underline">← My progress</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Rank</th>
                <th class="px-6 py-4">Learner</th>
                <th class="px-6 py-4">XP</th>
            </tr></thead>
            <tbody>
                @forelse($rows as $i => $row)
                <tr class="{{ ($row->user_id ?? $row->user?->id) === auth()->id() ? 'bg-emerald-50' : '' }}">
                    <td class="px-6 py-4 font-semibold">{{ $i + 1 }}</td>
                    <td class="px-6 py-4">{{ $row->user?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-emerald-600 font-bold">{{ $courseId ? ($row->total_xp ?? 0) : $row->xp }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">No rankings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
