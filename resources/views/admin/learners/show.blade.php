@extends('layouts.app')

@section('title', $learner->name)
@section('page-title', $learner->name)
@section('breadcrumb', 'Learners / Profile')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 flex items-center justify-center text-xl font-bold text-indigo-600">{{ strtoupper(substr($learner->name, 0, 1)) }}</div>
            <div>
                <p class="text-slate-500 text-sm">{{ $learner->email }}</p>
                <x-badge :type="$learner->is_active ? 'success' : 'danger'" class="mt-1">{{ $learner->is_active ? 'Active' : 'Inactive' }}</x-badge>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.learners.edit', $learner) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Edit</a>
            <a href="{{ route('admin.learners.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-300 text-sm">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Total Spent" :value="'₹'.number_format($learner->total_spent ?? 0, 0)" />
        <x-stat-card title="Enrollments" :value="number_format($learner->enrollments->count())" />
        <x-stat-card title="Certificates" :value="number_format($learner->certificates->count())" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Add Learner To Product</h3>
        <form method="POST" action="{{ route('admin.learners.enroll', $learner) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <x-form-input label="Select Product / Course" name="course_id" type="select" required>
                <option value="">Select course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->title }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Access Type" name="access_type" type="select" required>
                @foreach(['trial' => 'Trial', 'paid' => 'Paid', 'free' => 'Free'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('access_type', 'paid') === $val)>{{ $label }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Amount (₹)" name="amount" type="number" step="0.01" :value="old('amount')" />
            <x-form-input label="Expiry Date" name="expires_at" type="date" :value="old('expires_at')" />
            <label class="flex items-center gap-3 md:col-span-2">
                <input type="hidden" name="show_custom_fields" value="0">
                <input type="checkbox" name="show_custom_fields" value="1" @checked(old('show_custom_fields')) class="rounded border-slate-300 text-indigo-600">
                <span class="text-sm text-slate-700">Show Custom Fields</span>
            </label>
            <div class="md:col-span-2 flex flex-wrap gap-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Assign Product</button>
                <a href="{{ route('admin.learners.create') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50">Add New Learner</a>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Enrollments</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-slate-500 text-left"><th class="pb-2">Course</th><th class="pb-2">Status</th><th class="pb-2">Enrolled</th><th class="pb-2">Expires</th><th class="pb-2"></th></tr></thead>
                <tbody>
                    @forelse($learner->enrollments as $enrollment)
                    <tr>
                        <td class="py-2.5 text-slate-800">{{ $enrollment->course?->title }}</td>
                        <td class="py-2.5"><x-badge :type="$enrollment->status === 'active' ? 'success' : 'danger'">{{ ucfirst($enrollment->status) }}</x-badge></td>
                        <td class="py-2.5 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') }}</td>
                        <td class="py-2.5 text-slate-500">{{ $enrollment->expires_at?->format('M d, Y') ?? '—' }}</td>
                        <td class="py-2.5 text-right">
                            @if($enrollment->status === 'active')
                            <form method="POST" action="{{ route('admin.enrollments.revoke', $enrollment) }}">@csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700">Revoke</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-4 text-slate-500 text-center">No enrollments</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($learner->orders->count())
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Orders</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-slate-500 text-left"><th class="pb-2">Order</th><th class="pb-2">Total</th><th class="pb-2">Status</th><th class="pb-2">Date</th></tr></thead>
                <tbody>
                    @foreach($learner->orders as $order)
                    <tr>
                        <td class="py-2.5"><a href="{{ route('admin.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">{{ $order->order_number }}</a></td>
                        <td class="py-2.5 text-slate-800">₹{{ number_format($order->total, 0) }}</td>
                        <td class="py-2.5"><x-badge :type="$order->payment_status === 'paid' ? 'success' : 'warning'">{{ ucfirst($order->payment_status) }}</x-badge></td>
                        <td class="py-2.5 text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
