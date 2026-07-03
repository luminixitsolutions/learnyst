@extends('layouts.app')

@section('title', $batch->title)
@section('page-title', $batch->title)
@section('breadcrumb', 'Batches / Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <x-badge :type="match($batch->status) { 'active' => 'success', 'upcoming' => 'info', 'cancelled' => 'danger', default => 'default' }">{{ ucfirst($batch->status) }}</x-badge>
            <p class="text-sm text-slate-500 mt-2">{{ $batch->course?->title }} · {{ $batch->instructor?->name ?? 'No instructor' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.batches.edit', $batch) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm">Edit</a>
            <a href="{{ route('admin.batches.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-300 text-sm">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <x-stat-card title="Learners" :value="number_format($batch->learners->count())" />
        <x-stat-card title="Max Capacity" :value="$batch->max_learners ?? '∞'" />
        <x-stat-card title="Price" :value="'₹'.number_format($batch->price ?? 0, 0)" />
        <x-stat-card title="Mode" :value="$batch->is_online ? 'Online' : 'Offline'" />
    </div>

    @if($batch->description)
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-500">{{ $batch->description }}</p>
    </div>
    @endif

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Add Learner</h3>
        <form method="POST" action="{{ route('admin.batches.learners.add', $batch) }}" class="flex flex-wrap items-end gap-4">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <x-form-input label="Learner" name="user_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($availableLearners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </x-form-input>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Add to Batch</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Batch Learners ({{ $batch->learners->count() }})</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-slate-500 text-left"><th class="pb-2">Name</th><th class="pb-2">Email</th><th class="pb-2">Status</th><th class="pb-2"></th></tr></thead>
                <tbody>
                    @forelse($batch->learners as $learner)
                    <tr>
                        <td class="py-2.5 text-slate-800">{{ $learner->name }}</td>
                        <td class="py-2.5 text-slate-500">{{ $learner->email }}</td>
                        <td class="py-2.5"><x-badge type="success">{{ $learner->pivot->status ?? 'active' }}</x-badge></td>
                        <td class="py-2.5 text-right">
                            <form method="POST" action="{{ route('admin.batches.learners.remove', [$batch, $learner]) }}">@csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-4 text-slate-500 text-center">No learners in this batch</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
