@extends('layouts.app')
@section('title', 'Edit '.$page->title)
@section('page-title', 'Edit: '.$page->title)
@section('breadcrumb', 'Website / Builder / Edit')
@section('content')
<div class="space-y-6" x-data>
    <div class="flex flex-wrap gap-2 justify-between">
        <a href="{{ route('admin.website-builder.index') }}" class="text-sm text-slate-500">← Pages</a>
        <div class="flex gap-2">
            @if($page->status==='published')
                <a href="{{ route('website.companies.page', ['slug'=>$company->slug,'pageSlug'=>$page->slug]) }}" target="_blank" class="panel-btn-secondary text-sm">Public preview</a>
            @endif
            <form method="POST" action="{{ route('admin.website-builder.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">@csrf @method('DELETE')
                <button class="panel-btn-secondary text-sm text-rose-600">Delete</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.website-builder.pages.update', $page) }}" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="text-xs text-slate-500">Title</label><input name="title" value="{{ old('title',$page->title) }}" required class="panel-input w-full" /></div>
            <div><label class="text-xs text-slate-500">Slug</label><input name="slug" value="{{ old('slug',$page->slug) }}" required class="panel-input w-full" /></div>
            <div>
                <label class="text-xs text-slate-500">Type</label>
                <select name="page_type" class="panel-input w-full">
                    @foreach(['home','about','contact','faq','testimonials','faculty','gallery','blog','custom'] as $t)
                        <option value="{{ $t }}" @selected(old('page_type',$page->page_type)===$t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500">Status</label>
                <select name="status" class="panel-input w-full">
                    <option value="draft" @selected(old('status',$page->status)==='draft')>Draft</option>
                    <option value="published" @selected(old('status',$page->status)==='published')>Published</option>
                </select>
            </div>
        </div>
        <div><label class="text-xs text-slate-500">SEO title</label><input name="seo_title" value="{{ old('seo_title',$page->seo_title) }}" class="panel-input w-full" /></div>
        <div><label class="text-xs text-slate-500">SEO description</label><textarea name="seo_description" rows="2" class="panel-input w-full">{{ old('seo_description',$page->seo_description) }}</textarea></div>
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="show_in_nav" value="1" @checked(old('show_in_nav',$page->show_in_nav)) /> Show in nav</label>
        <button class="panel-btn-primary">Save page</button>
    </form>

    <div class="glass-card rounded-2xl p-6 space-y-4">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h3 class="font-bold text-slate-800">Blocks</h3>
            <form method="POST" action="{{ route('admin.website-builder.blocks.store', $page) }}" class="flex flex-wrap gap-2 items-end">
                @csrf
                <select name="block_type" class="panel-input text-sm">
                    @foreach($blockTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button class="panel-btn-primary text-sm">Add block</button>
            </form>
        </div>
        <p class="text-xs text-slate-500">Drag to reorder. Disable blocks without deleting.</p>
        <div id="blocks-list" class="space-y-4">
            @foreach($page->blocks as $block)
                @php $c = $block->content ?? []; @endphp
                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50" data-id="{{ $block->id }}">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="cursor-grab text-slate-400 block-drag">☰</span>
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $block->block_type }}</span>
                        <span class="text-sm font-medium text-slate-800">{{ $block->title }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.website-builder.blocks.update', $block) }}" class="space-y-3">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div><label class="text-xs text-slate-500">Block title</label><input name="title" value="{{ $block->title }}" class="panel-input w-full text-sm" /></div>
                            <label class="inline-flex items-center gap-2 text-sm mt-6"><input type="checkbox" name="is_enabled" value="1" @checked($block->is_enabled) /> Enabled</label>
                        </div>
                        @if(in_array($block->block_type, ['hero','cta','text','form','newsletter','courses']))
                            <div><label class="text-xs text-slate-500">Headline</label><input name="headline" value="{{ $c['headline'] ?? '' }}" class="panel-input w-full text-sm" /></div>
                            <div><label class="text-xs text-slate-500">Subheadline / body</label><textarea name="{{ $block->block_type==='text' || $block->block_type==='form' || $block->block_type==='courses' ? 'body' : 'subheadline' }}" rows="2" class="panel-input w-full text-sm">{{ $block->block_type==='text' || $block->block_type==='form' || $block->block_type==='courses' ? ($c['body'] ?? '') : ($c['subheadline'] ?? '') }}</textarea></div>
                            @if(in_array($block->block_type, ['hero','cta']))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div><label class="text-xs text-slate-500">CTA label</label><input name="cta_label" value="{{ $c['cta_label'] ?? '' }}" class="panel-input w-full text-sm" /></div>
                                    <div><label class="text-xs text-slate-500">CTA URL</label><input name="cta_url" value="{{ $c['cta_url'] ?? '' }}" class="panel-input w-full text-sm" /></div>
                                </div>
                                @if($block->block_type==='hero')
                                    <div><label class="text-xs text-slate-500">Image URL</label><input name="image_url" value="{{ $c['image_url'] ?? '' }}" class="panel-input w-full text-sm" /></div>
                                @endif
                            @endif
                        @else
                            <div>
                                <label class="text-xs text-slate-500">Items (JSON array)</label>
                                <textarea name="items_json" rows="4" class="panel-input w-full text-sm font-mono">{{ json_encode($c['items'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</textarea>
                            </div>
                            <div><label class="text-xs text-slate-500">Headline</label><input name="headline" value="{{ $c['headline'] ?? '' }}" class="panel-input w-full text-sm" /></div>
                        @endif
                        <div class="flex gap-2">
                            <button class="panel-btn-secondary text-sm">Save block</button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.website-builder.blocks.destroy', $block) }}" class="mt-2" onsubmit="return confirm('Remove block?')">@csrf @method('DELETE')
                        <button class="text-xs text-rose-600 font-semibold">Remove</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const list = document.getElementById('blocks-list');
    if (!list || !window.Sortable) return;
    Sortable.create(list, {
        handle: '.block-drag',
        animation: 150,
        onEnd: function () {
            const order = [...list.querySelectorAll('[data-id]')].map(el => el.dataset.id);
            fetch(@json(route('admin.website-builder.blocks.reorder', $page)), {
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
})();
</script>
@endsection
