@extends('layouts.app')

@section('title', 'Reviews')
@section('page-title', 'Student Reviews')
@section('breadcrumb', 'Website / Reviews')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    .reviews-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 900px) {
        .reviews-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    .reviews-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 1.15rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .reviews-stat .label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
    .reviews-stat .value { margin-top: .35rem; font-size: 1.5rem; font-weight: 700; color: #0f172a; }

    .reviews-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .85rem;
        border-radius: .75rem;
        font-size: .8rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        text-decoration: none;
    }
    .reviews-filter-chip.is-active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
    }

    .action-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.1rem;
        height: 2.1rem;
        border-radius: .7rem;
        border: 1px solid transparent;
        transition: all .15s ease;
        background: #f8fafc;
    }
    .action-icon-btn svg { width: 1rem; height: 1rem; }
    .action-icon-btn--approve { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }
    .action-icon-btn--approve:hover { background: #d1fae5; }
    .action-icon-btn--unpublish { color: #d97706; border-color: #fde68a; background: #fffbeb; }
    .action-icon-btn--unpublish:hover { background: #fef3c7; }
    .action-icon-btn--delete { color: #e11d48; border-color: #fecdd3; background: #fff1f2; }
    .action-icon-btn--delete:hover { background: #ffe4e6; }

    #reviewsTable_wrapper .dataTables_filter input,
    #reviewsTable_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        padding: .45rem .75rem;
        background: #fff;
        color: #0f172a;
        outline: none;
    }
    #reviewsTable_wrapper .dataTables_filter input:focus,
    #reviewsTable_wrapper .dataTables_length select:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    }
    #reviewsTable_wrapper .dataTables_length,
    #reviewsTable_wrapper .dataTables_filter,
    #reviewsTable_wrapper .dataTables_info,
    #reviewsTable_wrapper .dataTables_paginate {
        color: #64748b;
        font-size: .85rem;
        padding: .75rem 1.25rem;
    }
    #reviewsTable_wrapper .dataTables_paginate .paginate_button {
        border-radius: .55rem !important;
        border: 1px solid transparent !important;
        padding: .25rem .55rem !important;
        margin: 0 .1rem !important;
    }
    #reviewsTable_wrapper .dataTables_paginate .paginate_button.current {
        background: #4f46e5 !important;
        color: #fff !important;
        border-color: #4f46e5 !important;
    }
    #reviewsTable_wrapper .dataTables_paginate .paginate_button:hover {
        background: #eef2ff !important;
        color: #4338ca !important;
        border-color: #c7d2fe !important;
    }
    #reviewsTable thead th {
        white-space: nowrap;
    }
    .review-content-cell {
        max-width: 420px;
        color: #475569;
        line-height: 1.5;
    }
    .rating-stars {
        color: #f59e0b;
        letter-spacing: 1px;
        font-size: .9rem;
    }
</style>
@endpush

@section('content')
@php $status = $status ?? request('status'); @endphp

<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Student reviews</h3>
            <p class="text-sm text-slate-500 mt-1">
                Moderate reviews from your public institute page
                (<a href="{{ route('website.companies.show', $company->slug) }}#reviews" target="_blank" class="text-indigo-600 hover:underline">{{ $company->name }}</a>).
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.company-page.reviews') }}" class="reviews-filter-chip {{ blank($status) ? 'is-active' : '' }}">All</a>
            <a href="{{ route('admin.company-page.reviews', ['status' => 'pending']) }}" class="reviews-filter-chip {{ $status === 'pending' ? 'is-active' : '' }}">
                Pending
                @if(($stats['pending'] ?? 0) > 0)
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $status === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-700' }}">{{ $stats['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.company-page.reviews', ['status' => 'approved']) }}" class="reviews-filter-chip {{ $status === 'approved' ? 'is-active' : '' }}">Approved</a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="reviews-stat-grid">
        <div class="reviews-stat">
            <div class="label">Total</div>
            <div class="value">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <div class="reviews-stat">
            <div class="label">Pending</div>
            <div class="value">{{ number_format($stats['pending'] ?? 0) }}</div>
        </div>
        <div class="reviews-stat">
            <div class="label">Approved</div>
            <div class="value">{{ number_format($stats['approved'] ?? 0) }}</div>
        </div>
        <div class="reviews-stat">
            <div class="label">Avg rating</div>
            <div class="value">{{ ($stats['avg_rating'] ?? 0) > 0 ? number_format($stats['avg_rating'], 1) : '—' }}</div>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($items->count())
            <div class="overflow-x-auto">
                <table id="reviewsTable" class="w-full text-sm panel-table display" style="width:100%">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-4">Reviewer</th>
                            <th class="px-6 py-4">Review</th>
                            <th class="px-6 py-4">Rating</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <div class="font-semibold text-slate-800">{{ $item->reviewer_name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $item->reviewer_email ?: '—' }}</div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="review-content-cell">{{ $item->content }}</div>
                                </td>
                                <td class="px-6 py-4 align-top" data-order="{{ $item->rating }}">
                                    <div class="font-semibold text-slate-800">{{ $item->rating }}/5</div>
                                    <div class="rating-stars" aria-hidden="true">{{ str_repeat('★', (int) $item->rating) }}{{ str_repeat('☆', 5 - (int) $item->rating) }}</div>
                                </td>
                                <td class="px-6 py-4 align-top" data-order="{{ $item->is_approved ? 1 : 0 }}">
                                    <x-badge :type="$item->is_approved ? 'success' : 'warning'">
                                        {{ $item->is_approved ? 'Approved' : 'Pending' }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 align-top whitespace-nowrap" data-order="{{ optional($item->created_at)->timestamp }}">
                                    {{ optional($item->created_at)->format('M d, Y') }}
                                    <div class="text-xs text-slate-400">{{ optional($item->created_at)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @if(! $item->is_approved)
                                            <form method="POST" action="{{ route('admin.company-page.reviews.approve', $item) }}">
                                                @csrf
                                                <button type="submit" class="action-icon-btn action-icon-btn--approve" title="Approve & publish">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.company-page.reviews.reject', $item) }}">
                                                @csrf
                                                <button type="submit" class="action-icon-btn action-icon-btn--unpublish" title="Unpublish">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.company-page.reviews.destroy', $item) }}" onsubmit="return confirm('Delete this review permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon-btn action-icon-btn--delete" title="Delete">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="mx-auto w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <p class="font-semibold text-slate-800">No reviews yet</p>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    When students submit reviews on your public institute page, they will appear here for approval.
                </p>
                <a href="{{ route('website.companies.show', $company->slug) }}#reviews" target="_blank" class="inline-flex mt-4 text-sm font-semibold text-indigo-600 hover:underline">
                    Open public reviews section →
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($items->count())
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
(function () {
    if (!window.jQuery || !jQuery.fn.DataTable) return;

    jQuery('#reviewsTable').DataTable({
        order: [[4, 'desc']],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        columnDefs: [
            { orderable: false, targets: [5] },
            { searchable: false, targets: [5] }
        ],
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ reviews',
            info: 'Showing _START_ to _END_ of _TOTAL_ reviews',
            infoEmpty: 'No reviews available',
            zeroRecords: 'No matching reviews found'
        }
    });
})();
</script>
@endif
@endpush
