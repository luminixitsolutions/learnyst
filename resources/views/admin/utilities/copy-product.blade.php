@extends('layouts.app')

@section('title', 'Copy Product')
@section('page-title', 'Copy Product')
@section('breadcrumb', 'Utilities / Copy Product')

@section('content')
<div class="space-y-8">
    <div>
        <a href="{{ route('admin.utilities.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Utilities
        </a>
        <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mt-3">Utilities / Copy Product</p>
    </div>

    <div>
        <h3 class="text-2xl font-bold text-slate-900">Copy Product</h3>
        <p class="text-sm text-slate-500 mt-2">Create a copy of an existing product and view the completion status of your copied products</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach($copyTypes as $type)
            <div class="glass-card rounded-2xl p-6 flex flex-col">
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $type['icon'] }}"/>
                    </svg>
                </div>
                <h4 class="text-base font-bold text-slate-900">{{ $type['title'] }}</h4>
                <p class="text-sm text-slate-500 mt-2 flex-1">{{ $type['description'] }}</p>
                @if(!empty($type['url']))
                    <a href="{{ $type['url'] }}"
                       class="mt-5 w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition text-center">
                        {{ $type['button'] }}
                    </a>
                @else
                    <button type="button"
                            class="mt-5 w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                        {{ $type['button'] }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h4 class="text-base font-bold text-slate-900">Copy Product History</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4 font-medium text-slate-500">Source</th>
                        <th class="px-6 py-4 font-medium text-slate-500">Type</th>
                        <th class="px-6 py-4 font-medium text-slate-500">Destination</th>
                        <th class="px-6 py-4 font-medium text-slate-500">Date</th>
                        <th class="px-6 py-4 font-medium text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $log)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $log->source_title }}</td>
                        <td class="px-6 py-4">{{ $log->typeLabel() }}</td>
                        <td class="px-6 py-4">{{ $log->destination_title }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $log->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <x-badge :type="match($log->status) { 'completed' => 'success', 'failed' => 'default', default => 'warning' }">
                                {{ $log->statusLabel() }}
                            </x-badge>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($history->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">{{ $history->links() }}</div>
        @endif
    </div>
</div>
@endsection
