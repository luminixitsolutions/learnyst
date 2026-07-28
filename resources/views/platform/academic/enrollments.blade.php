@extends('layouts.app')

@section('title', 'Enrollments')
@section('page-title', 'Enrollments')
@section('breadcrumb', 'Platform Admin / Academic / Enrollments')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Course enrollments across institutes.</p>
        <a href="{{ route('platform.academic.enrollments.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Total" :value="number_format($stats['total'])" />
        <x-stat-card title="Active" :value="number_format($stats['active'])" />
        <x-stat-card title="Expired" :value="number_format($stats['expired'])" />
        <x-stat-card title="Revoked" :value="number_format($stats['revoked'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Student or course…" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute</label>
            <select name="company_id" class="panel-input w-full">
                <option value="">All</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((string) request('company_id') === (string) $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Course</label>
            <select name="course_id" class="panel-input w-full">
                <option value="">All</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected((string) request('course_id') === (string) $course->id)>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['active','expired','revoked'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="panel-input w-full">
        </div>
        <div class="flex gap-2 xl:col-span-6">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.academic.enrollments') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($enrollments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Progress</th>
                        <th class="px-6 py-4">Enrolled</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                        @php
                            $badge = match ($enrollment->status) {
                                'active' => 'success',
                                'revoked' => 'danger',
                                default => 'warning',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $enrollment->user?->name }}</div>
                                <div class="text-xs text-slate-400">{{ $enrollment->user?->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($enrollment->course)
                                    <a href="{{ route('platform.academic.courses.show', $enrollment->course) }}" class="text-indigo-600 hover:underline">{{ $enrollment->course->title }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($enrollment->institute)
                                    <a href="{{ route('platform.companies.show', $enrollment->institute) }}" class="text-indigo-600 hover:underline">{{ $enrollment->institute->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :type="$badge">{{ $enrollment->status }}</x-badge></td>
                            <td class="px-6 py-4">{{ number_format((float) $enrollment->progress, 0) }}%</td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $enrollment->enrolled_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.academic.enrollments.show', $enrollment) }}" class="text-xs font-semibold text-indigo-600">Detail</a>
                                    @if($enrollment->institute?->is_active)
                                        <form method="POST" action="{{ route('platform.companies.enter-panel', $enrollment->institute) }}">@csrf
                                            <button class="text-xs font-semibold text-teal-700">Open panel</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $enrollments->links() }}</div>
        @else
            <x-empty-state title="No enrollments found" />
        @endif
    </div>
</div>
@endsection
