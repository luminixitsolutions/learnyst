@extends('layouts.app')
@section('title', $job->title)
@section('page-title', $job->title)
@section('breadcrumb', 'Student / Placements')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-500">{{ $job->company?->name }} · {{ $job->location }} · {{ ucfirst($job->type) }}</p>
        <div class="prose prose-sm mt-4 text-slate-700 whitespace-pre-line">{{ $job->description }}</div>
        @if($job->requirements)<h4 class="font-semibold mt-4">Requirements</h4><div class="text-sm text-slate-600 whitespace-pre-line">{{ $job->requirements }}</div>@endif
    </div>
    @if($application)
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            You applied · Status: <strong>{{ $application->status }}</strong>
            @if($application->interview_at) · Interview: {{ $application->interview_at->format('M d, Y H:i') }} ({{ $application->interview_mode }})@endif
        </div>
    @else
        <form method="POST" action="{{ route('learner.placements.apply', $job) }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
            @csrf
            <h3 class="font-bold text-slate-800">Apply + resume builder fields</h3>
            <x-form-input label="Summary" name="resume_summary" type="textarea" />
            <x-form-input label="Skills" name="skills" placeholder="PHP, Laravel, SQL" />
            <x-form-input label="Education" name="education" />
            <x-form-input label="Cover letter" name="cover_letter" type="textarea" />
            <div><label class="text-sm text-slate-600">Upload resume (optional)</label><input type="file" name="resume" class="mt-1 block w-full text-sm"></div>
            <button class="px-5 py-2.5 rounded-xl panel-btn-primary">Submit application</button>
        </form>
    @endif
</div>
@endsection
