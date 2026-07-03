@extends('layouts.app')

@section('title', $discussion->title)
@section('page-title', $discussion->title)
@section('breadcrumb', 'Discussions / Thread')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm text-slate-500">{{ $discussion->course?->title }} · by {{ $discussion->user?->name }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $discussion->created_at->format('M d, Y h:i A') }}</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.discussions.lock', $discussion) }}">@csrf
                <button type="submit" class="panel-btn-secondary">
                    {{ $discussion->is_locked ? 'Unlock' : 'Lock' }} Discussion
                </button>
            </form>
            <form method="POST" action="{{ route('admin.discussions.destroy', $discussion) }}">@csrf @method('DELETE')
                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="px-4 py-2 rounded-xl bg-red-600/20 text-red-400 text-sm">Delete</button>
            </form>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <p class="text-slate-300 whitespace-pre-line">{{ $discussion->body }}</p>
        @if($discussion->is_locked)
            <x-badge type="danger" class="mt-4">This discussion is locked</x-badge>
        @endif
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Comments ({{ $discussion->comments->count() }})</h3>
        <div class="space-y-4">
            @forelse($discussion->comments->whereNull('parent_id') as $comment)
            <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sm font-semibold text-slate-800">{{ $comment->user?->name }}</span>
                    <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-slate-500">{{ $comment->body }}</p>
                @foreach($comment->replies as $reply)
                <div class="ml-6 mt-3 p-3 rounded-lg bg-slate-900/60">
                    <span class="text-xs font-medium text-indigo-600">{{ $reply->user?->name }}</span>
                    <p class="text-sm text-slate-500 mt-1">{{ $reply->body }}</p>
                </div>
                @endforeach
            </div>
            @empty
            <p class="text-sm text-slate-500 text-center py-8">No comments yet</p>
            @endforelse
        </div>
    </div>

    <a href="{{ route('admin.discussions.index') }}" class="text-sm text-slate-500 hover:text-white">← Back to discussions</a>
</div>
@endsection
