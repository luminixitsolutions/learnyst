@extends('layouts.app')
@section('title', $mode==='create'?'New Course':'Edit Course')
@section('page-title', $mode==='create'?'New Course':'Edit Course')
@section('breadcrumb', 'Instructor / Courses')
@section('content')
<div class="max-w-2xl space-y-4">
<a href="{{ route('instructor.courses.index') }}" class="text-sm text-slate-500">← Courses</a>
<form method="POST" action="{{ $mode==='create'?route('instructor.courses.store'):route('instructor.courses.update',$course) }}" class="glass-card rounded-2xl p-6 space-y-4">
@csrf @if($mode==='edit')@method('PUT')@endif
<x-form-input label="Title" name="title" :value="old('title',$course->title)" required />
<x-form-input label="Subtitle" name="subtitle" :value="old('subtitle',$course->subtitle)" />
<div><label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
<textarea name="description" rows="4" class="panel-input w-full">{{ old('description',$course->description) }}</textarea></div>
<div><label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
<select name="status" class="panel-input w-full">@foreach(['draft','published','unpublished'] as $s)<option value="{{ $s }}" @selected(old('status',$course->status)===$s)>{{ ucfirst($s) }}</option>@endforeach</select></div>
<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_free" value="1" @checked(old('is_free',$course->is_free))> Free course</label>
<x-form-input label="Price" name="price" type="number" :value="old('price',$course->price)" />
<button class="panel-btn-primary">Save</button>
</form></div>
@endsection
