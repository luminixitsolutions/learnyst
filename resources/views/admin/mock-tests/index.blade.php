@extends('layouts.app')

@section('title', 'Mock test')
@section('page-title', 'Mock test')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search mock tests..."
                   class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            <select name="quiz_type" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Quiz Types</option>
                <option value="online" @selected(request('quiz_type') === 'online')>Online Quiz</option>
                <option value="offline" @selected(request('quiz_type') === 'offline')>Offline Quiz</option>
            </select>
            <select name="status" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-800 shadow-sm text-sm focus:outline-none">
                <option value="">All Status</option>
                @foreach(['draft', 'published', 'unpublished'] as $st)
                    <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <button type="submit" class="panel-btn-secondary">Filter</button>
        </form>
        <a href="{{ route('admin.mock-tests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl panel-btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Mock Test
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($mockTests->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium">Title</th>
                        <th class="px-6 py-4 font-medium">Quiz Type</th>
                        <th class="px-6 py-4 font-medium">Template</th>
                        <th class="px-6 py-4 font-medium">Price</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mockTests as $mockTest)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <p class="text-slate-800 font-semibold">{{ $mockTest->title }}</p>
                            <p class="text-xs text-slate-500">{{ $mockTest->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-6 py-4">{{ $mockTest->quizTypeLabel() }}</td>
                        <td class="px-6 py-4 max-w-xs truncate">{{ $mockTest->templateLabel() }}</td>
                        <td class="px-6 py-4">
                            @if($mockTest->is_free)
                                <span class="text-emerald-600 font-medium">Free</span>
                            @else
                                ₹{{ number_format($mockTest->price, 0) }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($mockTest->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">
                                {{ ucfirst($mockTest->status) }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.mock-tests.destroy', $mockTest) }}" class="inline">
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
        <div class="px-6 py-4 border-t border-slate-200">{{ $mockTests->links() }}</div>
        @else
        <x-empty-state
            title="No mock tests yet"
            description="Create and manage mock tests with timed assessments."
            :action="route('admin.mock-tests.create')"
            actionLabel="Create Mock Test"
        />
        @endif
    </div>
</div>
@endsection
