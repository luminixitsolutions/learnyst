@extends('layouts.app')

@section('title', 'Leaderboard')
@section('page-title', 'Leaderboard')
@section('breadcrumb', 'Gamification / Leaderboard')

@push('styles')
    <x-admin.datatable-styles />
@endpush

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

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($rows->count())
        <div class="overflow-x-auto">
            <table id="leaderboardTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">XP</th>
                        @unless($courseId)<th class="px-6 py-4">Level</th><th class="px-6 py-4">Streak</th>@endunless
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $i => $row)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $row->user?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-emerald-600 font-semibold">{{ $courseId ? ($row->total_xp ?? 0) : $row->xp }}</td>
                        @unless($courseId)
                        <td class="px-6 py-4 text-slate-600">{{ $row->level }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $row->current_streak }}</td>
                        @endunless
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No rankings yet." description="Leaderboard rankings will appear as learners earn XP." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($rows->count())
    <x-admin.datatable-scripts table-id="leaderboardTable" entity="rankings" :order-column="2" order-direction="desc" export-file-name="leaderboard" />
@endif
@endpush
