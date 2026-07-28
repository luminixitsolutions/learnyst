@extends('layouts.app')
@section('title', 'Jobs & Internships')
@section('page-title', 'Placements')
@section('breadcrumb', 'Student / Placements')

@section('content')
<div class="space-y-6">
    <div class="flex gap-2">
        <a href="{{ route('learner.placements.index') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !request('type')?'bg-emerald-600 text-white':'bg-slate-100' }}">All</a>
        <a href="{{ route('learner.placements.index', ['type'=>'job']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('type')==='job'?'bg-emerald-600 text-white':'bg-slate-100' }}">Jobs</a>
        <a href="{{ route('learner.placements.index', ['type'=>'internship']) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('type')==='internship'?'bg-emerald-600 text-white':'bg-slate-100' }}">Internships</a>
        <a href="{{ route('learner.placements.applications') }}" class="ml-auto text-sm text-emerald-600">My applications</a>
        <a href="{{ route('learner.placements.resume') }}" class="text-sm text-emerald-600">Resume builder</a>
    </div>
    <div class="grid gap-4">
        @forelse($jobs as $job)
            <a href="{{ route('learner.placements.show', $job) }}" class="glass-card rounded-2xl p-5 block hover:ring-1 hover:ring-emerald-300">
                <div class="flex justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800">{{ $job->title }}</h3>
                        <p class="text-sm text-slate-500">{{ $job->company?->name }} · {{ $job->location }} · <span class="capitalize">{{ $job->type }}</span></p>
                    </div>
                    @if($appliedIds->contains($job->id))
                        <span class="text-xs text-emerald-600 font-semibold">Applied</span>
                    @endif
                </div>
            </a>
        @empty
            <x-empty-state title="No open listings" />
        @endforelse
    </div>
    {{ $jobs->links() }}
</div>
@endsection
