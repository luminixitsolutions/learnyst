@extends('layouts.app')
@section('title', 'Performance')
@section('page-title', 'Performance')
@section('breadcrumb', 'Parent / Performance')
@section('content')
<div class="space-y-4">
@forelse($data as $row)
    <div class="glass-card rounded-2xl p-5">
        <div class="flex justify-between mb-3">
            <h3 class="font-bold text-slate-800">{{ $row['learner']->name }}</h3>
            <span class="text-sm text-slate-500">Avg progress {{ $row['avg'] }}%</span>
        </div>
        <div class="space-y-2">
            @forelse($row['enrollments'] as $e)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $e->course?->title }}</span>
                        <span>{{ round((float) ($e->progress ?? 0), 1) }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-600 rounded-full" style="width: {{ min(100, (float) ($e->progress ?? 0)) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No course data.</p>
            @endforelse
        </div>
    </div>
@empty
    <div class="glass-card rounded-2xl p-8"><x-empty-state title="No linked learners" description="Link children to see performance." /></div>
@endforelse
</div>
@endsection
