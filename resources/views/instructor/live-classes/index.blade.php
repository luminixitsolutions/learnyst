@extends('layouts.app')
@section('title', 'Live Classes')
@section('page-title', 'Live Classes')
@section('breadcrumb', 'Instructor / Live Classes')
@section('content')
<div class="space-y-6">
<div class="flex justify-between"><p class="text-sm text-slate-500">Your scheduled classes.</p><a href="{{ route('instructor.live-classes.create') }}" class="panel-btn-primary text-sm">Schedule</a></div>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="glass-card rounded-2xl overflow-hidden">
@if($events->count())
<table class="w-full text-sm panel-table"><thead><tr class="text-left"><th class="px-6 py-3">Class</th><th class="px-6 py-3">When</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Open</th></tr></thead>
<tbody>@foreach($events as $event)<tr>
<td class="px-6 py-3"><div class="font-medium">{{ $event->title }}</div><div class="text-xs text-slate-400">{{ $event->course?->title }}</div></td>
<td class="px-6 py-3 text-slate-500">{{ $event->starts_at?->format('M d, Y H:i') }}</td>
<td class="px-6 py-3"><x-badge type="info">{{ $event->status }}</x-badge></td>
<td class="px-6 py-3 text-right"><a href="{{ route('instructor.live-classes.show', $event) }}" class="text-indigo-600 text-xs font-semibold">View</a></td>
</tr>@endforeach</tbody></table>
<div class="px-6 py-4">{{ $events->links() }}</div>
@else<x-empty-state title="No live classes" />@endif
</div></div>
@endsection
