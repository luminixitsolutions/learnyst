@extends('layouts.app')

@section('title', 'Parent Dashboard')
@section('page-title', 'Parent Dashboard')
@section('breadcrumb', 'Parent Portal')

@section('content')
<div class="glass-card rounded-2xl p-8 text-center max-w-lg mx-auto">
    <h2 class="text-lg font-bold text-slate-800">Parent Portal</h2>
    <p class="text-sm text-slate-600 mt-2">View linked learners’ attendance, progress, and fees in Phase 4. Your institute admin will link your account to learners.</p>
    <a href="{{ route('parent.learners') }}" class="inline-flex mt-6 panel-btn-primary">View linked learners</a>
</div>
@endsection
