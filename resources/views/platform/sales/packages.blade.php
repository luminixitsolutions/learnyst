@extends('layouts.app')

@section('title', 'Packages Overview')
@section('page-title', 'Packages Overview')
@section('breadcrumb', 'Platform Admin / Sales / Packages')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-500">Which institute has which platform subscription package.</p>
        <div class="flex gap-2">
            <a href="{{ route('platform.subscription-packages.index') }}" class="panel-btn-secondary text-sm">Manage packages</a>
            <a href="{{ route('platform.sales.packages.export', request()->query()) }}" class="panel-btn-primary text-sm">Export CSV</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($packageStats as $stat)
            <x-stat-card
                :title="$stat['package']->name"
                :value="number_format($stat['institutes'])"
                :trend="number_format($stat['active']).' active'"
            />
        @endforeach
        <x-stat-card title="Unassigned" :value="number_format($unassigned)" trend="No package" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Package</label>
            <select name="package_id" class="panel-input">
                <option value="">All</option>
                <option value="none" @selected(request('package_id') === 'none')>None</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}" @selected((string) request('package_id') === (string) $package->id)>{{ $package->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institute status</label>
            <select name="status" class="panel-input">
                <option value="">All</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
            </select>
        </div>
        <button class="panel-btn-primary text-sm">Filter</button>
        <a href="{{ route('platform.sales.packages') }}" class="panel-btn-secondary text-sm">Reset</a>
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
                        <th class="px-6 py-4">Assigned</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        <tr>
                            <td class="px-6 py-4 font-medium">
                                <a href="{{ route('platform.companies.show', $company) }}" class="text-indigo-600 hover:underline">{{ $company->name }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $company->owner?->email ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $company->subscriptionPackage?->name ?? 'None' }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $company->package_assigned_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <x-badge :type="$company->is_active ? 'success' : 'danger'">{{ $company->is_active ? 'Active' : 'Suspended' }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('platform.companies.show', $company) }}" class="text-xs font-semibold text-indigo-600">Manage</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $companies->links() }}</div>
        @else
            <x-empty-state title="No institutes match" />
        @endif
    </div>
</div>
@endsection
