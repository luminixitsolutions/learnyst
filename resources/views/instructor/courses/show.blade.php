@extends('layouts.app')
@section('title', $course->title)
@section('page-title', $course->title)
@section('breadcrumb', 'Instructor / Courses')
@section('content')
<div class="space-y-6 max-w-4xl">
<div class="flex flex-wrap justify-between gap-3">
<a href="{{ route('instructor.courses.index') }}" class="text-sm text-slate-500">← Courses</a>
<a href="{{ route('instructor.courses.edit', $course) }}" class="panel-btn-secondary text-sm">Edit details</a>
</div>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="grid grid-cols-3 gap-4"><x-stat-card title="Status" :value="ucfirst($course->status)" /><x-stat-card title="Enrollments" :value="number_format($course->enrollments_count)" /><x-stat-card title="Sections" :value="number_format($course->sections->count())" /></div>
<form method="POST" action="{{ route('instructor.courses.sections.store', $course) }}" class="glass-card rounded-2xl p-4 flex gap-3 items-end">@csrf
<div class="flex-1"><label class="text-xs text-slate-500">New section</label><input name="title" required class="panel-input w-full" placeholder="Section title"></div>
<button class="panel-btn-primary text-sm">Add</button></form>
@foreach($course->sections as $section)
<div class="glass-card rounded-2xl p-5 space-y-3">
<h3 class="font-bold text-slate-800">{{ $section->title }}</h3>
@foreach($section->lessons as $lesson)
<div class="flex justify-between text-sm py-2 border-b border-slate-100"><span>{{ $lesson->title }} <span class="text-xs text-slate-400">({{ $lesson->lesson_type }})</span></span></div>
@endforeach
<form method="POST" action="{{ route('instructor.courses.lessons.store', [$course, $section]) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-2 pt-2">@csrf
<input name="title" required placeholder="Lesson title" class="panel-input">
<select name="lesson_type" class="panel-input"><option value="video">Video</option><option value="text">Text</option><option value="pdf">PDF</option><option value="quiz">Quiz</option><option value="assignment">Assignment</option><option value="live_class">Live</option></select>
<input type="file" name="file" class="panel-input">
<button class="panel-btn-secondary text-sm">Add lesson</button>
</form>
</div>
@endforeach
</div>
@endsection
