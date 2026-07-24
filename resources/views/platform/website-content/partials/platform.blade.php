@php
    $items = old('items', $content['items'] ?? [['slug'=>'','title'=>'','desc'=>'','bg'=>'','image'=>'']]);
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-form-input label="Green heading word" name="heading_green" :value="old('heading_green', $content['heading_green'] ?? '')" />
    <x-form-input label="Blue heading word" name="heading_blue" :value="old('heading_blue', $content['heading_blue'] ?? '')" />
</div>
<x-form-input label="Rest of heading" name="heading_rest" :value="old('heading_rest', $content['heading_rest'] ?? '')" />
<x-form-input label="Subheading" name="subheading" type="textarea" :value="old('subheading', $content['subheading'] ?? '')" />

<div x-data="websiteRepeater(@js($items))" class="space-y-4 pt-2">
    <h3 class="text-sm font-semibold text-slate-800">Platform cards</h3>
    <template x-for="(item, index) in items" :key="index">
        <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
            <div class="flex justify-between">
                <h4 class="text-sm font-semibold text-slate-700" x-text="'Card ' + (index + 1)"></h4>
                <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input type="text" :name="'items['+index+'][title]'" x-model="item.title" class="panel-input" placeholder="Title">
                <input type="text" :name="'items['+index+'][slug]'" x-model="item.slug" class="panel-input" placeholder="Product slug">
                <input type="text" :name="'items['+index+'][desc]'" x-model="item.desc" class="panel-input md:col-span-2" placeholder="Description">
                <input type="text" :name="'items['+index+'][bg]'" x-model="item.bg" class="panel-input md:col-span-2" placeholder="CSS background">
                <div class="md:col-span-2 space-y-1">
                    <input type="hidden" :name="'items['+index+'][existing_image]'" :value="item.image || ''">
                    <input type="file" :name="'items['+index+'][image]'" accept="image/*" class="panel-input">
                    <p class="text-xs text-slate-500 truncate" x-text="item.image ? ('Current: ' + item.image) : ''"></p>
                </div>
            </div>
        </div>
    </template>
    <button type="button" class="panel-btn-secondary" @click="add({slug:'',title:'',desc:'',bg:'',image:''})">+ Add card</button>
</div>

@include('platform.website-content.partials._repeater-script')
