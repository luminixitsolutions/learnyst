@extends('layouts.app')

@section('title', 'Affiliates')
@section('page-title', 'Affiliate Partners')
@section('breadcrumb', 'Sales / Affiliates')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Register affiliates, track links, commissions, and payouts.</p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.affiliates.settings') }}" class="panel-btn-secondary">Settings</a>
            <a href="{{ route('admin.affiliates.create') }}" class="panel-btn-primary">Add Affiliate</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
        <x-stat-card title="Affiliates" :value="number_format($stats['total'])" />
        <x-stat-card title="Approved" :value="number_format($stats['approved'])" />
        <x-stat-card title="Pending" :value="number_format($stats['pending'])" />
        <x-stat-card title="Total Sales" :value="'₹' . number_format($stats['total_sales'], 2)" />
        <x-stat-card title="Commission" :value="'₹' . number_format($stats['total_commission'], 2)" />
        <x-stat-card title="Paid Out" :value="'₹' . number_format($stats['paid_commission'], 2)" />
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, or code..." class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach(['pending', 'approved', 'rejected', 'suspended'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="panel-btn-primary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($affiliates->count())
        <div class="overflow-x-auto">
            <table id="affiliatesTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Affiliate</th>
                        <th class="px-6 py-4">Code</th>
                        <th class="px-6 py-4">Commission</th>
                        <th class="px-6 py-4">Sales</th>
                        <th class="px-6 py-4">Earned</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affiliates as $affiliate)
                    <tr class="hover:bg-emerald-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $affiliate->name }}</p>
                            <p class="text-xs text-slate-500">{{ $affiliate->email }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-emerald-700">{{ $affiliate->code }}</td>
                        <td class="px-6 py-4">
                            {{ $affiliate->commission_type === 'percent' ? number_format($affiliate->commission_value, 1).'%' : '₹'.number_format($affiliate->commission_value, 2) }}
                        </td>
                        <td class="px-6 py-4" data-order="{{ $affiliate->total_sales }}">₹{{ number_format($affiliate->total_sales, 2) }}</td>
                        <td class="px-6 py-4 font-semibold text-emerald-700" data-order="{{ $affiliate->total_commission }}">₹{{ number_format($affiliate->total_commission, 2) }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="$affiliate->statusBadgeType()">{{ ucfirst($affiliate->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.affiliates.show', $affiliate) }}" class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $affiliates->links() }}</div>
        @else
        <x-empty-state title="No affiliates yet" description="Register an affiliate partner to start tracking commissions." :action="route('admin.affiliates.create')" actionLabel="Add Affiliate" />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($affiliates->count())
    <x-admin.datatable-scripts table-id="affiliatesTable" entity="affiliates" :order-column="4" order-direction="desc" :action-column="6" export-file-name="affiliates" />
@endif
@endpush
