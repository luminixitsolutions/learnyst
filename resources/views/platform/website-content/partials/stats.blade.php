@php
    $items = old('items', $content['items'] ?? [['value' => '', 'label' => '']]);
@endphp

<div x-data="websiteRepeater(@js($items))" class="space-y-4">
    <template x-for="(item, index) in items" :key="index">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 rounded-xl border border-slate-200 p-4 bg-slate-50/60">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Value</label>
                <input type="text" :name="'items['+index+'][value]'" x-model="item.value" class="panel-input" placeholder="1100 Cr+">
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Label</label>
                <div class="flex gap-2">
                    <input type="text" :name="'items['+index+'][label]'" x-model="item.label" class="panel-input flex-1" placeholder="Earned by Educators">
                    <button type="button" class="text-xs text-red-600 px-2" @click="remove(index)" x-show="items.length > 1">Remove</button>
                </div>
            </div>
        </div>
    </template>
    <button type="button" class="panel-btn-secondary" @click="add({value:'', label:''})">+ Add stat</button>
</div>

@include('platform.website-content.partials._repeater-script')
