@extends('layouts.app')

@section('title', 'All Courses')
@section('page-title', 'All Courses')
@section('breadcrumb', 'Platform Admin / Academic / Courses')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Courses across all institutes (read-only oversight).</p>
        <a href="{{ route('platform.academic.courses.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Courses" :value="number_format($stats['total'])" />
        <x-stat-card title="Published" :value="number_format($stats['published'])" />
        <x-stat-card title="Draft" :value="number_format($stats['draft'])" />
        <x-stat-card title="Enrollments" :value="number_format($stats['enrollments'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Title or slug…" class="panel-input w-full">
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
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['published','draft','unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.academic.courses') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($courses->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Enrollments</th>
                        <th class="px-6 py-4">Revenue</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                        @php
                            $badge = match ($course->status) {
                                'published' => 'success',
                                'unpublished' => 'warning',
                                default => 'info',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $course->title }}</div>
                                <div class="text-xs text-slate-400">{{ $course->category?->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($course->institute)
                                    <a href="{{ route('platform.companies.show', $course->institute) }}" class="text-indigo-600 hover:underline">{{ $course->institute->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :type="$badge">{{ $course->status }}</x-badge></td>
                            <td class="px-6 py-4">
                                <span class="font-semibold">{{ number_format($course->enrollments_count) }}</span>
                                <span class="text-xs text-slate-400">({{ number_format($course->active_enrollments_count) }} active)</span>
                            </td>
                            <td class="px-6 py-4 font-semibold">₹{{ number_format((float) $course->revenue, 0) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.academic.courses.show', $course) }}" class="text-xs font-semibold text-indigo-600">Detail</a>
                                    @if($course->institute?->is_active && $course->institute->owner_user_id)
                                        <form method="POST" action="{{ route('platform.companies.enter-panel', $course->institute) }}">@csrf
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
        <div class="px-6 py-4 border-t">{{ $courses->links() }}</div>
        @else
            <x-empty-state title="No courses found" />
        @endif
    </div>
</div>
@endsection
