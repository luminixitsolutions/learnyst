@extends('layouts.app')

@section('title', 'Question Pool')
@section('page-title', 'Question Pool')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-slate-900">Question Pool</h3>
            <p class="text-sm text-slate-500 mt-1">Create and manage question pools</p>
        </div>
        <a href="{{ route('admin.question-pools.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create
        </a>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-1 max-w-md items-center gap-2">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by Title"
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
        </form>
        <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-600 hover:bg-slate-50 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Columns
        </button>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($questionPools->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Questions</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Created</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questionPools as $pool)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $pool->title }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $pool->questions_count }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($pool->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($pool->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $pool->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.question-pools.destroy', $pool) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $questionPools->links() }}</div>
        @else
        <div class="py-20 px-6 text-center">
            <div class="w-24 h-24 mx-auto mb-6 text-slate-300">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    <circle cx="10" cy="10" r="6" stroke-width="1.5"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No results found</h3>
            <p class="text-sm text-slate-500 mt-2">Create your first question pool to get started.</p>
            <a href="{{ route('admin.question-pools.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
