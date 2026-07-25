@extends('layouts.app')
@section('title', 'Enquiries')
@section('page-title', 'Enquiries')
@section('breadcrumb', 'Website / Enquiries')

@section('content')
@php
    $status = request('status', 'all');
    $newCount = $company->enquiries()->where('status', 'new')->count();
@endphp

<div class="space-y-6 max-w-5xl">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Contact enquiries</h3>
            <p class="text-sm text-slate-500 mt-1">
                Messages from your public institute page
                (<a href="{{ route('website.companies.show', $company->slug) }}#contact" target="_blank" class="text-indigo-600 hover:underline">{{ $company->name }}</a>).
            </p>
        </div>
        @if($newCount > 0)
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 text-amber-800 text-xs font-semibold border border-amber-100">
                {{ $newCount }} new
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-2">
        @foreach(['all' => 'All', 'new' => 'New', 'read' => 'Read', 'replied' => 'Replied'] as $key => $label)
            <a href="{{ route('admin.company-page.enquiries', ['status' => $key === 'all' ? null : $key]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $status === $key || ($key === 'all' && $status === 'all') ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:border-indigo-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($items as $item)
            <div class="glass-card rounded-2xl p-5 {{ $item->status === 'new' ? 'ring-1 ring-amber-200' : '' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-slate-900">{{ $item->name }}</div>
                        <div class="text-sm text-slate-600 mt-1">
                            <a href="mailto:{{ $item->email }}" class="text-indigo-600 hover:underline">{{ $item->email }}</a>
                            @if($item->phone)
                                <span class="text-slate-300 mx-1">·</span>
                                <a href="tel:{{ preg_replace('/\s+/', '', $item->phone) }}" class="hover:underline">{{ $item->phone }}</a>
                            @endif
                        </div>
                        <div class="text-xs text-slate-400 mt-1">{{ $item->created_at?->format('M d, Y · H:i') }}</div>
                        @if($item->subject)
                            <div class="text-sm font-medium text-slate-800 mt-3">{{ $item->subject }}</div>
                        @endif
                        <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $item->message }}</p>
                    </div>
                    <x-badge :type="$item->status === 'new' ? 'warning' : ($item->status === 'replied' ? 'success' : 'info')">
                        {{ ucfirst($item->status) }}
                    </x-badge>
                </div>
                <div class="flex flex-wrap gap-3 mt-4 pt-4 border-t border-slate-100">
                    @if($item->status !== 'read')
                        <form method="POST" action="{{ route('admin.company-page.enquiries.mark', [$item, 'read']) }}">
                            @csrf
                            <button class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Mark read</button>
                        </form>
                    @endif
                    @if($item->status !== 'replied')
                        <form method="POST" action="{{ route('admin.company-page.enquiries.mark', [$item, 'replied']) }}">
                            @csrf
                            <button class="text-sm font-medium text-emerald-600 hover:text-emerald-800">Mark replied</button>
                        </form>
                    @endif
                    @if($item->status !== 'new')
                        <form method="POST" action="{{ route('admin.company-page.enquiries.mark', [$item, 'new']) }}">
                            @csrf
                            <button class="text-sm font-medium text-slate-500 hover:text-slate-800">Mark new</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.company-page.enquiries.destroy', $item) }}" onsubmit="return confirm('Delete this enquiry?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm font-medium text-rose-600 hover:text-rose-800">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-10 text-center">
                <p class="text-slate-500">No enquiries yet.</p>
                <p class="text-sm text-slate-400 mt-2">When visitors submit the form on your public institute page, they will appear here.</p>
                <a href="{{ route('website.companies.show', $company->slug) }}#contact" target="_blank" class="inline-block mt-4 text-sm font-semibold text-indigo-600 hover:underline">
                    Open public contact form →
                </a>
            </div>
        @endforelse
    </div>

    {{ $items->withQueryString()->links() }}
</div>
@endsection
