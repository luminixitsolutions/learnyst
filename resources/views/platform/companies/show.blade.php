@extends('layouts.app')

@section('title', $company->name)
@section('page-title', $company->name)
@section('breadcrumb', 'Platform Admin / Institutes / Detail')

@section('content')
@php
    $owner = $company->owner;
    $ownerCanEnterPanel = $company->is_active && $owner && $owner->is_active && in_array($owner->role?->slug, ['admin', 'sub-admin', 'counselor'], true);
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            @if($company->logoUrl())
                <img src="{{ $company->logoUrl() }}" alt="" class="h-14 w-14 rounded-2xl object-cover border border-slate-200">
            @else
                <span class="h-14 w-14 rounded-2xl bg-slate-900 text-white text-lg font-semibold inline-flex items-center justify-center">{{ $company->initials() }}</span>
            @endif
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-xl font-bold text-slate-800 truncate">{{ $company->name }}</h2>
                    <x-badge :type="$company->is_active ? 'success' : 'danger'">{{ $company->is_active ? 'Active' : 'Suspended' }}</x-badge>
                    <x-badge :type="$company->is_public ? 'success' : 'warning'">{{ $company->is_public ? 'Public' : 'Hidden' }}</x-badge>
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $company->tagline ?: ($company->slug ?? '') }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('platform.companies.index') }}" class="panel-btn-secondary text-sm">← All institutes</a>
            <a href="{{ route('platform.companies.edit', $company) }}" class="panel-btn-primary text-sm">Edit profile</a>
            @if($ownerCanEnterPanel)
                <form method="POST" action="{{ route('platform.companies.enter-panel', $company) }}">
                    @csrf
                    <button class="inline-flex items-center rounded-xl bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Open panel</button>
                </form>
            @endif
            <a href="{{ route('website.companies.show', ['slug' => $company->slug, 'preview' => 1]) }}" target="_blank" class="panel-btn-secondary text-sm">View profile</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Users" :value="number_format($stats['users'])" />
        <x-stat-card title="Courses" :value="number_format($stats['courses'])" :trend="number_format($stats['published_courses']).' published'" />
        <x-stat-card title="Revenue" :value="'₹'.number_format($stats['revenue'], 0)" :trend="number_format($stats['orders']).' orders'" />
        <x-stat-card title="Package" :value="$company->subscriptionPackage?->name ?? 'None'" :trend="$company->package_assigned_at ? 'Assigned '.$company->package_assigned_at->format('M d, Y') : 'Not assigned'" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Profile</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-slate-500">Email</dt><dd class="font-medium text-slate-800">{{ $company->email ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Phone</dt><dd class="font-medium text-slate-800">{{ $company->phone ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">City</dt><dd class="font-medium text-slate-800">{{ $company->city ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Website</dt><dd class="font-medium text-slate-800 break-all">{{ $company->website_url ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Address</dt><dd class="font-medium text-slate-800">{{ $company->address ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">About</dt><dd class="text-slate-700 whitespace-pre-line">{{ $company->about ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Registered</dt><dd class="font-medium text-slate-800">{{ $company->created_at?->format('M d, Y H:i') }}</dd></div>
                    <div><dt class="text-slate-500">Slug</dt><dd class="font-mono text-xs text-slate-600">{{ $company->slug }}</dd></div>
                </dl>
            </div>

            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Recent activity</h3>
                    <a href="{{ route('platform.activity.index') }}" class="text-sm text-indigo-600 font-medium">Activity logs →</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentActivity as $log)
                        <div class="flex items-start justify-between gap-3 py-2 border-b border-slate-100 last:border-0">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800">{{ $log->description ?? ucfirst($log->action) }}</p>
                                <p class="text-xs text-slate-500">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                            </div>
                            <x-badge type="info">{{ ucfirst(str_replace('_', ' ', (string) $log->action)) }}</x-badge>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No related activity yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 space-y-3">
                <h3 class="text-lg font-bold text-slate-800">Owner</h3>
                <p class="text-sm font-semibold text-slate-800">{{ $owner?->name ?? '—' }}</p>
                <p class="text-sm text-slate-500">{{ $owner?->email ?? '—' }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ str_replace('-', ' ', $owner?->role?->slug ?? 'no role') }}</p>
                <x-badge :type="$owner?->is_active ? 'success' : 'danger'">{{ $owner?->is_active ? 'Owner active' : 'Owner inactive' }}</x-badge>
                <p class="text-xs text-slate-400">Last login: {{ $owner?->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</p>
            </div>

            <div class="glass-card rounded-2xl p-6 space-y-4">
                <h3 class="text-lg font-bold text-slate-800">Actions</h3>

                <form method="POST" action="{{ route('platform.companies.toggle-active', $company) }}">
                    @csrf
                    <button class="w-full panel-btn-secondary text-sm {{ $company->is_active ? 'text-red-600' : 'text-emerald-700' }}">
                        {{ $company->is_active ? 'Suspend institute' : 'Activate institute' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('platform.companies.toggle-public', $company) }}">
                    @csrf
                    <button class="w-full panel-btn-secondary text-sm">
                        {{ $company->is_public ? 'Hide from public directory' : 'Make public' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('platform.companies.assign-package', $company) }}" class="space-y-3 pt-2 border-t border-slate-100">
                    @csrf
                    <label class="block text-xs font-medium text-slate-500">Subscription package</label>
                    <select name="subscription_package_id" class="panel-input w-full">
                        <option value="">No package</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" @selected((int) $company->subscription_package_id === (int) $package->id)>{{ $package->name }}</option>
                        @endforeach
                    </select>
                    <button class="w-full panel-btn-primary text-sm">Assign package</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
