@extends('layouts.app')

@section('title', $user->name . ' — Report')
@section('page-title', 'Learner Profile Report')
@section('breadcrumb', 'Reports / Learners / Profile')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center text-xl font-bold text-indigo-600">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }} · {{ $user->phone ?? 'No phone' }}</p>
                <p class="text-xs text-slate-400 mt-1">Segment: {{ $user->segments->pluck('name')->join(', ') ?: '—' }}</p>
            </div>
        </div>
        <a href="{{ route('admin.reports.learners') }}" class="text-sm text-slate-500 hover:text-indigo-600">← Learners Report</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <x-stat-card title="Total Purchases" :value="number_format($user->orders->where('payment_status', 'paid')->count())" />
        <x-stat-card title="Total Amount Spent" :value="'₹'.number_format($user->total_spent ?? 0, 0)" />
        <x-stat-card title="Enrollments" :value="number_format($user->enrollments->count())" />
        <x-stat-card title="Last Active" :value="$user->last_login_at?->format('M d, Y') ?? '—'" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Add Product</h3>
        </div>
        <form method="POST" action="{{ route('admin.learners.enroll', $user) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <select name="course_id" required class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <option value="">Select product / course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
            <select name="access_type" required class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                @foreach(['paid' => 'Paid', 'trial' => 'Trial', 'free' => 'Free'] as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-primary text-sm">Assign Product</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Learner Activity — Enrollments</h3>
            @forelse($user->enrollments as $enrollment)
            <div class="py-3 border-b border-slate-100 last:border-0 flex justify-between items-center">
                <div>
                    <p class="font-medium text-slate-800">{{ $enrollment->course?->title ?? '—' }}</p>
                    <p class="text-xs text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') }}</p>
                </div>
                <x-badge :type="$enrollment->status === 'active' ? 'success' : 'warning'">{{ ucfirst($enrollment->status) }}</x-badge>
            </div>
            @empty
            <x-empty-state title="No product enrolled" description="Assign a product using the form above." />
            @endforelse
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Learner Activity — Certificates</h3>
            @forelse($user->certificates as $cert)
            <div class="py-3 border-b border-slate-100 last:border-0">
                <p class="font-medium text-slate-800">{{ $cert->course?->title }}</p>
                <p class="text-xs text-slate-500">{{ $cert->certificate_number }} · {{ $cert->issued_at?->format('M d, Y') }}</p>
            </div>
            @empty
            <x-empty-state title="No certificates issued" />
            @endforelse
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Learner Account — Payment Requests</h3>
        @forelse($pendingPayments as $payment)
        <div class="py-3 border-b border-slate-100 last:border-0 flex justify-between items-center">
            <div>
                <p class="font-medium text-slate-800">{{ $payment->order?->order_number ?? 'Payment #'.$payment->id }}</p>
                <p class="text-xs text-slate-500">₹{{ number_format($payment->amount, 2) }} · {{ $payment->created_at->format('M d, Y') }}</p>
            </div>
            <x-badge :type="$payment->status === 'pending' ? 'warning' : 'danger'">{{ ucfirst($payment->status) }}</x-badge>
        </div>
        @empty
        <x-empty-state title="No payment requests" />
        @endforelse
    </div>
</div>
@endsection
