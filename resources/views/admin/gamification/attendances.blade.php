@extends('layouts.app')

@section('title', 'Mark Attendance')
@section('page-title', 'Live Attendance')
@section('breadcrumb', 'Gamification / Attendance')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white">{{ $liveClass->title }}</h3>
        <p class="text-sm text-slate-400 mt-1">{{ $liveClass->course?->title }} · {{ $liveClass->starts_at?->format('M d, Y h:i A') }}</p>
    </div>

    <form method="POST" action="{{ route('admin.gamification.attendances.mark', $liveClass) }}" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <p class="text-sm text-slate-400">Select learners who attended. XP is awarded once per class.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-96 overflow-y-auto">
            @foreach($learners as $learner)
            <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/50 {{ in_array($learner->id, $attended) ? 'opacity-60' : '' }}">
                <input type="checkbox" name="user_ids[]" value="{{ $learner->id }}"
                    @checked(in_array($learner->id, $attended))
                    @disabled(in_array($learner->id, $attended))
                    class="rounded border-slate-600 bg-slate-800 text-emerald-500">
                <span class="text-sm text-white">{{ $learner->name }}
                    <span class="text-slate-500 text-xs">{{ $learner->email }}</span>
                    @if(in_array($learner->id, $attended))
                        <span class="text-emerald-400 text-xs ml-1">✓ marked</span>
                    @endif
                </span>
            </label>
            @endforeach
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Mark Attendance + Award XP</button>
    </form>
</div>
@endsection
