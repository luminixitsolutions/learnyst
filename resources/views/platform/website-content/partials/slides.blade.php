@php
    $items = old('items', $content['items'] ?? []);
    if (!is_array($items) || count($items) === 0) {
        $items = [['image' => '', 'title' => '', 'text' => '', 'is_active' => true]];
    }
@endphp

<div x-data="websiteRepeater(@js($items))" class="space-y-4">
    <template x-for="(item, index) in items" :key="index">
        <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-slate-700" x-text="'Slide ' + (index + 1)"></h4>
                <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Title</label>
                    <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" required>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-sm font-semibold text-slate-700">Image</label>
                    <input type="hidden" :name="'items['+index+'][existing_image]'" :value="item.image || ''">
                    <input type="file" :name="'items['+index+'][image]'" accept="image/*" class="panel-input">
                    <template x-if="item.image">
                        <p class="text-xs text-slate-500 truncate" x-text="'Current: ' + item.image"></p>
                    </template>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Supporting text</label>
                <textarea :name="'items['+index+'][text]'" x-model="item.text" rows="3" class="panel-input"></textarea>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" :name="'items['+index+'][is_active]'" value="1" x-model="item.is_active" class="rounded border-slate-300 text-indigo-600">
                Active on homepage
            </label>
        </div>
    </template>
    <button type="button" class="panel-btn-secondary" @click="add({image:'', title:'', text:'', is_active:true})">+ Add slide</button>
</div>

@include('platform.website-content.partials._repeater-script')
