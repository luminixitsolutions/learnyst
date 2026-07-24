@php
    $items = old('items', $content['items'] ?? [['title'=>'','tag'=>'','date'=>'','read'=>'']]);
@endphp

<div class="space-y-4">
    <x-form-input label="Section title" name="title" :value="old('title', $content['title'] ?? '')" required />
    <x-form-input label="Section text" name="text" type="textarea" :value="old('text', $content['text'] ?? '')" />

    <div x-data="websiteRepeater(@js($items))" class="space-y-4">
        <h3 class="text-sm font-semibold text-slate-800">Stories</h3>
        <template x-for="(item, index) in items" :key="index">
            <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                <div class="flex justify-between">
                    <h4 class="text-sm font-semibold text-slate-700" x-text="'Story ' + (index + 1)"></h4>
                    <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                </div>
                <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Title">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input type="text" :name="'items['+index+'][tag]'" x-model="item.tag" class="panel-input" placeholder="Tag">
                    <input type="text" :name="'items['+index+'][date]'" x-model="item.date" class="panel-input" placeholder="Date">
                    <input type="text" :name="'items['+index+'][read]'" x-model="item.read" class="panel-input" placeholder="5 min read">
                </div>
            </div>
        </template>
        <button type="button" class="panel-btn-secondary" @click="add({title:'',tag:'',date:'',read:''})">+ Add story</button>
    </div>
</div>

@include('platform.website-content.partials._repeater-script')
