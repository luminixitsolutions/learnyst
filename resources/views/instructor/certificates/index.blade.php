@extends('layouts.app')
@section('title', 'Certificates')
@section('page-title', 'Certificates')
@section('breadcrumb', 'Instructor / Certificates')
@section('content')
<div class="space-y-6">
<div class="flex justify-between"><p class="text-sm text-slate-500">Certificates for your courses.</p>
<a href="{{ route('instructor.certificates.create') }}" class="panel-btn-primary text-sm">Issue certificate</a></div>
@if(session('success'))<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
<div class="glass-card rounded-2xl overflow-hidden">
@if($certificates->count())
<table class="w-full text-sm panel-table"><thead><tr class="text-left"><th class="px-6 py-3">Number</th><th class="px-6 py-3">Learner</th><th class="px-6 py-3">Course</th><th class="px-6 py-3">Issued</th></tr></thead>
<tbody>@foreach($certificates as $c)<tr>
<td class="px-6 py-3 font-mono text-xs">{{ $c->certificate_number }}</td>
<td class="px-6 py-3">{{ $c->user?->name }}</td>
<td class="px-6 py-3">{{ $c->course?->title }}</td>
<td class="px-6 py-3">{{ $c->issued_at?->format('M d, Y') }}</td>
</tr>@endforeach</tbody></table>
<div class="px-6 py-4">{{ $certificates->links() }}</div>
@else<x-empty-state title="No certificates yet" />@endif
</div></div>
@endsection
