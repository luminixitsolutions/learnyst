@extends('layouts.app')

@section('title', 'Blogs')
@section('page-title', 'Institute Blogs')
@section('breadcrumb', 'Website / Blogs')

@push('styles')
<style>
    .blog-list { display: grid; gap: 1rem; }

    .blog-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
        display: flex;
        flex-direction: column;
    }

    @media (min-width: 640px) {
        .blog-card { flex-direction: row; }
    }

    .blog-cover {
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #f1f5f9;
        flex-shrink: 0;
        overflow: hidden;
    }

    @media (min-width: 640px) {
        .blog-cover {
            width: 10rem;
            aspect-ratio: auto;
            min-height: 100%;
        }
    }

    .blog-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .blog-cover-fallback {
        width: 100%;
        height: 100%;
        min-height: 6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #eef2ff, #f0f9ff);
        color: #6366f1;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .blog-card-body {
        flex: 1;
        padding: 1rem 1.15rem;
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        min-width: 0;
    }

    .blog-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .blog-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
    }

    .blog-excerpt {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: auto;
        flex-wrap: wrap;
    }

    .blog-view-link {
        font-size: 0.8rem;
        font-weight: 600;
        color: #4f46e5;
        text-decoration: none;
    }
    .blog-view-link:hover { text-decoration: underline; }

    .blog-card-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
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
    .action-icon-btn--delete { color: #e11d48; border-color: #fecdd3; background: #fff1f2; }
    .action-icon-btn--delete:hover { background: #ffe4e6; }
</style>
@endpush

@section('content')
@php
    $editItems = $items->getCollection()->map(fn ($item) => [
        'id' => $item->id,
        'title' => $item->title,
        'excerpt' => $item->excerpt,
        'body' => $item->body,
        'is_published' => (bool) $item->is_published,
        'cover_url' => $item->cover_image ? $item->coverUrl() : null,
        'update_url' => route('admin.company-page.blogs.update', $item),
        'view_url' => route('website.companies.blog', [$company->slug, $item->slug]),
    ])->values();
@endphp

<div
    class="space-y-6 max-w-5xl"
    x-data="{
        showAdd: false,
        editOpen: false,
        edit: null,
        previewUrl: null,
        openEdit(item) {
            this.edit = Object.assign({}, item);
            this.previewUrl = item.cover_url || null;
            this.editOpen = true;
        },
        closeEdit() {
            this.editOpen = false;
            this.edit = null;
            this.previewUrl = null;
        },
        onCoverChange(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            if (this.previewUrl && this.previewUrl.startsWith('blob:')) {
                URL.revokeObjectURL(this.previewUrl);
            }
            this.previewUrl = URL.createObjectURL(file);
        }
    }"
>
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Blogs</h3>
            <p class="text-sm text-slate-500 mt-1">
                Articles shown on your public institute page
                (<a href="{{ route('website.companies.show', $company->slug) }}#blogs" target="_blank" class="text-indigo-600 hover:underline">{{ $company->name }}</a>).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('website.companies.show', $company->slug) }}#blogs" target="_blank" class="panel-btn-secondary">Preview</a>
            <button type="button" class="panel-btn-primary" @click="showAdd = !showAdd">
                <span x-text="showAdd ? 'Close form' : 'Add blog post'"></span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div x-show="showAdd" x-cloak x-transition class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.company-page.blogs.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <h4 class="text-sm font-semibold text-slate-800">Create blog post</h4>
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Excerpt" name="excerpt" type="textarea" placeholder="Short summary shown in the blog list" />
            <x-form-input label="Body" name="body" type="textarea" placeholder="Full article content" />
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Cover image</label>
                <input type="file" name="cover_image" accept="image/*" class="panel-input">
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300 text-indigo-600">
                Published on public page
            </label>
            <button class="panel-btn-primary" type="submit">Publish blog</button>
        </form>
    </div>

    <div class="blog-list">
        @forelse($items as $item)
            <article class="blog-card">
                <div class="blog-cover">
                    @if($item->cover_image)
                        <img src="{{ $item->coverUrl() }}" alt="{{ $item->title }}">
                    @else
                        <div class="blog-cover-fallback">{{ strtoupper(substr($item->title, 0, 1)) }}</div>
                    @endif
                </div>

                <div class="blog-card-body">
                    <div class="blog-card-top">
                        <div class="min-w-0">
                            <div class="blog-title">{{ $item->title }}</div>
                            @if($item->excerpt)
                                <p class="blog-excerpt mt-1">{{ $item->excerpt }}</p>
                            @endif
                        </div>
                        <div class="blog-card-actions">
                            <button
                                type="button"
                                class="action-icon-btn action-icon-btn--edit"
                                title="Edit blog post"
                                @click="openEdit(@js($editItems->firstWhere('id', $item->id)))"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            <form method="POST" action="{{ route('admin.company-page.blogs.destroy', $item) }}" onsubmit="return confirm('Delete this blog post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon-btn action-icon-btn--delete" title="Delete blog post">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="blog-meta">
                        <div class="flex items-center gap-2 flex-wrap">
                            <x-badge :type="$item->is_published ? 'success' : 'warning'">
                                {{ $item->is_published ? 'Published' : 'Hidden' }}
                            </x-badge>
                            @if($item->published_at)
                                <span class="text-xs text-slate-400">{{ $item->published_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                        <a class="blog-view-link" target="_blank" href="{{ route('website.companies.blog', [$company->slug, $item->slug]) }}">View post →</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="glass-card rounded-2xl p-12 text-center">
                <p class="font-semibold text-slate-800">No blog posts yet</p>
                <p class="text-sm text-slate-500 mt-1">Share tips, updates, and learning resources with your audience.</p>
                <button type="button" class="panel-btn-primary mt-4" @click="showAdd = true">Add blog post</button>
            </div>
        @endforelse
    </div>

    @if($items->hasPages())
        <div>{{ $items->links() }}</div>
    @endif

    {{-- Edit modal --}}
    <div
        x-show="editOpen"
        x-cloak
        class="fixed inset-0 z-[80] flex items-center justify-center p-4"
        @keydown.escape.window="closeEdit()"
    >
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeEdit()"></div>
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
                <h4 class="text-base font-bold text-slate-900">Edit blog post</h4>
                <button type="button" class="text-slate-400 hover:text-slate-700" @click="closeEdit()" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="edit">
                <form method="POST" :action="edit.update_url" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-900 aspect-video">
                        <template x-if="previewUrl || edit.cover_url">
                            <img :src="previewUrl || edit.cover_url" alt="" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!(previewUrl || edit.cover_url)">
                            <div class="w-full h-full flex items-center justify-center text-indigo-300 text-3xl font-bold" x-text="(edit.title || 'B').charAt(0).toUpperCase()"></div>
                        </template>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Replace cover image</label>
                        <input type="file" name="cover_image" accept="image/*" class="panel-input" @change="onCoverChange($event)">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="edit.title" required class="panel-input">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Excerpt</label>
                        <textarea name="excerpt" rows="2" x-model="edit.excerpt" class="panel-input" placeholder="Short summary"></textarea>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Body</label>
                        <textarea name="body" rows="8" x-model="edit.body" class="panel-input" placeholder="Full article content"></textarea>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="hidden" name="is_published" :value="edit.is_published ? 1 : 0">
                        <input type="checkbox" x-model="edit.is_published" class="rounded border-slate-300 text-indigo-600">
                        Published on public page
                    </label>

                    <div class="flex items-center justify-between gap-2 pt-2">
                        <a :href="edit.view_url" target="_blank" class="text-sm text-indigo-600 hover:underline">View post →</a>
                        <div class="flex items-center gap-2">
                            <button type="button" class="panel-btn-secondary" @click="closeEdit()">Cancel</button>
                            <button type="submit" class="panel-btn-primary">Save changes</button>
                        </div>
                    </div>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection
