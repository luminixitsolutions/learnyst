@extends('layouts.app')

@section('title', 'All Institutes')
@section('page-title', 'All Institutes')
@section('breadcrumb', 'Platform Admin / Institutes')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Manage institute tenants, visibility, status, and packages.</p>
        <a href="{{ route('platform.companies.create') }}" class="panel-btn-primary text-sm">Create institute</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Total" :value="number_format($stats['total'])" />
        <x-stat-card title="Active" :value="number_format($stats['active'])" />
        <x-stat-card title="Suspended" :value="number_format($stats['suspended'])" />
        <x-stat-card title="Public" :value="number_format($stats['public'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3 items-end">
        <div class="xl:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, email, city…" class="panel-input w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
            <select name="status" class="panel-input w-full">
                <option value="">All</option>
                @foreach(['active' => 'Active', 'suspended' => 'Suspended', 'public' => 'Public', 'hidden' => 'Hidden'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Package</label>
            <select name="package" class="panel-input w-full">
                <option value="">All packages</option>
                <option value="none" @selected(request('package') === 'none')>No package</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" @selected((string) request('package') === (string) $package->id)>{{ $package->name }}</option>
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
            <button type="submit" class="panel-btn-primary text-sm">Filter</button>
            <a href="{{ route('platform.companies.index') }}" class="panel-btn-secondary text-sm">Reset</a>
        </div>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($companies->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Institute</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Package</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Visibility</th>
                        <th class="px-6 py-4">Registered</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        @php
                            $owner = $company->owner;
                            $ownerCanEnterPanel = $company->is_active && $owner && $owner->is_active && in_array($owner->role?->slug, ['admin', 'sub-admin', 'counselor'], true);
                            $location = collect([$company->city, data_get($company->profile, 'state')])->filter()->implode(', ');
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('platform.companies.show', $company) }}" class="flex items-center gap-3 min-w-[220px] group">
                                    @if($company->logoUrl())
                                        <img src="{{ $company->logoUrl() }}" alt="" class="h-10 w-10 rounded-xl object-cover border border-slate-200 shrink-0">
                                    @else
                                        <span class="h-10 w-10 rounded-xl bg-slate-900 text-white text-xs font-semibold inline-flex items-center justify-center shrink-0">{{ $company->initials() }}</span>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-medium text-slate-800 group-hover:text-teal-700 truncate">{{ $company->name }}</div>
                                        <div class="text-xs text-slate-400 truncate">{{ $location ?: ($company->slug ?? '') }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $owner?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $owner?->email ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $company->subscriptionPackage?->name ?? '—' }}</td>
                            <td class="px-6 py-4">{{ number_format($company->courses_count) }}</td>
                            <td class="px-6 py-4">
                                <x-badge :type="$company->is_active ? 'success' : 'danger'">
                                    {{ $company->is_active ? 'Active' : 'Suspended' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge :type="$company->is_public ? 'success' : 'warning'">
                                    {{ $company->is_public ? 'Public' : 'Hidden' }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $company->created_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('platform.companies.show', $company) }}" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                                    @if($ownerCanEnterPanel)
                                        <form method="POST" action="{{ route('platform.companies.enter-panel', $company) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-teal-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-teal-700">Open panel</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('website.companies.show', ['slug' => $company->slug, 'preview' => 1]) }}" target="_blank" class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50">View profile</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $companies->links() }}</div>
        @else
            <x-empty-state title="No institutes match your filters" />
        @endif
    </div>
</div>
@endsection
