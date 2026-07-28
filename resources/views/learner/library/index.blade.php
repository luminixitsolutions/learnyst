@extends('layouts.app')
@section('title', 'Library')
@section('page-title', 'Digital Library')
@section('breadcrumb', 'Student / Library')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('learner.library.index') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !request('type')?'bg-emerald-600 text-white':'bg-slate-100' }}">All</a>
        @foreach($types as $k=>$v)
            <a href="{{ route('learner.library.index', ['type'=>$k]) }}" class="px-3 py-1.5 rounded-lg text-sm {{ request('type')===$k?'bg-emerald-600 text-white':'bg-slate-100' }}">{{ $v }}</a>
        @endforeach
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($items as $item)
            <a href="{{ route('learner.library.show', $item) }}" class="glass-card rounded-2xl p-5 block">
                <div class="text-xs uppercase text-slate-500">{{ $types[$item->item_type] ?? $item->item_type }}</div>
                <h3 class="font-bold text-slate-800 mt-1">{{ $item->title }}</h3>
                <p class="text-xs text-slate-500 mt-2">{{ $item->author }}</p>
            </a>
        @empty
            <div class="col-span-full"><x-empty-state title="No library items" /></div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
