@extends('layouts.app')

@section('title', 'Study Planner')
@section('page-title', 'AI Study Planner')
@section('breadcrumb', 'Student / AI')

@section('content')
<div class="space-y-6 max-w-3xl">
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('learner.ai.planner.create') }}" class="space-y-4">
            @csrf
            <x-form-input label="What should we plan?" name="prompt" type="textarea" required placeholder="I have 2 weeks for Algebra midterms..." />
            <button class="px-5 py-2.5 rounded-xl panel-btn-primary">Generate plan</button>
        </form>
    </div>
    @foreach($plans as $plan)
    <div class="glass-card rounded-2xl p-6">
        <h3 class="font-bold text-slate-800">{{ $plan->title }} · {{ $plan->created_at->format('M d') }}</h3>
        <pre class="mt-3 text-sm text-slate-700 whitespace-pre-wrap">{{ $plan->output }}</pre>
    </div>
    @endforeach
</div>
@endsection
