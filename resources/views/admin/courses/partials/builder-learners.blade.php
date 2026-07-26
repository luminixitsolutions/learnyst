@php
    $enrollments = $enrollments ?? collect();
    $learnerStats = $learnerStats ?? ['total' => 0, 'active' => 0, 'paid' => 0, 'completed' => 0];
@endphp

<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Course learners</h3>
            <p class="text-sm text-slate-500 mt-0.5">Learners who purchased or were enrolled in this course.</p>
        </div>
        <form method="GET" action="{{ route('admin.courses.builder', $course) }}" class="flex items-center gap-2">
            <input type="hidden" name="tab" value="learners">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/50 focus:outline-none min-w-[220px]">
            <button type="submit" class="panel-btn-secondary">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Total Learners" :value="number_format($learnerStats['total'])" />
        <x-stat-card title="Active" :value="number_format($learnerStats['active'])" />
        <x-stat-card title="Purchased / Paid" :value="number_format($learnerStats['paid'])" />
        <x-stat-card title="Completed" :value="number_format($learnerStats['completed'])" />
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($enrollments->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm panel-table">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-4">Learner</th>
                            <th class="px-6 py-4">Access</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Progress</th>
                            <th class="px-6 py-4">Amount</th>
                            <th class="px-6 py-4">Order</th>
                            <th class="px-6 py-4">Enrolled</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $enrollment)
                            @php $user = $enrollment->user; @endphp
                            <tr class="hover:bg-indigo-50/40">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800 truncate">{{ $user?->name ?? '—' }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $user?->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge type="info">{{ ucfirst($enrollment->access_type ?? $enrollment->enrollment_type ?? 'course') }}</x-badge>
                                    @if($enrollment->bundle)
                                        <div class="text-xs text-slate-400 mt-1">via {{ $enrollment->bundle->title }}</div>
                                    @elseif($enrollment->batch)
                                        <div class="text-xs text-slate-400 mt-1">batch {{ $enrollment->batch->title }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :type="match($enrollment->status) { 'active' => 'success', 'expired' => 'warning', 'revoked' => 'danger', default => 'default' }">
                                        {{ ucfirst($enrollment->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    {{ $enrollment->progress !== null ? number_format((float) $enrollment->progress, 0).'%' : '—' }}
                                    @if($enrollment->completed_at)
                                        <div class="text-xs text-emerald-600 mt-0.5">Completed</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-700 font-medium">
                                    @if($enrollment->amount !== null && (float) $enrollment->amount > 0)
                                        ₹{{ number_format((float) $enrollment->amount, 0) }}
                                    @elseif($enrollment->order)
                                        ₹{{ number_format((float) ($enrollment->order->total ?? 0), 0) }}
                                    @elseif(($enrollment->access_type ?? '') === 'free' || ($enrollment->amount !== null && (float) $enrollment->amount == 0))
                                        Free
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($enrollment->order)
                                        <a href="{{ route('admin.orders.show', $enrollment->order) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                            {{ $enrollment->order->order_number ?? '#'.$enrollment->order->id }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                    {{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}
                                    @if($enrollment->expires_at)
                                        <div class="text-xs text-slate-400">Expires {{ $enrollment->expires_at->format('M d, Y') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($user)
                                        <a href="{{ route('admin.enrollments.history', $user) }}" class="text-sm text-indigo-600 hover:underline">History</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($enrollments, 'links'))
                <div class="px-6 py-4 border-t border-slate-200">{{ $enrollments->links() }}</div>
            @endif
        @else
            <div class="p-12 text-center">
                <p class="font-semibold text-slate-800">No learners yet</p>
                <p class="text-sm text-slate-500 mt-1">Learners who purchase or get enrolled in this course will appear here.</p>
                <a href="{{ route('admin.enrollments.index') }}" class="panel-btn-secondary mt-4 inline-flex">Manage enrollments</a>
            </div>
        @endif
    </div>
</div>
