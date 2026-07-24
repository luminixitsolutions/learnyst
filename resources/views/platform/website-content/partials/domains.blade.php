@php
    $items = old('items', $content['items'] ?? [['slug'=>'','title'=>'','desc'=>'','type'=>'product','icon'=>'']]);
@endphp

<div class="space-y-4">
    <x-form-input label="Section title" name="title" :value="old('title', $content['title'] ?? '')" required />
    <x-form-input label="Section text" name="text" type="textarea" :value="old('text', $content['text'] ?? '')" />

    <div x-data="websiteRepeater(@js($items))" class="space-y-4">
        <h3 class="text-sm font-semibold text-slate-800">Domain cards</h3>
        <template x-for="(item, index) in items" :key="index">
            <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                <div class="flex justify-between">
                    <h4 class="text-sm font-semibold text-slate-700" x-text="'Domain ' + (index + 1)"></h4>
                    <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Title">
                    <input type="text" :name="'items['+index+'][slug]'" x-model="item.slug" class="panel-input" placeholder="Slug">
                    <input type="text" :name="'items['+index+'][desc]'" x-model="item.desc" class="panel-input md:col-span-2" placeholder="Description">
                    <select :name="'items['+index+'][type]'" x-model="item.type" class="panel-select">
                        <option value="product">Product</option>
                        <option value="solution">Solution</option>
                    </select>
                    <input type="text" :name="'items['+index+'][icon]'" x-model="item.icon" class="panel-input" placeholder="fa-graduation-cap">
                </div>
            </div>
        </template>
        <button type="button" class="panel-btn-secondary" @click="add({slug:'',title:'',desc:'',type:'product',icon:''})">+ Add domain</button>
    </div>
</div>

@include('platform.website-content.partials._repeater-script')
