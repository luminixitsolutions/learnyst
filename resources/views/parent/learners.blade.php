@extends('layouts.app')
@section('title', 'Children')
@section('page-title', 'Children / Learners')
@section('breadcrumb', 'Parent / Children')
@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    @if($learners->count())
    <table class="w-full text-sm panel-table">
        <thead><tr><th class="px-6 py-3 text-left">Name</th><th class="px-6 py-3 text-left">Email</th><th></th></tr></thead>
        <tbody>
        @foreach($learners as $learner)
            <tr>
                <td class="px-6 py-3 font-medium">{{ $learner->name }}</td>
                <td class="px-6 py-3 text-slate-500">{{ $learner->email }}</td>
                <td class="px-6 py-3 text-right"><a href="{{ route('parent.learners.show', $learner) }}" class="text-indigo-600 text-xs font-semibold">Open</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @else
    <div class="p-8"><x-empty-state title="No linked learners" description="Ask your institute admin to create a parent–learner link." /></div>
    @endif
</div>
@endsection
