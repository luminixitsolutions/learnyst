@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'My Learning')
@section('breadcrumb', 'Student Panel')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="glass-card rounded-2xl p-6 bg-gradient-to-r from-slate-900 to-emerald-900 text-white">
        <p class="text-sm text-white/70">Welcome back</p>
        <h2 class="text-2xl font-bold mt-1">{{ auth()->user()->name }}</h2>
        <p class="text-sm text-white/80 mt-2">Continue learning from your student panel. Track courses, certificates, and communities in one place.</p>
        <div class="flex flex-wrap gap-3 mt-5">
            <a href="{{ route('learner.courses.index') }}" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-sm font-semibold">My Courses</a>
            <a href="{{ route('learner.wallet.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-semibold">My Wallet</a>
            <a href="{{ route('public.courses') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-semibold">Browse Courses</a>
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-sm font-semibold">Edit Profile</a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Active Courses" :value="number_format($activeCount)" />
        <x-stat-card title="Avg Progress" :value="$avgProgress . '%'" />
        <x-stat-card title="Certificates" :value="number_format($certificateCount)" />
        <x-stat-card title="Orders" :value="number_format($ordersCount)" />
    </div>

    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Continue Learning</h3>
            <a href="{{ route('learner.courses.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($enrollments as $enrollment)
                <a href="{{ route('learner.courses.show', $enrollment->course) }}" class="p-4 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-400/40 transition">
                    @if($enrollment->course?->thumbnailUrl())
                        <img src="{{ $enrollment->course->thumbnailUrl() }}" alt="{{ $enrollment->course->title }}" class="w-full h-32 object-cover rounded-lg mb-3">
                    @else
                        <div class="w-full h-32 rounded-lg bg-slate-800 flex items-center justify-center text-emerald-400 font-bold mb-3">{{ strtoupper(substr($enrollment->course?->title ?? 'C', 0, 2)) }}</div>
                    @endif
                    <p class="text-sm font-semibold text-slate-800">{{ $enrollment->course?->title }}</p>
                    <div class="mt-2 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $enrollment->progress ?? 0 }}%"></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">{{ $enrollment->progress ?? 0 }}% complete</p>
                </a>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-200 p-8 text-center">
                    <p class="text-sm text-slate-500 mb-3">No enrolled courses yet</p>
                    <a href="{{ route('public.courses') }}" class="inline-flex px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold">Browse courses</a>
                </div>
            @endforelse
        </div>
    </div>

    @if($certificates->count())
    <div class="glass-card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Recent Certificates</h3>
            <a href="{{ route('learner.certificates') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all</a>
        </div>
        <div class="space-y-3">
            @foreach($certificates as $certificate)
                <div class="flex items-center justify-between py-2 border-b border-slate-200 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $certificate->course?->title ?? 'Certificate' }}</p>
                        <p class="text-xs text-slate-500 font-mono">{{ $certificate->certificate_number }}</p>
                    </div>
                    <span class="text-xs text-emerald-700">{{ $certificate->issued_at?->format('M d, Y') }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
