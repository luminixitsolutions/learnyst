@extends('layouts.app')

@section('title', $bundle->title)
@section('page-title', $bundle->title)
@section('breadcrumb', 'Bundles / Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <x-badge :type="match($bundle->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($bundle->status) }}</x-badge>
            <p class="text-sm text-slate-500 mt-2">₹{{ number_format($bundle->price ?? 0, 0) }} · {{ $bundle->courses->count() }} courses</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.bundles.edit', $bundle) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Edit</a>
            <a href="{{ route('admin.bundles.index') }}" class="panel-btn-secondary">Back</a>
        </div>
    </div>

    @if($bundle->description)
    <div class="glass-card rounded-2xl p-6"><p class="text-sm text-slate-500">{{ $bundle->description }}</p></div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Courses" :value="number_format($bundle->courses->count())" />
        <x-stat-card title="Enrollments" :value="number_format($bundle->enrollments->count())" />
        <x-stat-card title="Sales Total" :value="'₹'.number_format($salesTotal, 0)" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Included Courses</h3>
        <div class="space-y-2">
            @forelse($bundle->courses as $course)
            <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                <a href="{{ route('admin.courses.show', $course) }}" class="text-sm text-white hover:text-indigo-600">{{ $course->title }}</a>
                <span class="text-xs text-slate-500">₹{{ number_format($course->price, 0) }}</span>
            </div>
            @empty
            <p class="text-sm text-slate-500">No courses in this bundle</p>
            @endforelse
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-800">Bundle Enrollments</h3>
        </div>
        @if($bundle->enrollments->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Enrolled</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bundle->enrollments as $enrollment)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.enrollments.history', $enrollment->user) }}" class="text-white hover:text-indigo-600">{{ $enrollment->user?->name }}</a>
                            <p class="text-xs text-slate-500">{{ $enrollment->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4"><x-badge :type="$enrollment->status === 'active' ? 'success' : 'default'">{{ ucfirst($enrollment->status) }}</x-badge></td>
                        <td class="px-6 py-4 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="No enrollments yet" />
        @endif
    </div>
</div>
@endsection
