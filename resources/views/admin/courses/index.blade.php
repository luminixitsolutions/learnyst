@extends('layouts.app')

@section('title', 'Courses')
@section('page-title', 'Products & Courses')
@section('breadcrumb', 'Manage all courses and products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search courses..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:ring-2 focus:ring-indigo-500/50 focus:outline-none">
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Status</option>
                @foreach(['draft','published','unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <select name="product_type" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Types</option>
                @foreach($productTypes as $type)
                    <option value="{{ $type }}" @selected(request('product_type') === $type)>{{ ucfirst(str_replace('_',' ', $type)) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary hover:bg-slate-700">Filter</button>
        </form>
        <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Course
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($courses->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Course</th>
                        <th class="px-6 py-4 font-medium">Type</th>
                        <th class="px-6 py-4 font-medium">Category</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($course->thumbnail)
                                    <img src="{{ Storage::url($course->thumbnail) }}" alt="" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-indigo-600 text-xs font-bold">{{ strtoupper(substr($course->title,0,2)) }}</div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.courses.show', $course) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $course->title }}</a>
                                    <p class="text-xs text-slate-500">{{ $course->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ ucfirst(str_replace('_',' ', $course->product_type)) }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $course->category?->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-white">{{ $course->is_free ? 'Free' : '₹'.number_format($course->price, 0) }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($course->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($course->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.courses.edit', $course) }}" class="p-2 rounded-lg text-slate-500 hover:text-white hover:bg-slate-100" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.courses.duplicate', $course) }}" class="inline">@csrf<button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100" title="Duplicate"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button></form>
                                @if($course->status !== 'published')
                                <form method="POST" action="{{ route('admin.courses.publish', $course) }}" class="inline">@csrf<button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100" title="Publish"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></button></form>
                                @else
                                <form method="POST" action="{{ route('admin.courses.unpublish', $course) }}" class="inline">@csrf<button type="submit" class="p-2 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-slate-100" title="Unpublish"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg></button></form>
                                @endif
                                <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" class="inline">@csrf @method('DELETE')<button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="p-2 rounded-lg text-slate-500 hover:text-red-400 hover:bg-slate-100" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $courses->links() }}</div>
        @else
        <x-empty-state title="No courses yet" description="Create your first course to get started." :action="route('admin.courses.create')" actionLabel="Create Course" />
        @endif
    </div>
</div>
@endsection
