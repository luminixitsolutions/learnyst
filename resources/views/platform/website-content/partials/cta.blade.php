<div class="space-y-4 max-w-2xl">
    <x-form-input label="Title" name="title" :value="old('title', $content['title'] ?? '')" required />
    <x-form-input label="Description" name="text" type="textarea" :value="old('text', $content['text'] ?? '')" />
</div>
