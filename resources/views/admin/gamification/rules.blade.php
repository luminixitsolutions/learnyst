@extends('layouts.app')

@section('title', 'XP Rules')
@section('page-title', 'XP Rules')
@section('breadcrumb', 'Gamification / Rules')

@section('content')
<div class="space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-400 mb-4">Configure how learners earn XP. Changes apply to new awards only.</p>
        <form method="POST" action="{{ route('admin.gamification.rules.update') }}" class="space-y-4">
            @csrf
            @foreach($rules as $i => $rule)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end border-b border-slate-700/50 pb-4">
                <input type="hidden" name="rules[{{ $i }}][id]" value="{{ $rule->id }}">
                <div>
                    <label class="text-xs text-slate-400">Action</label>
                    <div class="text-white font-medium">{{ $rule->label }}</div>
                    <div class="text-xs text-slate-500 font-mono">{{ $rule->action_key }}</div>
                </div>
                <x-form-input label="Points" name="rules[{{ $i }}][points]" type="number" :value="$rule->points" required />
                <x-form-input label="Daily Cap" name="rules[{{ $i }}][daily_cap]" type="number" :value="$rule->daily_cap" />
                <label class="flex items-center gap-2 pb-2">
                    <input type="hidden" name="rules[{{ $i }}][is_active]" value="0">
                    <input type="checkbox" name="rules[{{ $i }}][is_active]" value="1" @checked($rule->is_active) class="rounded border-slate-600 bg-slate-800 text-emerald-500">
                    <span class="text-sm text-slate-300">Active</span>
                </label>
            </div>
            @endforeach
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Save Rules</button>
        </form>
    </div>
</div>
@endsection
