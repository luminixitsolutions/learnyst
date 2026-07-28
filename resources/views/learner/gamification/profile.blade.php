@extends('layouts.app')

@section('title', 'My Progress')
@section('page-title', 'Gamification')
@section('breadcrumb', 'Student Panel / Progress')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-slate-900 to-emerald-900 text-white">
        @if($profile)
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm text-white/70">Your level</p>
                    <h2 class="text-4xl font-bold mt-1">Level {{ $profile->level }}</h2>
                    <p class="mt-2 text-white/80">{{ number_format($profile->xp) }} XP · {{ max(0, $profile->xpToNextLevel()) }} XP to next level</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-white/70">Daily streak</p>
                    <p class="text-3xl font-bold">{{ $profile->current_streak }} days</p>
                    <p class="text-xs text-white/60">Best: {{ $profile->longest_streak }}</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('learner.gamification.leaderboard') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-semibold">View leaderboard</a>
            </div>
        @else
            <p class="text-white/80">Start learning to earn XP and unlock badges.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Badges</h3>
            @forelse($badges as $badge)
                <div class="flex items-start gap-3 py-3 border-b border-slate-100 last:border-0">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm font-bold">B</div>
                    <div>
                        <div class="font-semibold text-slate-800">{{ $badge->name }}</div>
                        <div class="text-xs text-slate-500">{{ $badge->description }}</div>
                        <div class="text-xs text-slate-400 mt-1">Earned {{ optional($badge->pivot->awarded_at)->format('M d, Y') }}</div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No badges yet — keep learning!</p>
            @endforelse
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Active Challenges</h3>
            @forelse($challenges as $challenge)
                @php $prog = $challengeProgress->get($challenge->id); @endphp
                <div class="py-3 border-b border-slate-100 last:border-0">
                    <div class="font-semibold text-slate-800">{{ $challenge->title }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $challenge->description }}</div>
                    <div class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: {{ min(100, (($prog->progress ?? 0) / max(1,$challenge->target_count)) * 100) }}%"></div>
                    </div>
                    <div class="text-xs text-slate-500 mt-1">{{ $prog->progress ?? 0 }} / {{ $challenge->target_count }} · +{{ $challenge->xp_reward }} XP</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No active challenges.</p>
            @endforelse
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Recent XP</h3>
        </div>
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left text-slate-500">
                <th class="px-6 py-3">When</th>
                <th class="px-6 py-3">Action</th>
                <th class="px-6 py-3">Points</th>
            </tr></thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr>
                    <td class="px-6 py-3 text-slate-500">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-3 text-slate-700">{{ str_replace('_', ' ', $txn->action_key) }}</td>
                    <td class="px-6 py-3 text-emerald-600 font-semibold">+{{ $txn->points }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">No XP yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
