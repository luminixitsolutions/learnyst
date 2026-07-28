@extends('layouts.app')

@section('title', 'Subscription #' . $subscription->id)
@section('page-title', 'Subscription Details')
@section('breadcrumb', 'Sales / Subscriptions / Details')

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
            <x-badge :type="match($subscription->status) { 'active','trialing' => 'success', 'paused' => 'warning', 'cancelled','expired' => 'danger', default => 'info' }">{{ $subscription->statusLabel() }}</x-badge>
            <p class="text-sm text-slate-500 mt-2">Assigned {{ $subscription->created_at?->format('M d, Y h:i A') }}</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="panel-btn-secondary">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Overview</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-slate-500">Learner</dt><dd class="text-slate-800 font-medium">{{ $subscription->user?->name }}</dd><dd class="text-slate-500">{{ $subscription->user?->email }}</dd></div>
                <div><dt class="text-slate-500">Plan</dt><dd class="text-slate-800 font-medium">{{ $subscription->plan?->title }}</dd><dd class="text-slate-500">{{ $subscription->plan?->billingCycleLabel() }}</dd></div>
                <div><dt class="text-slate-500">Amount</dt><dd class="text-emerald-700 font-semibold">₹{{ number_format($subscription->amount, 2) }}</dd></div>
                <div><dt class="text-slate-500">Auto renew</dt><dd class="text-slate-800">{{ $subscription->auto_renew ? 'Yes' : 'No' }}</dd></div>
                <div><dt class="text-slate-500">Starts</dt><dd class="text-slate-800">{{ $subscription->starts_at?->format('M d, Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Ends</dt><dd class="text-slate-800">{{ $subscription->ends_at?->format('M d, Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Trial ends</dt><dd class="text-slate-800">{{ $subscription->trial_ends_at?->format('M d, Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Next billing</dt><dd class="text-slate-800">{{ $subscription->next_billing_at?->format('M d, Y') ?? '—' }}</dd></div>
                @if($subscription->coursePricingPlan)
                <div><dt class="text-slate-500">Course pricing plan</dt><dd class="text-slate-800">{{ $subscription->coursePricingPlan->title }}</dd></div>
                @endif
                @if($subscription->order)
                <div><dt class="text-slate-500">Order</dt><dd><a href="{{ route('admin.orders.show', $subscription->order) }}" class="text-emerald-700">{{ $subscription->order->order_number }}</a></dd></div>
                @endif
            </dl>
            @if($subscription->notes)
                <p class="text-sm text-slate-500 border-t border-slate-200 pt-4">{{ $subscription->notes }}</p>
            @endif
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800">Actions</h3>

            @if($subscription->isPausable())
            <form method="POST" action="{{ route('admin.subscriptions.pause', $subscription) }}" class="space-y-2">
                @csrf
                <input type="text" name="notes" placeholder="Pause note" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-amber-500/15 text-amber-700 text-sm font-semibold hover:bg-amber-500/25">Pause</button>
            </form>
            @endif

            @if($subscription->isResumable())
            <form method="POST" action="{{ route('admin.subscriptions.resume', $subscription) }}">
                @csrf
                <button type="submit" class="panel-btn-primary w-full">Resume</button>
            </form>
            @endif

            @if($subscription->isCancellable())
            <form method="POST" action="{{ route('admin.subscriptions.cancel', $subscription) }}" class="space-y-2" onsubmit="return confirm('Cancel this subscription?')">
                @csrf
                <input type="text" name="notes" placeholder="Cancel reason" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-red-600/15 text-red-600 text-sm font-semibold hover:bg-red-600/25">Cancel</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
