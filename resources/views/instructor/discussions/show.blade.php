@extends('layouts.app')
@section('title', $discussion->title)
@section('page-title', $discussion->title)
@section('breadcrumb', 'Instructor / Discussions')
@section('content')
<div class="space-y-6 max-w-3xl">
<a href="{{ route('instructor.discussions.index') }}" class="text-sm text-slate-500">← Discussions</a>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="glass-card rounded-2xl p-6 space-y-2">
<div class="flex justify-between"><h2 class="font-bold text-lg">{{ $discussion->title }}</h2>
@unless($discussion->is_resolved)
<form method="POST" action="{{ route('instructor.discussions.resolve', $discussion) }}">@csrf<button class="panel-btn-secondary text-sm">Mark resolved</button></form>
@endunless
</div>
<p class="text-sm text-slate-600 whitespace-pre-line">{{ $discussion->body }}</p>
<div class="text-xs text-slate-400">{{ $discussion->user?->name }} · {{ $discussion->course?->title }}</div>
</div>
@foreach($discussion->comments as $comment)
<div class="rounded-xl border border-slate-200 p-4 text-sm">
<div class="font-semibold">{{ $comment->user?->name }} <span class="text-xs text-slate-400 font-normal">{{ $comment->created_at?->format('M d H:i') }}</span></div>
<p class="mt-1 whitespace-pre-line">{{ $comment->body }}</p>
</div>
@endforeach
<form method="POST" action="{{ route('instructor.discussions.reply', $discussion) }}" class="glass-card rounded-2xl p-5 space-y-3">@csrf
<textarea name="body" rows="4" required class="panel-input w-full" placeholder="Write an answer…"></textarea>
<button class="panel-btn-primary text-sm">Reply</button>
</form>
</div>
@endsection
