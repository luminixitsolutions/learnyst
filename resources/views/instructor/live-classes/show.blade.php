@extends('layouts.app')
@section('title', $event->title)
@section('page-title', $event->title)
@section('breadcrumb', 'Instructor / Live Classes')
@section('content')
<div class="space-y-6 max-w-3xl">
<div class="flex justify-between"><a href="{{ route('instructor.live-classes.index') }}" class="text-sm text-slate-500">← Classes</a>
<a href="{{ route('instructor.live-classes.edit', $event) }}" class="panel-btn-secondary text-sm">Edit</a></div>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="glass-card rounded-2xl p-6 text-sm space-y-2">
<div><span class="text-slate-500">Course:</span> {{ $event->course?->title }}</div>
<div><span class="text-slate-500">When:</span> {{ $event->starts_at?->format('M d, Y H:i') }}</div>
<div><span class="text-slate-500">Status:</span> {{ $event->status }}</div>
<div><span class="text-slate-500">Meeting:</span> <a class="text-indigo-600 break-all" href="{{ $event->meeting_url }}" target="_blank">{{ $event->meeting_url ?: '—' }}</a></div>
<div><span class="text-slate-500">Recording:</span> <a class="text-indigo-600 break-all" href="{{ $event->recording_url }}" target="_blank">{{ $event->recording_url ?: '—' }}</a></div>
</div>
<div class="glass-card rounded-2xl p-6">
<h3 class="font-bold mb-3">Attendance ({{ $attendance->count() }})</h3>
@foreach($attendance as $row)
<div class="flex justify-between text-sm py-2 border-b border-slate-100"><span>{{ $row->user?->name }}</span><span class="text-slate-500">{{ $row->attended_at?->format('M d H:i') }}</span></div>
@endforeach
<form method="POST" action="{{ route('instructor.live-classes.attendance', $event) }}" class="flex gap-2 mt-4">@csrf
<select name="user_id" required class="panel-input flex-1">
<option value="">Select learner</option>
@foreach($learners as $learner)
<option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
@endforeach
</select>
<button class="panel-btn-primary text-sm">Mark attended</button>
</form>
</div></div>
@endsection
