@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Overview & Analytics')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Total Sales" :value="'₹' . number_format($stats['total_sales'], 0)" trend="All time revenue" :href="route('admin.reports.sales')" />
        <x-stat-card title="Total Learners" :value="number_format($stats['total_learners'])" :href="route('admin.learners.index')" />
        <x-stat-card title="Products / Courses" :value="number_format($stats['total_courses'])" :href="route('admin.courses.index')" />
        <x-stat-card title="Successful Payments" :value="number_format($stats['total_payments'])" :href="route('admin.payments.index')" />
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Monthly Sales ({{ now()->year }})</h3>
                @php $yearTotal = $monthlySales->sum(); @endphp
                @if($yearTotal > 0)
                    <span class="text-sm font-semibold text-indigo-600">₹{{ number_format($yearTotal, 0) }} total</span>
                @endif
            </div>
            @php
                $plotHeight = 168;
                $maxSale = max($monthlySales->max() ?: 0, 1);
            @endphp
            <div class="relative pt-2" style="height: 12.5rem">
                <div class="absolute inset-x-0 bottom-6 top-0 flex items-end gap-1.5 sm:gap-2">
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $amount = (float) ($monthlySales->get($m, 0));
                            $barPx = $amount > 0 ? max((int) round(($amount / $maxSale) * $plotHeight), 14) : 6;
                        @endphp
                        <div class="flex-1 h-full flex flex-col items-center justify-end group min-w-0">
                            @if($amount > 0)
                                <span class="text-[10px] font-semibold text-indigo-600 mb-1 truncate max-w-full opacity-0 group-hover:opacity-100 transition-opacity" title="₹{{ number_format($amount, 0) }}">₹{{ number_format($amount, 0) }}</span>
                            @endif
                            <div class="w-full max-w-[2.75rem] mx-auto rounded-t-lg transition-all shadow-sm"
                                 style="height: {{ $barPx }}px; {{ $amount > 0 ? 'background: var(--theme-gradient, linear-gradient(to top, #0b7970, #7ac4be));' : 'background:#f1f5f9;border:1px solid #e2e8f0;' }}"
                                 title="{{ date('F', mktime(0, 0, 0, $m, 1)) }}: ₹{{ number_format($amount, 0) }}"></div>
                        </div>
                    @endfor
                </div>
                <div class="absolute inset-x-0 bottom-0 flex gap-1.5 sm:gap-2 border-t border-slate-100 pt-2">
                    @for($m = 1; $m <= 12; $m++)
                        <div class="flex-1 text-center min-w-0">
                            <span class="text-[10px] text-slate-500 font-medium">{{ date('M', mktime(0, 0, 0, $m, 1)) }}</span>
                        </div>
                    @endfor
                </div>
            </div>
            @if($monthlySales->isEmpty())
                <p class="text-xs text-slate-400 text-center mt-2">No paid orders recorded for {{ now()->year }} yet.</p>
            @endif
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Pending Tasks</h3>
            @forelse($pendingTasks as $task)
                <div class="py-3 border-b border-slate-100 last:border-0">
                    <p class="text-sm font-semibold text-slate-800">{{ $task->title }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">No pending tasks</p>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Recent Learners</h3>
                <a href="{{ route('admin.learners.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentLearners as $learner)
                    <a href="{{ route('admin.learners.show', $learner) }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0" style="background: var(--theme-gradient, linear-gradient(135deg, #0b7970, #0d9488, #7ac4be));">{{ strtoupper(substr($learner->name,0,1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 truncate">{{ $learner->name }}</p>
                            <p class="text-xs text-slate-500">{{ $learner->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No learners yet</p>
                @endforelse
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-800">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead><tr class="text-left"><th class="pb-2 px-2">Order</th><th class="pb-2 px-2">Amount</th><th class="pb-2 px-2">Status</th></tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-indigo-50/40 cursor-pointer" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                                <td class="py-2.5 px-2 font-medium text-slate-800">{{ $order->order_number }}</td>
                                <td class="py-2.5 px-2">₹{{ number_format($order->total, 0) }}</td>
                                <td class="py-2.5 px-2"><x-badge :type="$order->payment_status === 'paid' ? 'success' : 'warning'">{{ ucfirst($order->payment_status) }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-slate-500 text-center">No orders yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($scheduledEvents->count())
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Scheduled Events</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($scheduledEvents as $event)
                <div class="p-4 rounded-xl bg-indigo-50/50 border border-indigo-100">
                    <p class="font-semibold text-slate-800">{{ $event->title }}</p>
                    <p class="text-xs text-indigo-600 mt-1 font-medium">{{ $event->starts_at->format('M d, Y h:i A') }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
