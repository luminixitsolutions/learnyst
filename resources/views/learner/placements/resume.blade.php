@extends('layouts.app')
@section('title', 'Resume Builder')
@section('page-title', 'Resume Builder')
@section('breadcrumb', 'Student / Placements')

@section('content')
<div class="glass-card rounded-2xl p-6 max-w-2xl space-y-3" id="resume-print">
    <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
    <p class="text-sm text-slate-600">{{ $user->email }} · {{ $user->phone }}</p>
    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $user->bio ?: 'Add a bio on your profile, then use apply forms to attach skills/education per job.' }}</p>
    <button onclick="window.print()" class="no-print px-4 py-2 rounded-xl panel-btn-primary text-sm">Print resume</button>
</div>
<style>@media print{.no-print{display:none} body *{visibility:hidden} #resume-print,#resume-print *{visibility:visible} #resume-print{position:absolute;left:0;top:0}}</style>
@endsection
