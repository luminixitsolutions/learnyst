@extends('layouts.app')
@section('title', $item->title)
@section('page-title', $item->title)
@section('breadcrumb', 'Student / Library')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="glass-card rounded-2xl p-6">
        <p class="text-sm text-slate-500">{{ \App\Models\LibraryItem::types()[$item->item_type] ?? $item->item_type }} · {{ $item->author }}</p>
        <div class="mt-4 text-slate-700 whitespace-pre-line">{{ $item->description }}</div>
        <div class="mt-6 flex flex-wrap gap-3">
            @if($item->fileUrl())
                <a href="{{ route('learner.library.read', $item) }}" class="px-5 py-2.5 rounded-xl panel-btn-primary">Read online</a>
            @endif
            @if($item->allow_download && $item->file_path)
                <a href="{{ route('learner.library.download', $item) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-800 text-sm font-semibold">Download</a>
            @elseif(!$item->allow_download)
                <span class="text-xs text-slate-500 self-center">Downloads disabled for this item.</span>
            @endif
        </div>
    </div>
</div>
@endsection
