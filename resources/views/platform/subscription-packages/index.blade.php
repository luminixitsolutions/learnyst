@extends('layouts.app')

@section('title', 'Subscription Packages')
@section('page-title', 'Subscription Packages')
@section('breadcrumb', 'Platform Admin / Pricing Packages')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <p class="text-sm text-slate-600 max-w-2xl">
            Manage the plans shown on the public <a href="{{ route('website.pricing') }}" target="_blank" class="text-indigo-600 hover:underline">/pricing</a> page. Active packages appear to visitors in sort order.
        </p>
        <a href="{{ route('platform.subscription-packages.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-soft shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add package
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead>
                <tr class="text-left">
                    <th class="px-6 py-4">Package</th>
                    <th class="px-6 py-4">Monthly</th>
                    <th class="px-6 py-4">Yearly</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Order</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $package)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800 flex items-center gap-2">
                                {{ $package->name }}
                                @if($package->is_featured)
                                    <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100">Featured</span>
                                @endif
                                @if($package->badge)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $package->badge }}</span>
                                @endif
                            </div>
                            @if($package->tagline)
                                <div class="text-xs text-slate-400 mt-0.5">{{ $package->tagline }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            @if($package->is_custom)
                                Custom
                            @elseif($package->is_free)
                                Free
                            @else
                                {{ $package->formattedPrice('monthly') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            @if($package->is_custom || $package->is_free)
                                —
                            @else
                                {{ $package->formattedPrice('yearly') }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :type="$package->is_active ? 'success' : 'warning'">
                                {{ $package->is_active ? 'Active' : 'Hidden' }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $package->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('platform.subscription-packages.toggle', $package) }}">
                                    @csrf
                                    <button type="submit" class="text-xs font-medium text-slate-500 hover:text-indigo-600 px-2 py-1">
                                        {{ $package->is_active ? 'Hide' : 'Publish' }}
                                    </button>
                                </form>
                                <a href="{{ route('platform.subscription-packages.edit', $package) }}"
                                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 px-2 py-1">Edit</a>
                                <form method="POST" action="{{ route('platform.subscription-packages.destroy', $package) }}"
                                      onsubmit="return confirm('Delete this package?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800 px-2 py-1">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <p class="text-slate-500">No subscription packages yet.</p>
                            <a href="{{ route('platform.subscription-packages.create') }}" class="inline-block mt-3 text-sm font-semibold text-indigo-600 hover:underline">Create your first package →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
