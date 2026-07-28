@extends('layouts.app')

@section('title', 'AI Tools')
@section('page-title', 'AI Tools')
@section('breadcrumb', 'Instructor / AI')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('instructor.ai.generate') }}" class="space-y-4">
            @csrf
            <x-form-input label="Feature" name="feature" type="select" required>
                @foreach($features as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Title" name="title" />
            <x-form-input label="Prompt" name="prompt" type="textarea" required />
            <button class="px-5 py-2.5 rounded-xl panel-btn-primary">Generate draft</button>
        </form>
    </div>
    <div class="glass-card rounded-2xl p-6 space-y-4">
        <h3 class="font-bold text-slate-800">Your drafts</h3>
        @forelse($drafts as $draft)
            <div class="border-b border-slate-100 pb-3">
                <div class="font-semibold text-slate-800">{{ $draft->title }} <span class="text-xs text-slate-400">{{ $draft->status }}</span></div>
                <pre class="text-xs text-slate-600 mt-2 whitespace-pre-wrap max-h-40 overflow-y-auto">{{ $draft->output }}</pre>
            </div>
        @empty
            <p class="text-sm text-slate-500">No drafts yet.</p>
        @endforelse
        {{ $drafts->links() }}
    </div>
</div>
@endsection
