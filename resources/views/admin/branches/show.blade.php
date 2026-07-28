@extends('layouts.app')
@section('title', $branch->name)
@section('page-title', $branch->name)
@section('breadcrumb', 'Branches / Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5"><p class="text-xs text-slate-400">Paid revenue</p><p class="text-xl font-bold text-white mt-1">₹{{ number_format($revenue,2) }}</p></div>
        <div class="glass-card rounded-2xl p-5"><p class="text-xs text-slate-400">Branch share</p><p class="text-xl font-bold text-emerald-400 mt-1">₹{{ number_format($branchShare,2) }}</p></div>
        <div class="glass-card rounded-2xl p-5"><p class="text-xs text-slate-400">HQ share</p><p class="text-xl font-bold text-sky-400 mt-1">₹{{ number_format($hqShare,2) }}</p></div>
        <div class="glass-card rounded-2xl p-5"><p class="text-xs text-slate-400">Allocated users</p><p class="text-xl font-bold text-white mt-1">{{ $branch->users->count() }}</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="font-bold text-white">Revenue share %</h3>
            <form method="POST" action="{{ route('admin.branches.share', $branch) }}" class="flex gap-3">
                @csrf
                <input type="number" step="0.01" min="0" max="100" name="revenue_share_percent" value="{{ $branch->revenue_share_percent }}" class="rounded-xl bg-slate-800 border-slate-600 text-white">
                <button class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Update</button>
            </form>
            <h3 class="font-bold text-white pt-4">Assign branch admin</h3>
            <form method="POST" action="{{ route('admin.branches.admins', $branch) }}" class="flex gap-3">
                @csrf
                <select name="user_id" class="flex-1 rounded-xl bg-slate-800 border-slate-600 text-white text-sm">
                    @foreach($staff as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                </select>
                <button class="text-emerald-400 text-sm">Assign</button>
            </form>
            <div class="text-xs text-slate-500">Admins: {{ $branch->admins->pluck('name')->join(', ') ?: '—' }}</div>
        </div>
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="font-bold text-white">Allocate student / staff</h3>
            <form method="POST" action="{{ route('admin.branches.users', $branch) }}" class="space-y-3">
                @csrf
                <select name="user_id" class="w-full rounded-xl bg-slate-800 border-slate-600 text-white text-sm">
                    <optgroup label="Learners">
                        @foreach($learners as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                    </optgroup>
                    <optgroup label="Staff">
                        @foreach($staff as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach
                    </optgroup>
                </select>
                <select name="role_in_branch" class="w-full rounded-xl bg-slate-800 border-slate-600 text-white text-sm">
                    <option value="learner">Learner</option>
                    <option value="staff">Staff</option>
                </select>
                <button class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Allocate</button>
            </form>
            <div class="max-h-48 overflow-y-auto text-sm space-y-1">
                @foreach($branch->users as $u)
                    <div class="text-slate-300">{{ $u->name }} <span class="text-xs text-slate-500">({{ $u->pivot->role_in_branch }})</span></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
