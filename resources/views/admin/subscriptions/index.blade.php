@extends('layouts.app')

@section('title', 'Learner Subscriptions')
@section('page-title', 'Learner Subscriptions')
@section('breadcrumb', 'Sales / Subscriptions')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-500">Assign plans to learners and manage renewals, pauses, and cancellations.</p>
        <a href="{{ route('admin.subscriptions.plans') }}" class="panel-btn-secondary">Manage Plans</a>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Assign Subscription</h3>
        <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Learner</label>
                <select name="user_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Plan</label>
                <select name="subscription_plan_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    <option value="">Select plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->title }} — ₹{{ number_format($plan->price, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <input type="text" name="notes" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Optional">
            </div>
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="auto_renew" value="1" class="rounded border-slate-300 text-emerald-600" checked>
                    Auto renew
                </label>
                <button type="submit" class="panel-btn-primary">Assign</button>
            </div>
        </form>
    </div>

    <form method="GET" class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search learner or plan..." class="flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach(['pending','trialing','active','paused','cancelled','expired'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button type="submit" class="panel-btn-primary">Filter</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($subscriptions->count())
        <div class="overflow-x-auto">
            <table id="subsTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Plan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Ends</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $sub)
                    <tr class="hover:bg-emerald-50/40">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $sub->user?->name }}</p>
                            <p class="text-xs text-slate-500">{{ $sub->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $sub->plan?->title }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($sub->status) { 'active','trialing' => 'success', 'paused' => 'warning', 'cancelled','expired' => 'danger', default => 'info' }">{{ $sub->statusLabel() }}</x-badge>
                        </td>
                        <td class="px-6 py-4 font-semibold text-emerald-700">₹{{ number_format($sub->amount, 2) }}</td>
                        <td class="px-6 py-4 text-slate-500" data-order="{{ $sub->ends_at?->timestamp }}">{{ $sub->ends_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.subscriptions.show', $sub) }}" class="text-emerald-700 hover:text-emerald-900 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $subscriptions->links() }}</div>
        @else
        <x-empty-state title="No subscriptions yet" description="Assign a plan to a learner to get started." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($subscriptions->count())
    <x-admin.datatable-scripts table-id="subsTable" entity="subscriptions" :order-column="4" order-direction="desc" :action-column="5" export-file-name="learner-subscriptions" />
@endif
@endpush
