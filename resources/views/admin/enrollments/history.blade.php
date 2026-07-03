@extends('layouts.app')

@section('title', 'Enrollment History — '.$learner->name)
@section('page-title', 'Enrollment History')
@section('breadcrumb', 'Enrollments / '.$learner->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-lg font-bold text-indigo-600">{{ strtoupper(substr($learner->name, 0, 1)) }}</div>
            <div>
                <p class="text-slate-800 font-semibold">{{ $learner->name }}</p>
                <p class="text-sm text-slate-500">{{ $learner->email }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.learners.show', $learner) }}" class="panel-btn-secondary">View Profile</a>
            <a href="{{ route('admin.enrollments.index') }}" class="panel-btn-secondary">Back</a>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($enrollments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Target</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Progress</th>
                        <th class="px-6 py-4 font-medium">Access Period</th>
                        <th class="px-6 py-4 font-medium">Order</th>
                        <th class="px-6 py-4 font-medium">Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($enrollment->enrollment_type) }}</x-badge></td>
                        <td class="px-6 py-4 text-white">
                            @if($enrollment->enrollment_type === 'course')
                                {{ $enrollment->course?->title ?? '—' }}
                            @elseif($enrollment->enrollment_type === 'batch')
                                {{ $enrollment->batch?->title ?? '—' }}
                            @else
                                {{ $enrollment->bundle?->title ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($enrollment->status) { 'active' => 'success', 'expired' => 'warning', 'revoked' => 'danger', default => 'default' }">{{ ucfirst($enrollment->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $enrollment->progress ? number_format($enrollment->progress, 0).'%' : '—' }}</td>
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            {{ $enrollment->access_starts_at?->format('M d, Y') ?? '—' }}
                            @if($enrollment->expires_at) → {{ $enrollment->expires_at->format('M d, Y') }} @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($enrollment->order)
                                <a href="{{ route('admin.orders.show', $enrollment->order) }}" class="text-indigo-600 hover:text-indigo-800">{{ $enrollment->order->order_number }}</a>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $enrollments->links() }}</div>
        @else
        <x-empty-state title="No enrollment history" description="This learner has no enrollments yet." />
        @endif
    </div>
</div>
@endsection
