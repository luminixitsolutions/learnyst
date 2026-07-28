@extends('layouts.app')

@section('title', 'Subscription Plans')
@section('page-title', 'Subscription Plans')
@section('breadcrumb', 'Sales / Subscriptions / Plans')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Institute-level reusable plans for courses, bundles, and test series.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.subscriptions.index') }}" class="panel-btn-secondary">Learner Subscriptions</a>
            <a href="{{ route('admin.subscriptions.plans.create') }}" class="panel-btn-primary">Create Plan</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Plans" :value="number_format($stats['plans'])" />
        <x-stat-card title="Active Plans" :value="number_format($stats['active_plans'])" />
        <x-stat-card title="Subscriptions" :value="number_format($stats['subscriptions'])" />
        <x-stat-card title="Active Subs" :value="number_format($stats['active_subs'])" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search plans..." class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <select name="plan_type" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All types</option>
            @foreach(['course','bundle','test_series','platform'] as $type)
                <option value="{{ $type }}" @selected(request('plan_type') === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="panel-btn-primary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($plans->count())
        <div class="overflow-x-auto">
            <table id="plansTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Plan</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Billing</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Subs</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr class="hover:bg-emerald-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $plan->title }}</p>
                            <p class="text-xs text-slate-500">{{ $plan->slug }}</p>
                        </td>
                        <td class="px-6 py-4 capitalize">{{ str_replace('_', ' ', $plan->plan_type) }}</td>
                        <td class="px-6 py-4">{{ $plan->billingCycleLabel() }}</td>
                        <td class="px-6 py-4 font-semibold text-emerald-700" data-order="{{ $plan->price }}">₹{{ number_format($plan->price, 2) }}</td>
                        <td class="px-6 py-4">{{ $plan->subscriptions_count }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$plan->is_active ? 'success' : 'danger'">{{ $plan->is_active ? 'Active' : 'Inactive' }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.subscriptions.plans.edit', $plan) }}" class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $plans->links() }}</div>
        @else
        <x-empty-state title="No subscription plans" description="Create a reusable plan, then assign it to learners." :action="route('admin.subscriptions.plans.create')" actionLabel="Create Plan" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($plans->count())
    <x-admin.datatable-scripts table-id="plansTable" entity="plans" :order-column="3" order-direction="desc" :action-column="6" export-file-name="subscription-plans" />
@endif
@endpush
