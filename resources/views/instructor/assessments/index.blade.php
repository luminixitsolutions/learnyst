@extends('layouts.app')
@section('title', 'Assessments')
@section('page-title', 'Assessments')
@section('breadcrumb', 'Instructor / Assessments')
@section('content')
<div class="space-y-6">
<div class="flex flex-wrap gap-2 justify-between">
<p class="text-sm text-slate-500">Quizzes and assignments for your courses.</p>
<div class="flex gap-2">
<a href="{{ route('instructor.assessments.create', ['type'=>'assignment']) }}" class="panel-btn-primary text-sm">New assignment</a>
<a href="{{ route('instructor.assessments.create', ['type'=>'quiz']) }}" class="panel-btn-secondary text-sm">New quiz</a>
</div></div>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="glass-card rounded-2xl p-5">
<h3 class="font-bold mb-3">Assignments</h3>
@forelse($assignments as $a)
<div class="flex justify-between py-2 border-b text-sm">
<div><div class="font-medium">{{ $a->title }}</div><div class="text-xs text-slate-400">{{ $a->section?->course?->title }} · {{ $a->pending_count }} pending</div></div>
<a href="{{ route('instructor.assessments.submissions', $a) }}" class="text-indigo-600 text-xs font-semibold">Submissions</a>
</div>
@empty<p class="text-sm text-slate-500">No assignments.</p>@endforelse
</div>
<div class="glass-card rounded-2xl p-5">
<h3 class="font-bold mb-3">Quizzes</h3>
@forelse($quizzes as $q)
<div class="py-2 border-b text-sm"><div class="font-medium">{{ $q->title }}</div><div class="text-xs text-slate-400">{{ $q->section?->course?->title }}</div></div>
@empty<p class="text-sm text-slate-500">No quizzes.</p>@endforelse
</div></div>
@endsection
