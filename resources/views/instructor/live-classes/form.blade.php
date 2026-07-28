@extends('layouts.app')
@section('title', $mode==='create'?'Schedule Class':'Edit Class')
@section('page-title', $mode==='create'?'Schedule Class':'Edit Class')
@section('breadcrumb', 'Instructor / Live Classes')
@section('content')
<div class="max-w-2xl space-y-4">
<a href="{{ route('instructor.live-classes.index') }}" class="text-sm text-slate-500">← Live classes</a>
<form method="POST" action="{{ $mode==='create'?route('instructor.live-classes.store'):route('instructor.live-classes.update',$event) }}" class="glass-card rounded-2xl p-6 space-y-4">
@csrf @if($mode==='edit')@method('PUT')@endif
<x-form-input label="Title" name="title" :value="old('title',$event->title)" required />
<div><label class="block text-sm font-semibold mb-1">Course</label>
<select name="course_id" required class="panel-input w-full">@foreach($courses as $c)<option value="{{ $c->id }}" @selected(old('course_id',$event->course_id)==$c->id)>{{ $c->title }}</option>@endforeach</select></div>
<div><label class="block text-sm font-semibold mb-1">Description</label><textarea name="description" rows="3" class="panel-input w-full">{{ old('description',$event->description) }}</textarea></div>
<div class="grid grid-cols-2 gap-3">
<div><label class="block text-sm font-semibold mb-1">Starts</label><input type="datetime-local" name="starts_at" class="panel-input w-full" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}" required></div>
<div><label class="block text-sm font-semibold mb-1">Ends</label><input type="datetime-local" name="ends_at" class="panel-input w-full" value="{{ old('ends_at', optional($event->ends_at)->format('Y-m-d\TH:i')) }}"></div>
</div>
<x-form-input label="Meeting URL" name="meeting_url" :value="old('meeting_url',$event->meeting_url)" />
<x-form-input label="Recording URL" name="recording_url" :value="old('recording_url',$event->recording_url)" />
<div><label class="block text-sm font-semibold mb-1">Platform</label><input name="platform" class="panel-input w-full" value="{{ old('platform',$event->platform ?? 'zoom') }}"></div>
<div><label class="block text-sm font-semibold mb-1">Status</label>
<select name="status" class="panel-input w-full">@foreach(['scheduled','live','completed','cancelled'] as $s)<option value="{{ $s }}" @selected(old('status',$event->status)===$s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
<button class="panel-btn-primary">Save</button>
</form></div>
@endsection
