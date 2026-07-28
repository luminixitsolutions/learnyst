@extends('layouts.app')

@section('title', 'Affiliate — ' . $affiliate->name)
@section('page-title', 'Affiliate Details')
@section('breadcrumb', 'Sales / Affiliates / Details')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">{{ $affiliate->name }}</h2>
            <p class="text-sm text-slate-500">{{ $affiliate->email }} @if($affiliate->phone)· {{ $affiliate->phone }}@endif</p>
            <p class="mt-1 font-mono text-emerald-700 text-sm">Code: {{ $affiliate->code }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($affiliate->status !== 'approved')
                <form method="POST" action="{{ route('admin.affiliates.approve', $affiliate) }}">@csrf
                    <button type="submit" class="panel-btn-primary">Approve</button>
                </form>
            @endif
            @if($affiliate->status !== 'rejected')
                <form method="POST" action="{{ route('admin.affiliates.reject', $affiliate) }}">@csrf
                    <button type="submit" class="panel-btn-secondary">Reject</button>
                </form>
            @endif
            @if($affiliate->status === 'approved')
                <form method="POST" action="{{ route('admin.affiliates.suspend', $affiliate) }}">@csrf
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold bg-amber-500/15 text-amber-700 hover:bg-amber-500/25">Suspend</button>
                </form>
            @endif
            <a href="{{ route('admin.affiliates.index') }}" class="panel-btn-secondary">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <x-stat-card title="Status" :value="ucfirst($affiliate->status)" />
        <x-stat-card title="Clicks" :value="number_format($affiliate->total_clicks)" />
        <x-stat-card title="Sales" :value="'₹' . number_format($affiliate->total_sales, 2)" />
        <x-stat-card title="Commission" :value="'₹' . number_format($affiliate->total_commission, 2)" />
        <x-stat-card title="Available" :value="'₹' . number_format($affiliate->pendingCommissionBalance(), 2)" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6 space-y-4">
            <div>
                <p class="text-sm text-slate-500">Commission</p>
                <p class="text-lg font-bold text-slate-800 mt-1">
                    {{ $affiliate->commission_type === 'percent' ? number_format($affiliate->commission_value, 1).'%' : '₹'.number_format($affiliate->commission_value, 2).' fixed' }}
                </p>
                @if($affiliate->user)
                    <p class="text-xs text-slate-500 mt-2">Linked learner: {{ $affiliate->user->name }}</p>
                @endif
                @if($affiliate->payment_details)
                    <p class="text-xs text-slate-500 mt-2 whitespace-pre-line">{{ $affiliate->payment_details }}</p>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.affiliates.links.store', $affiliate) }}" class="space-y-3 border-t border-slate-200 pt-4">
                @csrf
                <p class="text-sm font-semibold text-slate-800">Add / Ensure Link</p>
                <select name="product_type" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" required>
                    <option value="course">Course</option>
                    <option value="bundle">Bundle</option>
                    <option value="custom">Custom</option>
                </select>
                <input type="number" name="product_id" placeholder="Product ID (optional)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input type="text" name="url_path" placeholder="URL path (optional)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="panel-btn-primary w-full">Save Link</button>
            </form>

            <form method="POST" action="{{ route('admin.affiliates.payouts.store', $affiliate) }}" class="space-y-3 border-t border-slate-200 pt-4">
                @csrf
                <p class="text-sm font-semibold text-slate-800">Request Payout</p>
                <p class="text-xs text-slate-500">Available: ₹{{ number_format($affiliate->pendingCommissionBalance(), 2) }}</p>
                <input type="number" step="0.01" min="0.01" name="amount" required placeholder="Amount" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <input type="text" name="notes" placeholder="Notes" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="panel-btn-secondary w-full" @disabled(!$affiliate->isApproved() || $affiliate->pendingCommissionBalance() <= 0)>Create Payout</button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800">Links</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm panel-table">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="px-6 py-3">Slug</th>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Clicks</th>
                                <th class="px-6 py-3">Conv.</th>
                                <th class="px-6 py-3">Path</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($links as $link)
                            <tr>
                                <td class="px-6 py-3 font-mono text-emerald-700">{{ $link->slug }}</td>
                                <td class="px-6 py-3">{{ ucfirst($link->product_type) }}{{ $link->product_id ? ' #'.$link->product_id : '' }}</td>
                                <td class="px-6 py-3">{{ $link->clicks }}</td>
                                <td class="px-6 py-3">{{ $link->conversions }} ({{ $link->conversionRate() }}%)</td>
                                <td class="px-6 py-3 text-slate-500 text-xs">{{ $link->url_path ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No links yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800">Commissions</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm panel-table">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Order</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Rate</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commissions as $commission)
                            <tr>
                                <td class="px-6 py-3 text-slate-500">{{ $commission->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-3">{{ $commission->order?->order_number ?? '—' }}</td>
                                <td class="px-6 py-3 font-medium text-emerald-700">₹{{ number_format($commission->amount, 2) }}</td>
                                <td class="px-6 py-3">{{ number_format($commission->rate, 2) }}</td>
                                <td class="px-6 py-3"><x-badge :type="$commission->statusBadgeType()">{{ ucfirst($commission->status) }}</x-badge></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No commissions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">{{ $commissions->links() }}</div>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-slate-800">Payouts</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm panel-table">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Reference</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                            <tr>
                                <td class="px-6 py-3 text-slate-500">{{ $payout->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-3 font-medium">₹{{ number_format($payout->amount, 2) }}</td>
                                <td class="px-6 py-3"><x-badge :type="$payout->statusBadgeType()">{{ ucfirst($payout->status) }}</x-badge></td>
                                <td class="px-6 py-3 text-slate-500">{{ $payout->payment_reference ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    @if($payout->status !== 'paid')
                                    <form method="POST" action="{{ route('admin.affiliates.payouts.paid', $payout) }}" class="flex gap-2 items-center">
                                        @csrf
                                        <input type="text" name="payment_reference" placeholder="Ref#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs w-24">
                                        <button type="submit" class="text-emerald-700 text-sm font-medium hover:text-emerald-900">Mark paid</button>
                                    </form>
                                    @else
                                    <span class="text-xs text-slate-400">{{ $payout->paid_at?->format('M d, Y') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No payouts yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4">{{ $payouts->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
