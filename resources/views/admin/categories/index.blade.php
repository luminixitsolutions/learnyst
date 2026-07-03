@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('breadcrumb', 'Classification / Categories')

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
                    <tr>
                        <td class="px-6 py-4">
                            <template x-if="editing !== {{ $category->id }}">
                                <span class="text-slate-800 font-semibold">{{ $category->name }}</span>
                            </template>
                            <form x-show="editing === {{ $category->id }}" method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex gap-2" x-cloak>
                                @csrf @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" class="px-2 py-1 rounded bg-slate-900 border border-slate-200 text-white text-sm">
                                <input type="hidden" name="is_active" value="{{ $category->is_active ? 1 : 0 }}">
                                <button type="submit" class="text-indigo-600 text-xs">Save</button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $category->icon ?? '—' }}</td>
                        <td class="px-6 py-4 text-white">{{ $category->courses_count }}</td>
                        <td class="px-6 py-4"><x-badge :type="$category->is_active ? 'success' : 'danger'">{{ $category->is_active ? 'Active' : 'Inactive' }}</x-badge></td>
                        <td class="px-6 py-4 text-right">
                            <button @click="editing = editing === {{ $category->id }} ? null : {{ $category->id }}" class="text-indigo-600 text-sm mr-3">Edit</button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline">@csrf @method('DELETE')
                                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-400 text-sm">Delete</button>
                            </form>
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
