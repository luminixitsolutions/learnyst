@extends('layouts.app')

@section('title', 'Badges')
@section('page-title', 'Badges')
@section('breadcrumb', 'Gamification / Badges')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Create Badge</h3>
        <form method="POST" action="{{ route('admin.gamification.badges.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Criteria" name="criteria_type" type="select" required>
                <option value="xp_total">Total XP</option>
                <option value="level">Level reached</option>
                <option value="streak">Login streak</option>
                <option value="lesson_complete_count">Lessons completed</option>
                <option value="quiz_pass_count">Quizzes passed</option>
            </x-form-input>
            <x-form-input label="Criteria Value" name="criteria_value" type="number" :value="10" required />
            <x-form-input label="Bonus XP" name="xp_reward" type="number" :value="0" />
            <x-form-input label="Icon / emoji key" name="icon" placeholder="star" />
            <x-form-input label="Description" name="description" type="textarea" class="md:col-span-3" />
            <label class="flex items-center gap-2 md:col-span-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-600 bg-slate-800 text-emerald-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <div class="md:col-span-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Badge</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($badges->count())
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Badge</th>
                <th class="px-6 py-4">Criteria</th>
                <th class="px-6 py-4">Awarded</th>
                <th class="px-6 py-4">Bonus XP</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @foreach($badges as $badge)
                <tr>
                    <td class="px-6 py-4 text-white font-medium">{{ $badge->name }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $badge->criteria_type }} ≥ {{ $badge->criteria_value }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $badge->users_count }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $badge->xp_reward }}</td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.gamification.badges.destroy', $badge) }}">@csrf @method('DELETE')
                            <button type="submit" class="text-red-400 text-sm" onclick="return confirm('Delete badge?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $badges->links() }}</div>
        @else
        <x-empty-state title="No badges yet" />
        @endif
    </div>
</div>
@endsection
