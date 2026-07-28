@extends('layouts.app')
@section('title', 'Students')
@section('page-title', 'Students')
@section('breadcrumb', 'Instructor / Students')
@section('content')
<div class="space-y-6">
<form method="GET" class="glass-card rounded-2xl p-4 flex gap-3 items-end">
<div><label class="text-xs text-slate-500">Course</label>
<select name="course_id" class="panel-input"><option value="">All</option>
@foreach($courses as $c)<option value="{{ $c->id }}" @selected(request('course_id')==$c->id)>{{ $c->title }}</option>@endforeach
</select></div>
<button class="panel-btn-primary text-sm">Filter</button>
</form>
<div class="glass-card rounded-2xl overflow-hidden">
@if($students->count())
<table class="w-full text-sm panel-table"><thead><tr class="text-left"><th class="px-6 py-3">Student</th><th class="px-6 py-3">Certificates</th><th class="px-6 py-3 text-right">View</th></tr></thead>
<tbody>@foreach($students as $student)<tr>
<td class="px-6 py-3"><div class="font-medium">{{ $student->name }}</div><div class="text-xs text-slate-400">{{ $student->email }}</div></td>
<td class="px-6 py-3">{{ $student->certificates_count }}</td>
<td class="px-6 py-3 text-right"><a href="{{ route('instructor.students.show', $student) }}" class="text-indigo-600 text-xs font-semibold">Progress</a></td>
</tr>@endforeach</tbody></table>
<div class="px-6 py-4">{{ $students->links() }}</div>
@else<x-empty-state title="No students" />@endif
</div></div>
@endsection
