@extends('layouts.app')

@section('title', 'Gallery')
@section('page-title', 'Gallery')
@section('breadcrumb', 'Website / Gallery')

@push('styles')
<style>
    .gallery-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 700px) {
        .gallery-stat-grid { grid-template-columns: 1fr; }
    }
    .gallery-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1rem 1.15rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
    }
    .gallery-stat .label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
    .gallery-stat .value { margin-top: .35rem; font-size: 1.5rem; font-weight: 700; color: #0f172a; }

    .gallery-filter-chip {
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
    .gallery-filter-chip.is-active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 1024px) {
        .gallery-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .gallery-grid { grid-template-columns: 1fr; }
    }

    .gallery-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
    }
    .gallery-card-media {
        position: relative;
        aspect-ratio: 16 / 10;
        background: #0f172a;
        overflow: hidden;
    }
    .gallery-card-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .gallery-card-body {
        padding: 0.9rem 1rem 1rem;
        display: grid;
        gap: 0.75rem;
        flex: 1;
    }
    .gallery-card-caption {
        font-size: 0.875rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.4;
        min-height: 2.5rem;
    }
    .gallery-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
    }
    .gallery-card-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
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

    .gallery-pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem 1.25rem;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 0 0 1rem 1rem;
    }
    .gallery-pagination nav span,
    .gallery-pagination nav a {
        color: #64748b !important;
    }
</style>
@endpush

@section('content')
@php
    $status = $status ?? request('status');
    $editItems = $items->getCollection()->map(fn ($item) => [
        'id' => $item->id,
        'caption' => $item->caption,
        'is_published' => (bool) $item->is_published,
        'image_url' => $item->imageUrl(),
        'update_url' => route('admin.company-page.gallery.update', $item),
    ])->values();
@endphp

<div
    class="space-y-6"
    x-data="{
        showUpload: false,
        editOpen: false,
        edit: null,
        previewUrl: null,
        openEdit(item) {
            this.edit = Object.assign({}, item);
            this.previewUrl = item.image_url || null;
            this.editOpen = true;
        },
        closeEdit() {
            this.editOpen = false;
            this.edit = null;
            this.previewUrl = null;
        },
        onImageChange(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
                URL.revokeObjectURL(this.previewUrl);
            }
            this.previewUrl = URL.createObjectURL(file);
        }
    }"
>
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Gallery</h3>
            <p class="text-sm text-slate-500 mt-1">
                Images shown on your public institute profile
                (<a href="{{ route('website.companies.show', $company->slug) }}#gallery" target="_blank" class="text-indigo-600 hover:underline">{{ $company->name }}</a>).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.company-page.gallery') }}" class="gallery-filter-chip {{ blank($status) ? 'is-active' : '' }}">All</a>
            <a href="{{ route('admin.company-page.gallery', ['status' => 'published']) }}" class="gallery-filter-chip {{ $status === 'published' ? 'is-active' : '' }}">Published</a>
            <a href="{{ route('admin.company-page.gallery', ['status' => 'hidden']) }}" class="gallery-filter-chip {{ $status === 'hidden' ? 'is-active' : '' }}">Hidden</a>
            <a href="{{ route('website.companies.show', $company->slug) }}#gallery" target="_blank" class="panel-btn-secondary">Preview</a>
            <button type="button" class="panel-btn-primary" @click="showUpload = !showUpload">
                <span x-text="showUpload ? 'Close upload' : 'Upload images'"></span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
    @endif

    <div class="gallery-stat-grid">
        <div class="gallery-stat">
            <div class="label">Total</div>
            <div class="value">{{ number_format($stats['total'] ?? 0) }}</div>
        </div>
        <div class="gallery-stat">
            <div class="label">Published</div>
            <div class="value">{{ number_format($stats['published'] ?? 0) }}</div>
        </div>
        <div class="gallery-stat">
            <div class="label">Hidden</div>
            <div class="value">{{ number_format($stats['hidden'] ?? 0) }}</div>
        </div>
    </div>

    <div x-show="showUpload" x-cloak x-transition class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.company-page.gallery.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <h4 class="text-sm font-semibold text-slate-800">Upload gallery images</h4>
            <x-form-input label="Caption (optional, applied to all uploaded)" name="caption" />
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Images <span class="text-red-500">*</span></label>
                <input type="file" name="images[]" accept="image/*" multiple required class="panel-input">
                <p class="text-xs text-slate-400">You can select multiple images. Max 5MB each.</p>
            </div>
            <button class="panel-btn-primary" type="submit">Upload</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($items->count())
            <div class="p-4 sm:p-5">
                <div class="gallery-grid">
                    @foreach($items as $item)
                        <article class="gallery-card">
                            <div class="gallery-card-media">
                                <img src="{{ $item->imageUrl() }}" alt="{{ $item->caption ?: 'Gallery image' }}">
                            </div>
                            <div class="gallery-card-body">
                                <div class="gallery-card-caption">
                                    {{ $item->caption ?: 'Untitled image' }}
                                </div>
                                <div class="gallery-card-meta">
                                    <x-badge :type="$item->is_published ? 'success' : 'warning'">
                                        {{ $item->is_published ? 'Published' : 'Hidden' }}
                                    </x-badge>
                                    <div class="gallery-card-actions">
                                        <button
                                            type="button"
                                            class="action-icon-btn action-icon-btn--edit"
                                            title="Edit image & caption"
                                            @click="openEdit(@js($editItems->firstWhere('id', $item->id)))"
                                        >
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.company-page.gallery.toggle', $item) }}">
                                            @csrf
                                            <button type="submit" class="action-icon-btn {{ $item->is_published ? 'action-icon-btn--hide' : 'action-icon-btn--publish' }}" title="{{ $item->is_published ? 'Hide' : 'Publish' }}">
                                                @if($item->is_published)
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                @else
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                @endif
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.company-page.gallery.destroy', $item) }}" onsubmit="return confirm('Delete this gallery image?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon-btn action-icon-btn--delete" title="Delete">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            @if($items->hasPages())
                <div class="gallery-pagination">
                    <div class="text-sm text-slate-500">
                        Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} images
                    </div>
                    <div>{{ $items->links() }}</div>
                </div>
            @else
                <div class="gallery-pagination">
                    <div class="text-sm text-slate-500">
                        Showing {{ $items->count() }} of {{ $items->total() }} images
                    </div>
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <p class="font-semibold text-slate-800">No gallery images yet</p>
                <p class="text-sm text-slate-500 mt-1">Upload campus photos, classroom moments, and event highlights.</p>
                <button type="button" class="panel-btn-primary mt-4" @click="showUpload = true">Upload images</button>
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
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h4 class="text-base font-bold text-slate-900">Edit gallery image</h4>
                <button type="button" class="text-slate-400 hover:text-slate-700" @click="closeEdit()" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="edit">
                <form method="POST" :action="edit.update_url" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video">
                        <img :src="previewUrl || edit.image_url" alt="" class="w-full h-full object-cover">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Replace image</label>
                        <input type="file" name="image" accept="image/*" class="panel-input" @change="onImageChange($event)">
                        <p class="text-xs text-slate-400">Leave empty to keep the current image. Max 5MB.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Caption</label>
                        <input type="text" name="caption" x-model="edit.caption" class="panel-input" placeholder="Optional caption">
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
