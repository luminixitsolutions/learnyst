@extends('layouts.app')
@section('title', 'Submissions — '.$lesson->title)
@section('page-title', 'Submissions')
@section('breadcrumb', 'Instructor / Assessments')
@section('content')
<div class="space-y-6 max-w-4xl">
<a href="{{ route('instructor.assessments.index') }}" class="text-sm text-slate-500">← Assessments</a>
<h2 class="text-lg font-bold">{{ $lesson->title }}</h2>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
@forelse($submissions as $submission)
<div class="glass-card rounded-2xl p-5 space-y-3">
<div class="flex justify-between text-sm"><div><span class="font-semibold">{{ $submission->learner?->name }}</span> · {{ $submission->status }}</div>
<span class="text-slate-400">{{ $submission->submitted_at?->format('M d, Y H:i') }}</span></div>
@if($submission->content)<p class="text-sm text-slate-700 whitespace-pre-line">{{ $submission->content }}</p>@endif
@if($submission->file_path)<a class="text-xs text-indigo-600" href="{{ asset('storage/'.$submission->file_path) }}" target="_blank">Download file</a>@endif
<form method="POST" action="{{ route('instructor.assessments.grade', $submission) }}" class="grid grid-cols-1 md:grid-cols-4 gap-2 pt-2 border-t">@csrf
<input type="number" step="0.01" name="score" value="{{ $submission->score }}" placeholder="Score" class="panel-input" required>
<input name="feedback" value="{{ $submission->feedback }}" placeholder="Feedback" class="panel-input md:col-span-2">
<select name="status" class="panel-input"><option value="graded">Graded</option><option value="resubmit">Allow resubmit</option></select>
<label class="text-xs flex items-center gap-1 md:col-span-3"><input type="checkbox" name="allow_resubmit" value="1"> Allow resubmit</label>
<button class="panel-btn-primary text-sm">Save grade</button>
</form>
</div>
@empty<x-empty-state title="No submissions yet" />@endforelse
<div>{{ $submissions->links() }}</div>
</div>
@endsection
