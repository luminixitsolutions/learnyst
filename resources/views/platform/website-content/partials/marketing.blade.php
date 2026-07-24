@php
    $imageUrl = \App\Models\WebsiteContent::mediaUrl($content['image'] ?? null);
@endphp
<div class="space-y-4">
    <x-form-input label="Title" name="title" :value="old('title', $content['title'] ?? '')" required />
    <x-form-input label="Description" name="text" type="textarea" :value="old('text', $content['text'] ?? '')" />
    <x-form-input label="Bullets (one per line)" name="bullets" type="textarea" :value="old('bullets', $content['bullets'] ?? '')" />
    <div class="space-y-1.5">
        <label class="block text-sm font-semibold text-slate-700">Section image</label>
        <input type="file" name="image" accept="image/*" class="panel-input">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="" class="mt-2 h-28 rounded-lg object-cover border border-slate-200">
        @endif
    </div>
</div>
