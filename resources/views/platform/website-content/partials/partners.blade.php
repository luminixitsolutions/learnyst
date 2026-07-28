@php
    use App\Models\WebsiteContent;

    $items = old('items', $content['items'] ?? [['name' => '', 'image' => '']]);
    $items = collect($items)->map(function ($item) {
        if (! is_array($item)) {
            return $item;
        }
        $item['image_url'] = WebsiteContent::mediaUrl($item['image'] ?? null);

        return $item;
    })->values()->all();
@endphp

<div x-data="websitePartnerRepeater(@js($items))" class="space-y-4">
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
                    <input type="file" :name="'items['+index+'][image]'" accept="image/*" class="panel-input flex-1" @change="previewImage($event, index)">
                    <button type="button" class="text-xs text-red-600 px-2 shrink-0" @click="remove(index)" x-show="items.length > 1">Remove</button>
                </div>
                <template x-if="item.preview_url || item.image_url">
                    <div class="mt-2 space-y-1">
                        <div class="flex h-24 w-full max-w-xs items-center justify-center rounded-lg border border-slate-200 bg-white p-3 shadow-sm">
                            <img :src="item.preview_url || item.image_url"
                                 :alt="item.name ? item.name + ' logo' : 'Partner logo'"
                                 class="max-h-full max-w-full object-contain">
                        </div>
                        <p class="text-xs text-slate-500 truncate" x-show="item.image && !item.preview_url" x-text="'Current: ' + item.image"></p>
                        <p class="text-xs text-emerald-600" x-show="item.preview_url">New logo selected — save to apply.</p>
                    </div>
                </template>
            </div>
        </div>
    </template>
    <button type="button" class="panel-btn-secondary" @click="add({name:'', image:''})">+ Add partner</button>
</div>

@include('platform.website-content.partials._repeater-script')

<script>
function websitePartnerRepeater(initial) {
    return {
        items: Array.isArray(initial) && initial.length ? initial : [{ name: '', image: '' }],
        add(row) { this.items.push(row || {}); },
        remove(index) { if (this.items.length > 1) this.items.splice(index, 1); },
        previewImage(event, index) {
            const file = event.target.files?.[0];
            if (!file) {
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                this.items[index].preview_url = e.target.result;
            };
            reader.readAsDataURL(file);
        },
    };
}
</script>
