@extends('layouts.app')

@section('title', 'Sub Admin Wizard — Communities')
@section('page-title', 'Create Sub Admin')
@section('breadcrumb', 'Wizard / Step 5 — Communities')

@section('content')
<div class="max-w-3xl mx-auto">
    @include('admin.sub-admins.wizard._progress', ['current' => 5])

    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.sub-admins.wizard.store', 5) }}" class="space-y-5">
            @csrf
            <p class="text-sm text-slate-500">Optionally restrict access to specific communities.</p>
            @php $selected = old('community_ids', $data['community_ids'] ?? []); @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-80 overflow-y-auto p-4 rounded-xl bg-slate-900/80 border border-slate-200">
                @forelse($communities as $community)
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="community_ids[]" value="{{ $community->id }}" @checked(in_array($community->id, $selected)) class="rounded border-slate-600 bg-slate-800 text-brand-500">
                    <span class="text-sm text-slate-300">{{ $community->name }}</span>
                </label>
                @empty
                <p class="text-sm text-slate-500 col-span-2">No communities available</p>
                @endforelse
            </div>
            <div class="flex justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sub-admins.wizard.step', 4) }}" class="text-sm text-slate-500 hover:text-white">← Back</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Next: Preview →</button>
            </div>
        </form>
    </div>
</div>
@endsection
