@extends('layouts.app')

@section('title', 'Challenges')
@section('page-title', 'Challenges')
@section('breadcrumb', 'Gamification / Challenges')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-white mb-4">Create Challenge</h3>
        <form method="POST" action="{{ route('admin.gamification.challenges.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Action" name="action_key" type="select" required>
                @foreach($actions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Target Count" name="target_count" type="number" :value="5" required />
            <x-form-input label="Reward XP" name="xp_reward" type="number" :value="50" />
            <x-form-input label="Starts At" name="starts_at" type="datetime-local" />
            <x-form-input label="Ends At" name="ends_at" type="datetime-local" />
            <x-form-input label="Description" name="description" type="textarea" class="md:col-span-3" />
            <div class="md:col-span-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Challenge</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($challenges->count())
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Challenge</th>
                <th class="px-6 py-4">Action</th>
                <th class="px-6 py-4">Target</th>
                <th class="px-6 py-4">Reward</th>
                <th class="px-6 py-4">Window</th>
                <th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @foreach($challenges as $challenge)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $challenge->title }}</td>
                    <td class="px-6 py-4 text-slate-400 font-mono text-xs">{{ $challenge->action_key }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $challenge->target_count }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $challenge->xp_reward }} XP</td>
                    <td class="px-6 py-4 text-slate-400 text-xs">
                        {{ $challenge->starts_at?->format('M d') ?? '—' }} → {{ $challenge->ends_at?->format('M d') ?? '—' }}
                    </td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.gamification.challenges.destroy', $challenge) }}">@csrf @method('DELETE')
                            <button type="submit" class="text-red-400 text-sm" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $challenges->links() }}</div>
        @else
        <x-empty-state title="No challenges yet" />
        @endif
    </div>
</div>
@endsection
