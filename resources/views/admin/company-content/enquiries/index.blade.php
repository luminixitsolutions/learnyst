@extends('layouts.app')
@section('title', 'Enquiries')
@section('page-title', 'Enquiries')
@section('breadcrumb', 'Website / Enquiries')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div>
        <h3 class="text-xl font-bold text-slate-900">Contact enquiries</h3>
        <p class="text-sm text-slate-500">Messages submitted from your institute contact form.</p>
    </div>
    <div class="space-y-4">
        @forelse($items as $item)
            <div class="glass-card rounded-2xl p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold text-slate-900">{{ $item->name }} · {{ $item->email }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $item->phone }} · {{ $item->created_at?->format('M d, Y H:i') }}</div>
                        @if($item->subject)<div class="text-sm font-medium text-slate-700 mt-2">{{ $item->subject }}</div>@endif
                        <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $item->message }}</p>
                    </div>
                    <x-badge :type="$item->status === 'new' ? 'warning' : 'success'">{{ ucfirst($item->status) }}</x-badge>
                </div>
                <div class="flex flex-wrap gap-3 mt-4">
                    <form method="POST" action="{{ route('admin.company-page.enquiries.mark', [$item, 'read']) }}">@csrf<button class="text-sm text-indigo-600">Mark read</button></form>
                    <form method="POST" action="{{ route('admin.company-page.enquiries.mark', [$item, 'replied']) }}">@csrf<button class="text-sm text-emerald-600">Mark replied</button></form>
                    <form method="POST" action="{{ route('admin.company-page.enquiries.destroy', $item) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-sm text-red-600">Delete</button></form>
                </div>
            </div>
        @empty
            <div class="glass-card rounded-2xl p-8 text-center text-slate-500">No enquiries yet.</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
