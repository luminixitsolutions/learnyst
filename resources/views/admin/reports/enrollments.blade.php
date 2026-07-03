@extends('layouts.app')

@section('title', 'Enrollments Report')
@section('page-title', 'Enrollments Report')
@section('breadcrumb', 'Reports / Enrollments')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $enrollments->total() }} total enrollment records</p>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white">← All Reports</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($enrollments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Learner</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Target</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Progress</th>
                        <th class="px-6 py-4 font-medium">Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.enrollments.history', $enrollment->user) }}" class="text-white hover:text-indigo-600">{{ $enrollment->user?->name }}</a>
                        </td>
                        <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($enrollment->enrollment_type) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-300">
                            @if($enrollment->enrollment_type === 'course') {{ $enrollment->course?->title ?? '—' }}
                            @elseif($enrollment->enrollment_type === 'batch') {{ $enrollment->batch?->title ?? '—' }}
                            @else {{ $enrollment->bundle?->title ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($enrollment->status) { 'active' => 'success', 'expired' => 'warning', 'revoked' => 'danger', default => 'default' }">{{ ucfirst($enrollment->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $enrollment->progress ? number_format($enrollment->progress, 0).'%' : '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $enrollments->links() }}</div>
        @else
        <x-empty-state title="No enrollment data" />
        @endif
    </div>
</div>
@endsection
