@extends('layouts.app')

@section('title', 'Linked Learners')
@section('page-title', 'Linked Learners')
@section('breadcrumb', 'Parent / Learners')

@section('content')
<div class="glass-card rounded-2xl p-8">
    <x-empty-state title="No linked learners yet" description="Ask your institute administrator to link your parent account to your child’s learner profile (Phase 4)." />
</div>
@endsection
