@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('breadcrumb', 'Parent / Notifications')
@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <ul class="divide-y divide-slate-100">
        @forelse($notifications as $n)
            <li class="px-6 py-4">
                <div class="flex justify-between gap-4">
                    <div>
                        <p class="font-medium text-slate-800">{{ $n->title }}</p>
                        @if($n->body)<p class="text-sm text-slate-600 mt-1">{{ $n->body }}</p>@endif
                        <p class="text-xs text-slate-400 mt-1">{{ $n->user?->name }} · {{ $n->type }}</p>
                    </div>
                    <span class="text-xs text-slate-500 whitespace-nowrap">{{ $n->created_at?->diffForHumans() }}</span>
                </div>
            </li>
        @empty
            <li class="px-6 py-10"><x-empty-state title="No notifications" description="Alerts for linked learners will appear here." /></li>
        @endforelse
    </ul>
    <div class="px-6 py-4">{{ $notifications->links() }}</div>
</div>
@endsection
