@extends('layouts.app')
@section('title', $user->name)
@section('page-title', $user->name)
@section('breadcrumb', 'Instructor / Students')
@section('content')
<div class="space-y-6 max-w-3xl">
<a href="{{ route('instructor.students.index') }}" class="text-sm text-slate-500">← Students</a>
<div class="glass-card rounded-2xl p-5 text-sm"><div class="font-semibold">{{ $user->name }}</div><div class="text-slate-500">{{ $user->email }}</div></div>
@foreach($enrollments as $enrollment)
@php $p = $progress[$enrollment->course_id] ?? ['pct'=>0,'done'=>0,'total'=>0]; @endphp
<div class="glass-card rounded-2xl p-5">
<div class="flex justify-between mb-2"><span class="font-medium">{{ $enrollment->course?->title }}</span><span class="text-sm font-semibold">{{ $p['pct'] }}%</span></div>
<div class="h-2 rounded-full bg-slate-100 overflow-hidden"><div class="h-full bg-teal-500" style="width: {{ $p['pct'] }}%"></div></div>
<div class="text-xs text-slate-400 mt-1">{{ $p['done'] }}/{{ $p['total'] }} lessons · enrollment {{ $enrollment->status }}</div>
</div>
@endforeach
</div>
@endsection
