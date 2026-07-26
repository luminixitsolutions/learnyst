@extends('layouts.app')

@section('title', 'Team')
@section('page-title', 'Team')
@section('breadcrumb', 'Website / Team')

@push('styles')
<style>
    .team-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    @media (max-width: 768px) {
        .team-grid { grid-template-columns: 1fr; }
    }

    .team-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.15rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
        display: flex;
        gap: 1rem;
    }

    .team-photo {
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        background: #f1f5f9;
        flex-shrink: 0;
    }

    .team-photo-fallback {
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
        border: 1px solid #e2e8f0;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 1rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .team-card-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .team-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .team-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .team-role {
        font-size: 0.8rem;
        font-weight: 600;
        color: #059669;
        margin-top: 0.15rem;
    }

    .team-bio {
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.55;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .team-card-actions {
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
        'name' => $item->name,
        'role' => $item->role,
        'bio' => $item->bio,
        'is_published' => (bool) $item->is_published,
        'photo_url' => $item->photoUrl(),
        'update_url' => route('admin.company-page.team.update', $item),
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
            this.previewUrl = item.photo_url || null;
            this.editOpen = true;
        },
        closeEdit() {
            this.editOpen = false;
            this.edit = null;
            this.previewUrl = null;
        },
        onPhotoChange(event) {
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
            <h3 class="text-xl font-bold text-slate-900">Team</h3>
            <p class="text-sm text-slate-500 mt-1">
                People shown on your public institute profile
                (<a href="{{ route('website.companies.show', $company->slug) }}#team" target="_blank" class="text-indigo-600 hover:underline">{{ $company->name }}</a>).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('website.companies.show', $company->slug) }}#team" target="_blank" class="panel-btn-secondary">Preview</a>
            <button type="button" class="panel-btn-primary" @click="showAdd = !showAdd">
                <span x-text="showAdd ? 'Close form' : 'Add member'"></span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <div x-show="showAdd" x-cloak x-transition class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.company-page.team.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <h4 class="text-sm font-semibold text-slate-800">Add team member</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-input label="Name" name="name" required />
                <x-form-input label="Role" name="role" placeholder="Founder & Academic Head" />
            </div>
            <x-form-input label="Bio" name="bio" type="textarea" />
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Photo</label>
                <input type="file" name="photo" accept="image/*" class="panel-input">
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300 text-indigo-600">
                Published on public page
            </label>
            <button class="panel-btn-primary" type="submit">Save member</button>
        </form>
    </div>

    <div class="team-grid">
        @forelse($items as $item)
            <article class="team-card">
                @if($item->photoUrl())
                    <img src="{{ $item->photoUrl() }}" class="team-photo" alt="{{ $item->name }}">
                @else
                    <span class="team-photo-fallback">{{ strtoupper(substr($item->name, 0, 1)) }}</span>
                @endif

                <div class="team-card-body">
                    <div class="team-card-top">
                        <div class="min-w-0">
                            <div class="team-name">{{ $item->name }}</div>
                            @if($item->role)
                                <div class="team-role">{{ $item->role }}</div>
                            @endif
                        </div>
                        <div class="team-card-actions">
                            <button
                                type="button"
                                class="action-icon-btn action-icon-btn--edit"
                                title="Edit member"
                                @click="openEdit(@js($editItems->firstWhere('id', $item->id)))"
                            >
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>

                            <form method="POST" action="{{ route('admin.company-page.team.destroy', $item) }}" onsubmit="return confirm('Delete this team member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-icon-btn action-icon-btn--delete" title="Delete member">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($item->bio)
                        <p class="team-bio">{{ $item->bio }}</p>
                    @endif

                    <div>
                        <x-badge :type="$item->is_published ? 'success' : 'warning'">
                            {{ $item->is_published ? 'Published' : 'Hidden' }}
                        </x-badge>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full glass-card rounded-2xl p-12 text-center">
                <p class="font-semibold text-slate-800">No team members yet</p>
                <p class="text-sm text-slate-500 mt-1">Add instructors and leadership for your public institute page.</p>
                <button type="button" class="panel-btn-primary mt-4" @click="showAdd = true">Add member</button>
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
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden" @click.stop>
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h4 class="text-base font-bold text-slate-900">Edit team member</h4>
                <button type="button" class="text-slate-400 hover:text-slate-700" @click="closeEdit()" aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="edit">
                <form method="POST" :action="edit.update_url" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center gap-4">
                        <template x-if="previewUrl || edit.photo_url">
                            <img :src="previewUrl || edit.photo_url" alt="" class="team-photo">
                        </template>
                        <template x-if="!(previewUrl || edit.photo_url)">
                            <span class="team-photo-fallback" x-text="(edit.name || '?').charAt(0).toUpperCase()"></span>
                        </template>
                        <div class="flex-1 space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Replace photo</label>
                            <input type="file" name="photo" accept="image/*" class="panel-input" @change="onPhotoChange($event)">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="edit.name" required class="panel-input">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-semibold text-slate-700">Role</label>
                            <input type="text" name="role" x-model="edit.role" class="panel-input" placeholder="Founder & Academic Head">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-sm font-semibold text-slate-700">Bio</label>
                        <textarea name="bio" rows="4" x-model="edit.bio" class="panel-input"></textarea>
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
