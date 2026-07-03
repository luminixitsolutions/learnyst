@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'My Learning')
@section('breadcrumb', 'Learner Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <x-stat-card title="Active Courses" :value="number_format($enrollments->count())" />
        <x-stat-card title="Certificates" :value="number_format($certificates->count())" />
        <x-stat-card title="Free Resources" :value="number_format($resources->count())" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Continue Learning</h3>
            <a href="{{ route('learner.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($enrollments as $enrollment)
                <a href="{{ route('learner.courses.show', $enrollment->course) }}" class="p-4 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-400/30 transition">
                    @if($enrollment->course?->thumbnail)
                        <img src="{{ Storage::url($enrollment->course->thumbnail) }}" alt="" class="w-full h-32 object-cover rounded-lg mb-3">
                    @else
                        <div class="w-full h-32 rounded-lg bg-slate-800 flex items-center justify-center text-indigo-600 font-bold mb-3">{{ strtoupper(substr($enrollment->course?->title ?? 'C', 0, 2)) }}</div>
                    @endif
                    <p class="text-sm font-semibold text-slate-800">{{ $enrollment->course?->title }}</p>
                    <div class="mt-2 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-500 rounded-full" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $enrollment->progress ?? 0 }}% complete</p>
                </a>
            @empty
                <p class="text-sm text-slate-500 col-span-full">No enrolled courses yet</p>
            @endforelse
        </div>
    </div>

    @if($certificates->count())
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Recent Certificates</h3>
        <div class="space-y-3">
            @foreach($certificates as $certificate)
                <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                    <div>
                        <p class="text-sm text-white">{{ $certificate->course?->title ?? 'Certificate' }}</p>
                        <p class="text-xs text-slate-500 font-mono">{{ $certificate->certificate_number }}</p>
                    </div>
                    <span class="text-xs text-indigo-600">{{ $certificate->issued_at?->format('M d, Y') }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
