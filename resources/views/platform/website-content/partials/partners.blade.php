@php
    $items = old('items', $content['items'] ?? [['name' => '', 'image' => '']]);
@endphp

<div x-data="websiteRepeater(@js($items))" class="space-y-4">
    <template x-for="(item, index) in items" :key="index">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-slate-200 p-4 bg-slate-50/60">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Partner name</label>
                <input type="text" :name="'items['+index+'][name]'" x-model="item.name" class="panel-input">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Logo</label>
                <input type="hidden" :name="'items['+index+'][existing_image]'" :value="item.image || ''">
                <div class="flex gap-2 items-center">
                    <input type="file" :name="'items['+index+'][image]'" accept="image/*" class="panel-input flex-1">
                    <button type="button" class="text-xs text-red-600 px-2" @click="remove(index)" x-show="items.length > 1">Remove</button>
                </div>
                <p class="text-xs text-slate-500 truncate" x-text="item.image ? ('Current: ' + item.image) : ''"></p>
            </div>
        </div>
    </template>
    <button type="button" class="panel-btn-secondary" @click="add({name:'', image:''})">+ Add partner</button>
</div>

@include('platform.website-content.partials._repeater-script')
