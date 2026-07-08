@extends('layouts.app')

@section('title', 'Courses')
@section('page-title', 'Courses')
@section('breadcrumb', 'Manage all courses and curriculum')

@section('content')
<div class="space-y-6" x-data="{ showFilters: false, openManage: null }">
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card title="Total Courses" :value="$stats['total']" />
        <x-stat-card title="Active Courses" :value="$stats['active']" />
        <x-stat-card title="Suspended Courses" :value="$stats['suspended']" />
        <x-stat-card title="Enrolled Users" :value="number_format($stats['enrolled_users'])" />
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" class="flex flex-1 flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search course..."
                   class="flex-1 min-w-[200px] px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <button type="button" @click="showFilters = !showFilters"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filters
            </button>
            <button type="submit" class="panel-btn-secondary">Search</button>
        </form>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.courses.index', array_merge(request()->query(), ['export' => '1'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 hover:bg-slate-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Now
            </a>
            <a href="{{ route('admin.bundles.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-amber-200 bg-amber-50 text-sm text-amber-700 hover:bg-amber-100">
                Add Trial Bundle
            </a>
            <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create
            </a>
        </div>
    </div>

    {{-- Filters panel --}}
    <div x-show="showFilters" x-cloak class="glass-card rounded-2xl p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <x-form-input label="Status" name="status" type="select" :value="request('status')">
                <option value="">All Status</option>
                @foreach(['draft','published','unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Product Type" name="product_type" type="select" :value="request('product_type')">
                <option value="">All Types</option>
                @foreach($productTypes as $type)
                    <option value="{{ $type }}" @selected(request('product_type') === $type)>{{ ucfirst(str_replace('_',' ', $type)) }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Category" name="category_id" type="select" :value="request('category_id')">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </x-form-input>
            <x-form-input label="Access Type" name="access_type" type="select" :value="request('access_type')">
                <option value="">All Access</option>
                @foreach(['free','trial','paid'] as $access)
                    <option value="{{ $access }}" @selected(request('access_type') === $access)>{{ ucfirst($access) }}</option>
                @endforeach
            </x-form-input>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="panel-btn-primary px-4 py-2 rounded-xl text-sm">Apply Filters</button>
                <a href="{{ route('admin.courses.index') }}" class="panel-btn-secondary px-4 py-2 rounded-xl text-sm">Clear</a>
            </div>
        </form>
    </div>

    {{-- Course cards --}}
    @if($courses->count())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($courses as $course)
        <div class="glass-card rounded-2xl overflow-hidden flex flex-col">
            <div class="relative h-40 bg-slate-100">
                @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-100 to-purple-100">
                        <span class="text-3xl font-bold text-indigo-400">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                    </div>
                @endif
                <div class="absolute top-3 right-3">
                    <x-badge :type="match($course->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($course->status) }}</x-badge>
                </div>
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $course->title }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $course->lessons_count }} {{ Str::plural('lesson', $course->lessons_count) }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $course->category?->name ?? 'Uncategorized' }} · {{ ucfirst(str_replace('_', ' ', $course->product_type)) }}</p>

                <div class="mt-4 flex items-center gap-2 mt-auto pt-4">
                    <a href="{{ route('admin.courses.builder', $course) }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl panel-btn-primary text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Course Builder
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-600 hover:bg-slate-50">
                            Manage ▾
                        </button>
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 bottom-full mb-1 w-48 bg-white border border-slate-200 rounded-xl shadow-lg z-10 py-1">
                            <a href="{{ route('admin.courses.show', $course) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">View Details</a>
                            <a href="{{ route('admin.courses.builder', ['course' => $course, 'tab' => 'settings']) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Settings</a>
                            <form method="POST" action="{{ route('admin.courses.duplicate', $course) }}">@csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Duplicate</button>
                            </form>
                            @if($course->status !== 'published')
                            <form method="POST" action="{{ route('admin.courses.publish', $course) }}">@csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-slate-50">Publish</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.courses.unpublish', $course) }}">@csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-amber-600 hover:bg-slate-50">Unpublish</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true; open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-slate-50">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $courses->links() }}</div>
    @else
    <x-empty-state title="No courses yet" description="Create your first course to get started." :action="route('admin.courses.create')" actionLabel="Create Course" />
    @endif
</div>
@endsection
