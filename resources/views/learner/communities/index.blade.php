@extends('layouts.app')

@section('title', 'Communities')
@section('page-title', 'Communities')
@section('breadcrumb', 'Your communities')

@section('content')
<div class="space-y-6">
    @if($communities->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($communities as $community)
        <a href="{{ route('learner.communities.show', $community) }}" class="glass-card rounded-2xl p-6 hover:border-indigo-400/30 transition">
            <h3 class="text-lg font-bold text-slate-800">{{ $community->name }}</h3>
            <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $community->description ?? 'Join the conversation' }}</p>
            <div class="flex items-center gap-4 mt-4 text-xs text-slate-500">
                <span>{{ $community->posts_count }} posts</span>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="glass-card rounded-2xl">
        <x-empty-state title="No communities yet" description="You haven't joined any communities." />
    </div>
    @endif
</div>
@endsection
