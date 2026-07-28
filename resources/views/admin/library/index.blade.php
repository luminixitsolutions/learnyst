@extends('layouts.app')
@section('title', 'Digital Library')
@section('page-title', 'Digital Library')
@section('breadcrumb', 'Digital Library')

@push('styles')
    <x-admin.datatable-styles />
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-3">
        <form method="POST" action="{{ route('admin.library.import') }}">@csrf
            <button class="px-4 py-2 rounded-xl text-sm text-sky-600">Import existing eBooks/Resources</button>
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
            <div><label class="text-sm text-slate-600">File</label><input type="file" name="file" class="mt-1 block w-full text-sm text-slate-500"></div>
            <div><label class="text-sm text-slate-600">Cover</label><input type="file" name="cover" accept="image/*" class="mt-1 block w-full text-sm text-slate-500"></div>
            <label class="flex items-center gap-2 self-end pb-2">
                <input type="checkbox" name="allow_download" value="1" class="rounded border-slate-300 bg-white text-emerald-500">
                <span class="text-sm text-slate-600">Allow download</span>
            </label>
            <label class="flex items-center gap-2 md:col-span-3">
                <input type="checkbox" name="sync_ebook" value="1" class="rounded border-slate-300 bg-white text-emerald-500">
                <span class="text-sm text-slate-600">Also create Ebook product record</span>
            </label>
            <div class="md:col-span-3"><button class="px-5 py-2.5 rounded-xl panel-btn-primary">Add to library</button></div>
        </form>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden panel-datatable-wrapper">
        @if($items->count())
        <div class="overflow-x-auto">
            <table id="libraryTable" class="w-full text-sm panel-table display" style="width:100%">
                <thead>
                    <tr class="text-left">
                        <th class="px-6 py-4">Title</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Access</th>
                        <th class="px-6 py-4">Views</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr class="hover:bg-indigo-50/40">
                        <td class="px-6 py-4 text-slate-800">{{ $item->title }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $types[$item->item_type] ?? $item->item_type }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $item->access_mode }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $item->view_count }}/{{ $item->download_count }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.library.destroy', $item) }}">@csrf @method('DELETE')
                                <button class="text-red-500 text-sm" onclick="return confirm('Delete?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-empty-state title="Library empty" description="Add items using the form above." />
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($items->count())
    <x-admin.datatable-scripts table-id="libraryTable" entity="library items" :order-column="0" order-direction="desc" :action-column="4" export-file-name="library" />
@endif
@endpush
