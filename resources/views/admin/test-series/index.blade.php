@extends('layouts.app')

@section('title', 'Test Series')
@section('page-title', 'Test Series')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search test series..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Status</option>
                @foreach(['draft', 'published', 'unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.test-series.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Test Series
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($testSeries->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testSeries as $series)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="text-slate-800 font-semibold">{{ $series->title }}</p>
                            <p class="text-xs text-slate-500">{{ $series->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($series->is_free)
                                <span class="text-emerald-600 font-medium">Free</span>
                            @else
                                ₹{{ number_format($series->price, 0) }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($series->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($series->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.test-series.destroy', $series) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $testSeries->links() }}</div>
        @else
        <x-empty-state
            title="No test series yet"
            description="Organize grouped test series and practice exams for your learners."
            :action="route('admin.test-series.create')"
            actionLabel="Create Test Series"
        />
        @endif
    </div>
</div>
@endsection
