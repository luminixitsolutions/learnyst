@extends('layouts.app')

@section('title', 'Consent Report')
@section('page-title', 'Consent Acceptance Report')
@section('breadcrumb', 'Checkout Consents / Report')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">All accepted consent records from checkout</p>
        <a href="{{ route('admin.checkout-consents.index') }}" class="text-sm text-slate-500 hover:text-white">← Back to Consents</a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($records->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4 font-medium">Consent</th>
                        <th class="px-6 py-4 font-medium">User</th>
                        <th class="px-6 py-4 font-medium">Order</th>
                        <th class="px-6 py-4 font-medium">Accepted At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-white">{{ $record->consent?->title ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <p class="text-slate-800">{{ $record->user?->name ?? '—' }}</p>
                            <p class="text-xs text-slate-500">{{ $record->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($record->order)
                                <a href="{{ route('admin.orders.show', $record->order) }}" class="text-indigo-600 hover:text-indigo-800">{{ $record->order->order_number }}</a>
                            @else
                                <span class="text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $record->accepted_at?->format('M d, Y H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $records->links() }}</div>
        @else
        <x-empty-state title="No acceptance records" description="Consent acceptances will appear here after checkout." />
        @endif
    </div>
</div>
@endsection
