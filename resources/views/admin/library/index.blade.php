@extends('layouts.app')
@section('title', 'Digital Library')
@section('page-title', 'Digital Library')
@section('breadcrumb', 'Digital Library')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-3">
        <form method="POST" action="{{ route('admin.library.import') }}">@csrf
            <button class="px-4 py-2 rounded-xl text-sm text-sky-400">Import existing eBooks/Resources</button>
        </form>
    </div>
    <div class="glass-card rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.library.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <x-form-input label="Title" name="title" required />
            <x-form-input label="Type" name="item_type" type="select" required>
                @foreach($types as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
            </x-form-input>
            <x-form-input label="Access" name="access_mode" type="select" required>
                <option value="enrolled">Enrolled learners</option>
                <option value="public">Public</option>
                <option value="subscription">Active subscription</option>
                <option value="private">Private</option>
            </x-form-input>
            <x-form-input label="Author" name="author" />
            <x-form-input label="Course gate" name="course_id" type="select">
                <option value="">Any enrollment</option>
                @foreach($courses as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
            </x-form-input>
            <x-form-input label="External URL" name="external_url" />
            <x-form-input label="Description" name="description" type="textarea" class="md:col-span-3" />
            <div><label class="text-sm text-slate-300">File</label><input type="file" name="file" class="mt-1 block w-full text-sm text-slate-400"></div>
            <div><label class="text-sm text-slate-300">Cover</label><input type="file" name="cover" accept="image/*" class="mt-1 block w-full text-sm text-slate-400"></div>
            <label class="flex items-center gap-2 self-end pb-2">
                <input type="checkbox" name="allow_download" value="1" class="rounded border-slate-600 bg-slate-800 text-emerald-500">
                <span class="text-sm text-slate-300">Allow download</span>
            </label>
            <label class="flex items-center gap-2 md:col-span-3">
                <input type="checkbox" name="sync_ebook" value="1" class="rounded border-slate-600 bg-slate-800 text-emerald-500">
                <span class="text-sm text-slate-300">Also create Ebook product record</span>
            </label>
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Add to library</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Title</th><th class="px-6 py-4">Type</th><th class="px-6 py-4">Access</th><th class="px-6 py-4">Views</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4 text-white">{{ $item->title }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $types[$item->item_type] ?? $item->item_type }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $item->access_mode }}</td>
                    <td class="px-6 py-4 text-slate-400">{{ $item->view_count }}/{{ $item->download_count }}</td>
                    <td class="px-6 py-4">
                        <form method="POST" action="{{ route('admin.library.destroy', $item) }}">@csrf @method('DELETE')
                            <button class="text-red-400 text-sm" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">Library empty.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $items->links() }}</div>
    </div>
</div>
@endsection
