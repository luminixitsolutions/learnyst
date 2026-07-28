@extends('layouts.app')
@section('title', 'Issue Certificate')
@section('page-title', 'Issue Certificate')
@section('breadcrumb', 'Instructor / Certificates')
@section('content')
<div class="max-w-xl space-y-4">
<a href="{{ route('instructor.certificates.index') }}" class="text-sm text-slate-500">← Certificates</a>
<form method="POST" action="{{ route('instructor.certificates.store') }}" class="glass-card rounded-2xl p-6 space-y-4">@csrf
<div><label class="block text-sm font-semibold mb-1">Learner</label>
<select name="user_id" required class="panel-input w-full">@foreach($learners as $l)<option value="{{ $l->id }}">{{ $l->name }} ({{ $l->email }})</option>@endforeach</select></div>
<div><label class="block text-sm font-semibold mb-1">Course</label>
<select name="course_id" required class="panel-input w-full">@foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach</select></div>
<div><label class="block text-sm font-semibold mb-1">Notes</label><input name="notes" class="panel-input w-full" placeholder="Optional"></div>
<button class="panel-btn-primary">Issue</button>
</form></div>
@endsection
