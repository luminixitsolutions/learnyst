@extends('layouts.app')

@section('title', 'Certificates')
@section('page-title', 'Certificates Issued')
@section('breadcrumb', 'Platform Admin / Academic / Certificates')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Certificates issued across institutes.</p>
        <a href="{{ route('platform.academic.certificates.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Issued" :value="number_format($stats['total'])" />
        <x-stat-card title="Valid" :value="number_format($stats['valid'])" />
        <x-stat-card title="Expiring soon" :value="number_format($stats['expiring'])" />
        <x-stat-card title="Expired / renewal" :value="number_format($stats['expired'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Certificate #, student, course…" class="panel-input w-full">
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
                @foreach(['valid','expiring_soon','expired','renewal_due'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ str_replace('_',' ', ucfirst($st)) }}</option>
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
            <a href="{{ route('platform.academic.certificates') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($certificates->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Certificate</th>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Course</th>
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Issued</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($certificates as $certificate)
                        @php
                            $badge = match ($certificate->status) {
                                'valid' => 'success',
                                'expiring_soon', 'renewal_due' => 'warning',
                                'expired' => 'danger',
                                default => 'info',
                            };
                        @endphp
                        <tr>
                            <td class="px-6 py-4 font-mono text-xs">{{ $certificate->certificate_number }}</td>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $certificate->user?->name }}</div>
                                <div class="text-xs text-slate-400">{{ $certificate->user?->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($certificate->course)
                                    <a href="{{ route('platform.academic.courses.show', $certificate->course) }}" class="text-indigo-600 hover:underline">{{ $certificate->course->title }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($certificate->institute)
                                    <a href="{{ route('platform.companies.show', $certificate->institute) }}" class="text-indigo-600 hover:underline">{{ $certificate->institute->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-6 py-4"><x-badge :type="$badge">{{ str_replace('_',' ', $certificate->status) }}</x-badge></td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $certificate->issued_at?->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('platform.academic.certificates.show', $certificate) }}" class="text-xs font-semibold text-indigo-600">Detail</a>
                                    @if($certificate->institute?->is_active)
                                        <form method="POST" action="{{ route('platform.companies.enter-panel', $certificate->institute) }}">@csrf
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
        <div class="px-6 py-4 border-t">{{ $certificates->links() }}</div>
        @else
            <x-empty-state title="No certificates found" />
        @endif
    </div>
</div>
@endsection
