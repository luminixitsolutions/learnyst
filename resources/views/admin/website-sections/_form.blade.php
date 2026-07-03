@php $section = $websiteSection ?? null; @endphp
<x-form-input label="Section Name" name="name" :value="$section?->name" required />
<x-form-input label="Heading" name="heading" :value="$section?->heading" />
<x-form-input label="Sub Heading" name="sub_heading" :value="$section?->sub_heading" />
<x-form-input label="Description" name="description" type="textarea" :value="$section?->description" />
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <x-form-input label="Button Text" name="button_text" :value="$section?->button_text" />
    <x-form-input label="Button Link" name="button_link" :value="$section?->button_link" placeholder="/courses or https://" />
    <x-form-input label="Section Order" name="sort_order" type="number" :value="$section?->sort_order ?? 0" />
</div>
<div class="space-y-1.5">
    <label class="block text-sm font-medium text-slate-700">Upload Image</label>
    @if($section?->image)
        <img src="{{ Storage::url($section->image) }}" alt="" class="w-32 h-20 object-cover rounded-lg mb-2">
    @endif
    <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white">
</div>
<label class="flex items-center gap-3">
    <input type="hidden" name="is_visible" value="0">
    <input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $section?->is_visible ?? true)) class="rounded border-slate-300 text-indigo-600">
    <span class="text-sm text-slate-700">Show section on website</span>
</label>
