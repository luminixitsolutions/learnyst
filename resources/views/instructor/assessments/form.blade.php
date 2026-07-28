@extends('layouts.app')
@section('title', 'New '.ucfirst($type))
@section('page-title', 'New '.ucfirst($type))
@section('breadcrumb', 'Instructor / Assessments')
@section('content')
<div class="max-w-2xl space-y-4">
<a href="{{ route('instructor.assessments.index') }}" class="text-sm text-slate-500">← Assessments</a>
<form method="POST" action="{{ route('instructor.assessments.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4" x-data="{ courseId: '' }">
@csrf
<input type="hidden" name="lesson_type" value="{{ $type }}">
<div><label class="block text-sm font-semibold mb-1">Course</label>
<select name="course_id" class="panel-input w-full" x-model="courseId" required>
<option value="">Select…</option>
@foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
</select></div>
<div><label class="block text-sm font-semibold mb-1">Section</label>
<select name="section_id" class="panel-input w-full" required>
<option value="">Select course first</option>
@foreach($courses as $c)
@foreach($c->sections as $s)
<option value="{{ $s->id }}" x-show="courseId == '{{ $c->id }}'">{{ $c->title }} — {{ $s->title }}</option>
@endforeach
@endforeach
</select></div>
<x-form-input label="Title" name="title" required />
<div><label class="block text-sm font-semibold mb-1">Description</label><textarea name="description" rows="4" class="panel-input w-full"></textarea></div>
<div class="grid grid-cols-2 gap-3">
<div><label class="block text-sm font-semibold mb-1">Due date</label><input type="date" name="due_date" class="panel-input w-full"></div>
<div><label class="block text-sm font-semibold mb-1">Marks</label><input type="number" name="marks" class="panel-input w-full"></div>
</div>
<div><label class="block text-sm font-semibold mb-1">Status</label>
<select name="status" class="panel-input w-full"><option value="draft">Draft</option><option value="published">Published</option></select></div>
<div><label class="block text-sm font-semibold mb-1">File</label><input type="file" name="file" class="panel-input w-full"></div>
<button class="panel-btn-primary">Create</button>
</form></div>
@endsection
