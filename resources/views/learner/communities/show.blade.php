@extends('layouts.app')

@section('title', $community->name)
@section('page-title', $community->name)
@section('breadcrumb', 'Community / Posts')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $community->description }}</p>
        <a href="{{ route('learner.communities.index') }}" class="text-sm text-indigo-600">← All communities</a>
    </div>

    <div class="space-y-4">
        @forelse($community->posts as $post)
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-sm font-bold text-indigo-600">
                    {{ strtoupper(substr($post->user?->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $post->user?->name }}</p>
                    <p class="text-xs text-slate-500">{{ $post->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @if($post->title)<h3 class="text-lg font-bold text-slate-800 mb-2">{{ $post->title }}</h3>@endif
            <p class="text-sm text-slate-500 whitespace-pre-line">{{ $post->body }}</p>
        </div>
        @empty
        <div class="glass-card rounded-2xl">
            <x-empty-state title="No posts yet" description="Be the first to start a conversation." />
        </div>
        @endforelse
    </div>
</div>
@endsection
