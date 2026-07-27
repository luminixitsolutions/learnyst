@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('breadcrumb', 'Classification / Categories')

@push('styles')
<style>
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
        text-decoration: none;
    }
    .action-icon-btn svg { width: 1rem; height: 1rem; }
    .action-icon-btn--edit { color: #0d9488; border-color: #b6dfdb; background: #ecfdf5; }
    .action-icon-btn--edit:hover { background: #ccfbf1; }
    .action-icon-btn--delete { color: #e11d48; border-color: #fecdd3; background: #fff1f2; }
    .action-icon-btn--delete:hover { background: #ffe4e6; }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="{ editing: null }">
    <a href="{{ route('admin.classification.index') }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>
    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Add Category</h3>
        <form method="POST" action="{{ route('admin.categories.store') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            @csrf
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Icon" name="icon" placeholder="emoji or icon class" />
            <x-form-input label="Description" name="description" />
            <label class="flex items-center gap-2 pb-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Active</span>
            </label>
            <div class="md:col-span-4">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Add Category</button>
            </div>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($categories->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Icon</th>
                        <th class="px-6 py-4">Courses</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4">
                            <template x-if="editing !== {{ $category->id }}">
                                <span class="text-slate-800 font-semibold">{{ $category->name }}</span>
                            </template>
                            <form x-show="editing === {{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex gap-2" x-cloak>
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" class="panel-input py-1.5 text-sm">
                                <input type="hidden" name="is_active" value="{{ $category->is_active ? 1 : 0 }}">
                                <button type="submit" class="text-indigo-600 text-xs font-semibold">Save</button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $category->icon ?? '—' }}</td>
                        <td class="px-6 py-4">{{ $category->courses_count }}</td>
                        <td class="px-6 py-4"><x-badge :type="$category->is_active ? 'success' : 'danger'">{{ $category->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        @click="editing = editing === {{ $category->id }} ? null : {{ $category->id }}"
                                        class="action-icon-btn action-icon-btn--edit"
                                        title="Edit category">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="action-icon-btn action-icon-btn--delete"
                                            title="Delete category"
                                            @click="deleteForm = $el.closest('form'); deleteModal = true">
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
        <div class="px-6 py-4 border-t border-slate-200">{{ $categories->links() }}</div>
        @else
        <x-empty-state title="No categories yet" />
        @endif
    </div>
</div>
@endsection
