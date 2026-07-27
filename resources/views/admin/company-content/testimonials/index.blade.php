@extends('layouts.app')

@section('title', 'Testimonials')
@section('page-title', 'Testimonials')
@section('breadcrumb', 'Website / Testimonials')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<x-admin.datatable-styles />
<style>
    .tm-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 700px) {
        .tm-stat-grid { grid-template-columns: 1fr; }
    }
    .tm-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 1.15rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .tm-stat .label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
    .tm-stat .value { margin-top: .35rem; font-size: 1.5rem; font-weight: 700; color: #0f172a; }

    .tm-filter-chip {
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
    .tm-filter-chip.is-active {
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
        cursor: pointer;
    }
    .action-icon-btn svg { width: 1rem; height: 1rem; }
    .action-icon-btn--edit { color: #4f46e5; border-color: #c7d2fe; background: #eef2ff; }
    .action-icon-btn--edit:hover { background: #e0e7ff; }
    .action-icon-btn--publish { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }
    .action-icon-btn--publish:hover { background: #d1fae5; }
    .action-icon-btn--hide { color: #d97706; border-color: #fde68a; background: #fffbeb; }
    .action-icon-btn--hide:hover { background: #fef3c7; }
    .action-icon-btn--delete { color: #e11d48; border-color: #fecdd3; background: #fff1f2; }
    .action-icon-btn--delete:hover { background: #ffe4e6; }

    .rating-stars { color: #f59e0b; letter-spacing: 1px; font-size: .9rem; }
    .tm-content-cell { max-width: 380px; color: #475569; line-height: 1.5; }
    .tm-avatar {
        width: 2.5rem; height: 2.5rem; border-radius: .85rem;
        object-fit: cover; border: 1px solid #e2e8f0; background: #f1f5f9;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700; color: #64748b;
    }
</style>
@endpush

@section('content')
@php
    $status = $status ?? request('status');
    $editItems = $items->map(fn ($item) => [
        'id' => $item->id,
        'author_name' => $item->author_name,
        'author_title' => $item->author_title,
        'content' => $item->content,
        'rating' => (int) $item->rating,
        'is_published' => (bool) $item->is_published,
        'avatar_url' => $item->avatarUrl(),
        'update_url' => route('admin.company-page.testimonials.update', $item),
    ])->values();
@endphp

<div
    class="space-y-6"
    x-data="{
        showAdd: false,
        editOpen: false,
        edit: null,
        openEdit(item) {
            this.edit = Object.assign({}, item);
            this.editOpen = true;
        },
        closeEdit() {
            this.editOpen = false;
            this.edit = null;
        }
    }"
>
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Testimonials</h3>
            <p class="text-sm text-slate-500 mt-1">
                Featured quotes shown on your public institute page
                (<a href="{{ route('website.companies.show', $company->slug) }}#testimonials" target="_blank" class="text-indigo-600 hover:underline">{{ $company->name }}</a>).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.company-page.testimonials') }}" class="tm-filter-chip {{ blank($status) ? 'is-active' : '' }}">All</a>
            <a href="{{ route('admin.company-page.testimonials', ['status' => 'published']) }}" class="tm-filter-chip {{ $status === 'published' ? 'is-active' : '' }}">Published</a>
            <a href="{{ route('admin.company-page.testimonials', ['status' => 'hidden']) }}" class="tm-filter-chip {{ $status === 'hidden' ? 'is-active' : '' }}">Hidden</a>
            <a href="{{ route('website.companies.show', $company->slug) }}#testimonials" target="_blank" class="panel-btn-secondary">Preview</a>
            <button type="button" class="panel-btn-primary" @click="showAdd = !showAdd">
                <span x-text="showAdd ? 'Close form' : 'Add testimonial'"></span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="tm-stat-grid">
        <div class="tm-stat">
            <div class="label">Total</div>
            <div class="value">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <div class="tm-stat">
            <div class="label">Published</div>
            <div class="value">{{ number_format($stats['published'] ?? 0) }}</div>
        </div>
        <div class="tm-stat">
            <div class="label">Hidden</div>
            <div class="value">{{ number_format($stats['hidden'] ?? 0) }}</div>
        </div>
    </div>

    <div x-show="showAdd" x-cloak x-transition class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.company-page.testimonials.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <h4 class="text-sm font-semibold text-slate-800">Add testimonial</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Author name" name="author_name" required />
                <x-form-input label="Author title" name="author_title" placeholder="Student / Parent" />
                <x-form-input label="Rating (1-5)" name="rating" type="number" :value="5" />
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Avatar</label>
                    <input type="file" name="avatar" accept="image/*" class="panel-input">
                </div>
            </div>
            <x-form-input label="Testimonial" name="content" type="textarea" required />
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300 text-indigo-600">
                Published on public page
            </label>
            <button class="panel-btn-primary" type="submit">Save testimonial</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($items->count())
            <div class="overflow-x-auto">
                <table id="testimonialsTable" class="w-full text-sm panel-table display" style="width:100%">
                    <thead>
                        <tr class="text-left">
                            <th class="px-6 py-4">Author</th>
                            <th class="px-6 py-4">Testimonial</th>
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
                                    <div class="flex items-center gap-3">
                                        @if($item->avatarUrl())
                                            <img src="{{ $item->avatarUrl() }}" alt="" class="tm-avatar">
                                        @else
                                            <span class="tm-avatar">{{ strtoupper(substr($item->author_name, 0, 1)) }}</span>
                                        @endif
                                        <div>
                                            <div class="font-semibold text-slate-800">{{ $item->author_name }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $item->author_title ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    <div class="tm-content-cell">{{ $item->content }}</div>
                                </td>
                                <td class="px-6 py-4 align-top" data-order="{{ $item->rating }}">
                                    <div class="font-semibold text-slate-800">{{ $item->rating }}/5</div>
                                    <div class="rating-stars" aria-hidden="true">{{ str_repeat('★', (int) $item->rating) }}{{ str_repeat('☆', 5 - (int) $item->rating) }}</div>
                                </td>
                                <td class="px-6 py-4 align-top" data-order="{{ $item->is_published ? 1 : 0 }}">
                                    <x-badge :type="$item->is_published ? 'success' : 'warning'">
                                        {{ $item->is_published ? 'Published' : 'Hidden' }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 align-top whitespace-nowrap" data-order="{{ optional($item->created_at)->timestamp }}">
                                    {{ optional($item->created_at)->format('M d, Y') }}
                                    <div class="text-xs text-slate-400">{{ optional($item->created_at)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 align-top text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <button
                                            type="button"
                                            class="action-icon-btn action-icon-btn--edit"
                                            title="Edit"
                                            @click="openEdit(@js($editItems->firstWhere('id', $item->id)))"
                                        >
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.company-page.testimonials.toggle', $item) }}">
                                            @csrf
                                            <button type="submit" class="action-icon-btn {{ $item->is_published ? 'action-icon-btn--hide' : 'action-icon-btn--publish' }}" title="{{ $item->is_published ? 'Hide' : 'Publish' }}">
                                                @if($item->is_published)
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                @else
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.company-page.testimonials.destroy', $item) }}" onsubmit="return confirm('Delete this testimonial?')">
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
                <p class="font-semibold text-slate-800">No testimonials yet</p>
                <p class="text-sm text-slate-500 mt-1">Add your first featured quote for the public institute page.</p>
                <button type="button" class="panel-btn-primary mt-4" @click="showAdd = true">Add testimonial</button>
            </div>
        @endif
    </div>

    {{-- Edit modal --}}
    <div
        x-show="editOpen"
        x-cloak
        class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        @keydown.escape.window="closeEdit()"
    >
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeEdit()"></div>
        <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h4 class="text-base font-bold text-slate-900">Edit testimonial</h4>
                <button type="button" class="text-slate-400 hover:text-slate-700" @click="closeEdit()" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="edit">
                <form method="POST" :action="edit.update_url" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Author name <span class="text-red-500">*</span></label>
                            <input type="text" name="author_name" x-model="edit.author_name" required class="panel-input">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Author title</label>
                            <input type="text" name="author_title" x-model="edit.author_title" class="panel-input" placeholder="Student / Parent">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Rating (1-5)</label>
                            <input type="number" name="rating" min="1" max="5" x-model="edit.rating" required class="panel-input">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Avatar</label>
                            <input type="file" name="avatar" accept="image/*" class="panel-input">
                            <template x-if="edit.avatar_url">
                                <div class="pt-1">
                                    <img :src="edit.avatar_url" alt="" class="tm-avatar">
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Testimonial <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="4" x-model="edit.content" required class="panel-input"></textarea>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_published" :value="edit.is_published ? 1 : 0">
                        <input type="checkbox" x-model="edit.is_published" class="rounded border-slate-300 text-indigo-600">
                        Published on public page
                    </label>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" class="panel-btn-secondary" @click="closeEdit()">Cancel</button>
                        <button type="submit" class="panel-btn-primary">Save changes</button>
                    </div>
                </form>
            </template>
        </div>
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

    const $table = jQuery('#testimonialsTable');
    $table.addClass('cell-border row-border');

    $table.DataTable({
        order: [[4, 'desc']],
        pageLength: 10,
        lengthChange: false,
        dom: '<"dt-toolbar"f>rt<"dt-footer"ip>',
        columnDefs: [
            { orderable: false, targets: [5] },
            { searchable: false, targets: [5] }
        ],
        language: {
            search: 'Search:',
            info: 'Showing _START_ to _END_ of _TOTAL_ testimonials',
            infoEmpty: 'No testimonials available',
            zeroRecords: 'No matching testimonials found',
            paginate: { previous: 'Previous', next: 'Next' }
        }
    });
})();
</script>
@endif
@endpush
