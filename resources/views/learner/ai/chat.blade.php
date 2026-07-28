@extends('layouts.app')

@section('title', 'AI Doubt Chat')
@section('page-title', 'Doubt Assistant')
@section('breadcrumb', 'Student / AI')

@section('content')
<div class="max-w-3xl space-y-4">
    <div class="glass-card rounded-2xl p-4 max-h-[60vh] overflow-y-auto space-y-3">
        @forelse($messages as $msg)
            <div class="{{ $msg->role === 'user' ? 'text-right' : 'text-left' }}">
                <div class="inline-block max-w-[85%] rounded-2xl px-4 py-2 text-sm {{ $msg->role === 'user' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-800' }}">
                    {{ $msg->content }}
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 text-center py-8">Ask a doubt to get started.</p>
        @endforelse
    </div>
    <form method="POST" action="{{ route('learner.ai.chat.send') }}" class="glass-card rounded-2xl p-4 flex gap-3">
        @csrf
        <input name="message" required class="flex-1 rounded-xl border-slate-300" placeholder="Type your doubt...">
        <button class="px-4 py-2 rounded-xl panel-btn-primary">Send</button>
    </form>
    <a href="{{ route('learner.ai.planner') }}" class="text-sm text-emerald-600">Open study planner →</a>
</div>
@endsection
