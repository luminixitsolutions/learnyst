@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')
@section('breadcrumb', 'Gamification / Leaderboard')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <x-form-input label="Scope" name="course_id" type="select" :value="$courseId">
                <option value="">Global</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected($courseId == $course->id)>{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <button type="submit" class="px-4 py-2.5 rounded-xl panel-btn-primary">View</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">#</th>
                <th class="px-6 py-4">Learner</th>
                <th class="px-6 py-4">XP</th>
                @unless($courseId)<th class="px-6 py-4">Level</th><th class="px-6 py-4">Streak</th>@endunless
            </tr></thead>
            <tbody>
                @forelse($rows as $i => $row)
                <tr>
                    <td class="px-6 py-4 text-slate-400">{{ $i + 1 }}</td>
                    <td class="px-6 py-4 text-white">{{ $row->user?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-emerald-400 font-semibold">{{ $courseId ? ($row->total_xp ?? 0) : $row->xp }}</td>
                    @unless($courseId)
                    <td class="px-6 py-4 text-slate-400">{{ $row->level }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $row->current_streak }}</td>
                    @endunless
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No rankings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
