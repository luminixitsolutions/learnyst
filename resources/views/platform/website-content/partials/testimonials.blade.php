@php
    $items = old('items', $content['items'] ?? [['quote'=>'','name'=>'','role'=>'']]);
@endphp

<div class="space-y-4">
    <x-form-input label="Section title" name="title" :value="old('title', $content['title'] ?? '')" required />
    <x-form-input label="Section text" name="text" type="textarea" :value="old('text', $content['text'] ?? '')" />

    <div x-data="websiteRepeater(@js($items))" class="space-y-4">
        <h3 class="text-sm font-semibold text-slate-800">Testimonials</h3>
        <template x-for="(item, index) in items" :key="index">
            <div class="rounded-xl border border-slate-200 p-4 space-y-3 bg-slate-50/60">
                <div class="flex justify-between">
                    <h4 class="text-sm font-semibold text-slate-700" x-text="'Quote ' + (index + 1)"></h4>
                    <button type="button" class="text-xs text-red-600" @click="remove(index)" x-show="items.length > 1">Remove</button>
                </div>
                <textarea :name="'items['+index+'][quote]'" x-model="item.quote" rows="3" class="panel-input" placeholder="Quote"></textarea>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" :name="'items['+index+'][name]'" x-model="item.name" class="panel-input" placeholder="Name">
                    <input type="text" :name="'items['+index+'][role]'" x-model="item.role" class="panel-input" placeholder="Role / Company">
                </div>
            </div>
        </template>
        <button type="button" class="panel-btn-secondary" @click="add({quote:'',name:'',role:''})">+ Add testimonial</button>
    </div>
</div>

@include('platform.website-content.partials._repeater-script')
