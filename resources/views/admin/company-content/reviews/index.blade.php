@extends('layouts.app')
@section('title', 'Reviews')
@section('page-title', 'Student Reviews')
@section('breadcrumb', 'Website / Reviews')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div>
        <h3 class="text-xl font-bold text-slate-900">Student reviews</h3>
        <p class="text-sm text-slate-500">Approve reviews submitted from your public institute page.</p>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left"><th class="px-6 py-4">Reviewer</th><th class="px-6 py-4">Rating</th><th class="px-6 py-4">Status</th><th class="px-6 py-4 text-right">Actions</th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $item->reviewer_name }}</div>
                        <div class="text-xs text-slate-500">{{ $item->reviewer_email }}</div>
                        <div class="text-sm text-slate-600 mt-1">{{ $item->content }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $item->rating }}/5</td>
                    <td class="px-6 py-4"><x-badge :type="$item->is_approved ? 'success' : 'warning'">{{ $item->is_approved ? 'Approved' : 'Pending' }}</x-badge></td>
                    <td class="px-6 py-4 text-right space-y-2">
                        @if(!$item->is_approved)
                            <form method="POST" action="{{ route('admin.company-page.reviews.approve', $item) }}" class="inline">@csrf<button class="text-emerald-600 text-sm">Approve</button></form>
                        @else
                            <form method="POST" action="{{ route('admin.company-page.reviews.reject', $item) }}" class="inline">@csrf<button class="text-amber-600 text-sm">Unpublish</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.company-page.reviews.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                            <button class="text-red-600 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No reviews yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
