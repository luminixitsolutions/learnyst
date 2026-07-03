@extends('layouts.app')

@section('title', 'Bundles')
@section('page-title', 'Course Bundles')
@section('breadcrumb', 'Manage product bundles')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search bundles..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Status</option>
                @foreach(['draft', 'published', 'unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary hover:bg-slate-700">Filter</button>
        </form>
        <a href="{{ route('admin.bundles.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Bundle
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($bundles->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Bundle</th>
                        <th class="px-6 py-4 font-medium">Courses</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bundles as $bundle)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.bundles.show', $bundle) }}" class="text-slate-800 font-semibold hover:text-indigo-600">{{ $bundle->title }}</a>
                            <p class="text-xs text-slate-500">{{ $bundle->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-300">{{ $bundle->courses_count }}</td>
                        <td class="px-6 py-4 text-white">₹{{ number_format($bundle->price ?? 0, 0) }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($bundle->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($bundle->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.bundles.edit', $bundle) }}" class="text-indigo-600 hover:text-indigo-800 text-sm mr-3">Edit</a>
                            <form method="POST" action="{{ route('admin.bundles.destroy', $bundle) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $bundles->links() }}</div>
        @else
        <x-empty-state title="No bundles yet" description="Create bundles to sell multiple courses together." :action="route('admin.bundles.create')" actionLabel="Create Bundle" />
        @endif
    </div>
</div>
@endsection
