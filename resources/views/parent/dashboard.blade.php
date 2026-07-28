@extends('layouts.app')
@section('title', 'Parent Dashboard')
@section('page-title', 'Parent Dashboard')
@section('breadcrumb', 'Parent Portal')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Linked children" :value="number_format($summaries->count())" />
        <x-stat-card title="Pending fees" :value="number_format($pendingFees)" />
        <x-stat-card title="Upcoming classes" :value="number_format($upcomingClasses->count())" />
        <x-stat-card title="Notifications" :value="number_format($notifications->count())" />
    </div>

    @if($summaries->isEmpty())
        <div class="glass-card rounded-2xl p-8 text-center">
            <x-empty-state title="No linked learners" description="Ask your institute admin to link your parent account to a learner profile under Parent Links." />
        </div>
    @else
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-800">Children overview</div>
            <table class="w-full text-sm panel-table">
                <thead><tr><th class="px-6 py-3 text-left">Learner</th><th class="px-6 py-3 text-left">Courses</th><th class="px-6 py-3 text-left">Progress</th><th class="px-6 py-3 text-left">Attendance</th><th class="px-6 py-3 text-left">Upcoming</th><th></th></tr></thead>
                <tbody>
                @foreach($summaries as $row)
                    <tr>
                        <td class="px-6 py-3 font-medium">{{ $row['learner']->name }}</td>
                        <td class="px-6 py-3">{{ $row['courses'] }}</td>
                        <td class="px-6 py-3">{{ $row['progress'] }}%</td>
                        <td class="px-6 py-3">{{ $row['attendance'] }}</td>
                        <td class="px-6 py-3">{{ $row['upcoming'] }}</td>
                        <td class="px-6 py-3 text-right"><a href="{{ route('parent.learners.show', $row['learner']) }}" class="text-indigo-600 text-xs font-semibold">View</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="glass-card rounded-2xl p-5">
            <h3 class="font-bold text-slate-800 mb-3">Upcoming classes</h3>
            @forelse($upcomingClasses as $event)
                <div class="py-2 border-b border-slate-100 text-sm">
                    <div class="font-medium">{{ $event->title }}</div>
                    <div class="text-xs text-slate-500">{{ $event->course?->title }} · {{ $event->starts_at?->format('M d, Y H:i') }}</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No upcoming classes.</p>
            @endforelse
        </div>
        <div class="glass-card rounded-2xl p-5">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-slate-800">Recent notifications</h3>
                <a href="{{ route('parent.notifications') }}" class="text-xs text-indigo-600 font-semibold">All</a>
            </div>
            @forelse($notifications as $n)
                <div class="py-2 border-b border-slate-100 text-sm">
                    <div class="font-medium">{{ $n->title }}</div>
                    <div class="text-xs text-slate-500">{{ $n->created_at?->diffForHumans() }}</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No notifications yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
