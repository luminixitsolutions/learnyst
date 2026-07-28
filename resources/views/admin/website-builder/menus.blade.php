@extends('layouts.app')
@section('title', 'Menus & Footer')
@section('page-title', 'Navigation & Footer')
@section('breadcrumb', 'Website / Builder / Menus')
@section('content')
<div class="space-y-6">
    <a href="{{ route('admin.website-builder.index') }}" class="text-sm text-slate-500">← Builder</a>
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @foreach(['header' => $header, 'footer' => $footer] as $location => $items)
        <div class="glass-card rounded-2xl p-5 space-y-4">
            <h3 class="font-bold text-slate-800 capitalize">{{ $location }} menu</h3>
            <form method="POST" action="{{ route('admin.website-builder.menus.store') }}" class="grid grid-cols-1 gap-2">
                @csrf
                <input type="hidden" name="location" value="{{ $location }}" />
                <input name="label" placeholder="Label" required class="panel-input text-sm" />
                <input name="url" placeholder="External URL (optional)" class="panel-input text-sm" />
                <select name="page_id" class="panel-input text-sm">
                    <option value="">— or link to page —</option>
                    @foreach($pages as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                    @endforeach
                </select>
                <button class="panel-btn-primary text-sm w-fit">Add item</button>
            </form>
            <div class="space-y-2 menu-list" data-location="{{ $location }}">
                @foreach($items as $item)
                    <div class="border border-slate-200 rounded-xl p-3 bg-white" data-id="{{ $item->id }}">
                        <form method="POST" action="{{ route('admin.website-builder.menus.update', $item) }}" class="space-y-2">
                            @csrf @method('PUT')
                            <div class="flex items-center gap-2">
                                <span class="cursor-grab text-slate-400 menu-drag">☰</span>
                                <input name="label" value="{{ $item->label }}" class="panel-input text-sm flex-1" />
                            </div>
                            <input name="url" value="{{ $item->url }}" placeholder="URL" class="panel-input text-sm w-full" />
                            <label class="inline-flex items-center gap-2 text-xs"><input type="checkbox" name="is_enabled" value="1" @checked($item->is_enabled) /> Enabled</label>
                            <div class="flex gap-2">
                                <button class="panel-btn-secondary text-xs">Save</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.website-builder.menus.destroy', $item) }}" class="mt-1">@csrf @method('DELETE')
                            <button class="text-xs text-rose-600 font-semibold">Remove</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.querySelectorAll('.menu-list').forEach(list => {
    if (!window.Sortable) return;
    Sortable.create(list, {
        handle: '.menu-drag',
        animation: 150,
        onEnd() {
            const order = [...list.querySelectorAll('[data-id]')].map(el => el.dataset.id);
            fetch(@json(route('admin.website-builder.menus.reorder')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ order }),
            });
        }
    });
});
</script>
@endsection
