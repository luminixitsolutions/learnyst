@extends('layouts.app')

@section('title', $course->title)
@section('page-title', $course->title)
@section('breadcrumb', 'Products / Course Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($course->thumbnail)
                <img src="{{ $course->thumbnailUrl() }}" alt="" class="w-16 h-16 rounded-xl object-cover">
            @endif
            <div>
                <x-badge :type="match($course->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($course->status) }}</x-badge>
                <p class="text-sm text-slate-500 mt-1">{{ ucfirst(str_replace('_',' ', $course->product_type)) }} · {{ $course->category?->name ?? 'Uncategorized' }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.courses.edit', $course) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Edit</a>
            <a href="{{ route('admin.courses.index') }}" class="panel-btn-secondary">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-stat-card title="Price" :value="$course->is_free ? 'Free' : '₹'.number_format($course->price, 0)" />
        <x-stat-card title="Enrollments" :value="number_format($course->enrollments->count())" />
        <x-stat-card title="Sections" :value="number_format($course->sections->count())" />
        <x-stat-card title="Lessons" :value="number_format($course->sections->sum(fn($s) => $s->lessons->count()))" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Description</h3>
            <p class="text-sm text-slate-500 whitespace-pre-line">{{ $course->description ?: 'No description provided.' }}</p>
            <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-slate-500">Access</dt><dd class="text-white capitalize">{{ $course->access_type }}</dd></div>
                <div><dt class="text-slate-500">Drip</dt><dd class="text-slate-800">{{ $course->drip_enabled ? 'Enabled' : 'Disabled' }}</dd></div>
                <div><dt class="text-slate-500">Start Date</dt><dd class="text-slate-800">{{ $course->start_date?->format('M d, Y') ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Expiry</dt><dd class="text-slate-800">{{ $course->expiry_date?->format('M d, Y') ?? '—' }}</dd></div>
            </dl>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Curriculum</h3>
            @forelse($course->sections as $section)
                <div class="mb-4">
                    <p class="text-sm font-medium text-indigo-600">{{ $section->title }}</p>
                    <ul class="mt-2 space-y-1">
                        @foreach($section->lessons as $lesson)
                            <li class="text-sm text-slate-500 flex items-center gap-2">
                                <x-badge type="info">{{ ucfirst($lesson->lesson_type) }}</x-badge>
                                {{ $lesson->title }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <p class="text-sm text-slate-500">No curriculum added yet.</p>
            @endforelse
        </div>
    </div>

    @if($course->enrollments->count())
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Recent Enrollments</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-slate-500 text-left"><th class="pb-2">Learner</th><th class="pb-2">Status</th><th class="pb-2">Enrolled</th><th class="pb-2">Progress</th></tr></thead>
                <tbody>
                    @foreach($course->enrollments->take(10) as $enrollment)
                    <tr>
                        <td class="py-2.5 text-slate-800">{{ $enrollment->user?->name }}</td>
                        <td class="py-2.5"><x-badge :type="$enrollment->status === 'active' ? 'success' : 'danger'">{{ ucfirst($enrollment->status) }}</x-badge></td>
                        <td class="py-2.5 text-slate-500">{{ $enrollment->enrolled_at?->format('M d, Y') }}</td>
                        <td class="py-2.5 text-slate-500">{{ $enrollment->progress ?? 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
