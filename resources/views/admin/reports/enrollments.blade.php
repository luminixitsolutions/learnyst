@extends('layouts.app')

@section('title', 'Enrollments Report')
@section('page-title', 'Learner Enrollments Report')
@section('breadcrumb', 'Reports / Enrollments')

@section('content')
<div class="space-y-6">
    <x-report-toolbar searchPlaceholder="Search by email or name..." :showDateRange="true" :from="request('from')" :to="request('to')">
        <x-slot:filters>
            <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Status</option>
                @foreach(['active','expired','revoked','completed'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="type" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Types</option>
                @foreach(['course','batch','bundle'] as $tp)
                    <option value="{{ $tp }}" @selected(request('type') === $tp)>{{ ucfirst($tp) }}</option>
                @endforeach
            </select>
            <select name="course_id" class="px-3 py-2 rounded-xl border border-slate-200 text-sm">
                <option value="">All Products</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </x-slot:filters>
    </x-report-toolbar>

    <p class="text-sm text-slate-500">{{ $enrollments->total() }} enrollment records</p>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($enrollments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                    <th class="px-6 py-4 font-medium">Product / Course</th>
                    <th class="px-6 py-4 font-medium">Learner</th>
                    <th class="px-6 py-4 font-medium">Email</th>
                    <th class="px-6 py-4 font-medium">Mobile</th>
                    <th class="px-6 py-4 font-medium">Enrollment Date</th>
                    <th class="px-6 py-4 font-medium">Access Start</th>
                    <th class="px-6 py-4 font-medium">Access Expiry</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-800">
                            @if($enrollment->enrollment_type === 'course') {{ $enrollment->course?->title ?? '—' }}
                            @elseif($enrollment->enrollment_type === 'batch') {{ $enrollment->batch?->title ?? '—' }}
                            @else {{ $enrollment->bundle?->title ?? '—' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.reports.learner-profile', $enrollment->user) }}" class="text-indigo-600 hover:underline">{{ $enrollment->user?->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->user?->email }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->user?->phone ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->access_starts_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($enrollment->status) { 'active' => 'success', 'expired' => 'warning', 'revoked' => 'danger', default => 'default' }">{{ ucfirst($enrollment->status) }}</x-badge>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $enrollments->links() }}</div>
        @else
        <x-empty-state title="No enrollment data" description="Try adjusting your search or filters." />
        @endif
    </div>
</div>
@endsection
