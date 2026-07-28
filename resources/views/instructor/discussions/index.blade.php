@extends('layouts.app')
@section('title', 'Doubts')
@section('page-title', 'Doubts / Discussions')
@section('breadcrumb', 'Instructor / Discussions')
@section('content')
<div class="space-y-6">
<form method="GET" class="flex gap-2">
<a href="{{ route('instructor.discussions.index') }}" class="px-3 py-1.5 rounded-xl text-sm {{ !request('status')?'bg-brand-600 text-white':'bg-slate-100' }}">All</a>
<a href="{{ route('instructor.discussions.index',['status'=>'open']) }}" class="px-3 py-1.5 rounded-xl text-sm {{ request('status')==='open'?'bg-brand-600 text-white':'bg-slate-100' }}">Open</a>
<a href="{{ route('instructor.discussions.index',['status'=>'resolved']) }}" class="px-3 py-1.5 rounded-xl text-sm {{ request('status')==='resolved'?'bg-brand-600 text-white':'bg-slate-100' }}">Resolved</a>
</form>
<div class="glass-card rounded-2xl overflow-hidden">
@forelse($discussions as $d)
<a href="{{ route('instructor.discussions.show', $d) }}" class="block px-6 py-4 border-b border-slate-100 hover:bg-slate-50">
<div class="flex justify-between"><span class="font-medium text-slate-800">{{ $d->title }}</span>
<x-badge :type="$d->is_resolved?'success':'warning'">{{ $d->is_resolved?'Resolved':'Open' }}</x-badge></div>
<div class="text-xs text-slate-400 mt-1">{{ $d->course?->title }} · {{ $d->user?->name }} · {{ $d->comments_count }} replies</div>
</a>
@empty<x-empty-state title="No discussions" />@endforelse
<div class="px-6 py-4">{{ $discussions->links() }}</div>
</div></div>
@endsection
