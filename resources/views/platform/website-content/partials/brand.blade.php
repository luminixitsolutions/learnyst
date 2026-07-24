<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-form-input label="Brand name" name="name" :value="old('name', $content['name'] ?? '')" required />
    <x-form-input label="Tagline" name="tagline" :value="old('tagline', $content['tagline'] ?? '')" />
    <x-form-input label="Email" name="email" type="email" :value="old('email', $content['email'] ?? '')" />
    <x-form-input label="Phone" name="phone" :value="old('phone', $content['phone'] ?? '')" />
</div>
<x-form-input label="Address (one line per row)" name="address" type="textarea" :value="old('address', $content['address'] ?? '')" />
