@extends('layouts.app')
@section('title', 'Instructor Reports')
@section('page-title', 'Reports')
@section('breadcrumb', 'Instructor / Reports')
@section('content')
<div class="space-y-6">
<div class="grid grid-cols-2 xl:grid-cols-5 gap-4">
<x-stat-card title="Learners" :value="number_format($stats['learners'])" />
<x-stat-card title="Grading backlog" :value="number_format($stats['grading_backlog'])" />
<x-stat-card title="Attendance marks" :value="number_format($stats['attendance'])" />
<x-stat-card title="Classes held" :value="number_format($stats['classes_held'])" />
<x-stat-card title="Open doubts" :value="number_format($stats['open_doubts'])" />
</div>
<div class="glass-card rounded-2xl overflow-hidden">
<table class="w-full text-sm panel-table"><thead><tr class="text-left"><th class="px-6 py-3">Course</th><th class="px-6 py-3">Learners</th><th class="px-6 py-3">Avg progress</th></tr></thead>
<tbody>@forelse($completion as $row)<tr>
<td class="px-6 py-3">{{ $row->course?->title ?? 'Course #'.$row->course_id }}</td>
<td class="px-6 py-3">{{ $row->learners }}</td>
<td class="px-6 py-3">{{ number_format((float)$row->avg_progress, 1) }}%</td>
</tr>@empty<tr><td colspan="3" class="px-6 py-8 text-center text-slate-500">No enrollment data</td></tr>@endforelse</tbody></table>
</div></div>
@endsection
