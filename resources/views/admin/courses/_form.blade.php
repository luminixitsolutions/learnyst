@php $course = $course ?? null; @endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <x-form-input label="Course Title" name="title" :value="$course?->title" required />
    <x-form-input label="Course Subtitle" name="subtitle" :value="$course?->subtitle" placeholder="Short subtitle for landing page" />
    <x-form-input label="Course Category" name="category_id" type="select" :value="$course?->category_id">
        <option value="">— Select Category —</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $course?->category_id) == $cat->id)>{{ $cat->name }}</option>
        @endforeach
    </x-form-input>
    <x-form-input label="Product Type" name="product_type" type="select" :value="$course?->product_type ?? ($productType ?? 'course')" required>
        @foreach(['course','ebook','podcast','webinar','custom','free_resource'] as $type)
            <option value="{{ $type }}" @selected(old('product_type', $course?->product_type ?? ($productType ?? 'course')) === $type)>{{ ucfirst(str_replace('_',' ', $type)) }}</option>
        @endforeach
    </x-form-input>
    <x-form-input label="Access Type" name="access_type" type="select" :value="$course?->access_type ?? 'paid'" required>
        @foreach(['free','trial','paid'] as $access)
            <option value="{{ $access }}" @selected(old('access_type', $course?->access_type ?? 'paid') === $access)>{{ ucfirst($access) }}</option>
        @endforeach
    </x-form-input>
    <x-form-input label="Course Price (₹)" name="price" type="number" step="0.01" :value="$course?->price ?? 0" />
    <x-form-input label="Sale Price (₹)" name="sale_price" type="number" step="0.01" :value="$course?->sale_price" placeholder="Optional discounted price" />
    <x-form-input label="Course Validity (days)" name="validity_days" type="number" :value="$course?->validity_days" placeholder="Access duration after enrollment" />
    <x-form-input label="Course Status" name="status" type="select" :value="$course?->status ?? 'draft'" required>
        @foreach(['draft','published','unpublished'] as $st)
            <option value="{{ $st }}" @selected(old('status', $course?->status ?? 'draft') === $st)>{{ ucfirst($st) }}</option>
        @endforeach
    </x-form-input>
    <x-form-input label="Start Date" name="start_date" type="date" :value="$course?->start_date?->format('Y-m-d')" />
    <x-form-input label="Expiry Date" name="expiry_date" type="date" :value="$course?->expiry_date?->format('Y-m-d')" />
</div>

@if(isset($instructors))
<div class="mt-5 space-y-1.5">
    <label class="block text-sm font-medium text-slate-700">Instructor(s)</label>
    <select name="instructor_ids[]" multiple size="4" class="panel-input">
        @foreach($instructors as $instructor)
            <option value="{{ $instructor->id }}" @selected(in_array($instructor->id, old('instructor_ids', $course?->instructors?->pluck('id')->toArray() ?? [])))>{{ $instructor->name }}</option>
        @endforeach
    </select>
    <p class="text-xs text-slate-500">Hold Ctrl/Cmd to select multiple instructors</p>
</div>
@endif

<div class="mt-5">
    <x-form-input label="Course Description" name="description" type="textarea" :value="$course?->description" />
</div>

<div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="space-y-1.5">
        <label class="block text-sm font-medium text-slate-700">Course Thumbnail</label>
        @if($course?->thumbnailUrl())
            <img src="{{ $course->thumbnailUrl() }}" alt="" class="w-32 h-20 object-cover rounded-lg mb-2">
        @endif
        <input type="file" name="thumbnail" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-900 file:text-white">
        @error('thumbnail')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <x-form-input label="Course Intro Video URL" name="intro_video_url" :value="$course?->intro_video_url" placeholder="https://youtube.com/..." />
</div>

<div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
    <x-form-input label="SEO Title" name="seo_title" :value="$course?->seo_title" />
    <x-form-input label="SEO Description" name="seo_description" type="textarea" :value="$course?->seo_description" />
</div>

<div class="mt-5 flex flex-wrap gap-6">
    <label class="flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="is_free" value="0">
        <input type="checkbox" name="is_free" value="1" @checked(old('is_free', $course?->is_free ?? false)) class="rounded border-slate-300 text-indigo-600">
        <span class="text-sm text-slate-700">Free Course</span>
    </label>
    <label class="flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="drip_enabled" value="0">
        <input type="checkbox" name="drip_enabled" value="1" @checked(old('drip_enabled', $course?->drip_enabled ?? false)) class="rounded border-slate-300 text-indigo-600">
        <span class="text-sm text-slate-700">Enable Drip Content</span>
    </label>
</div>

@if(isset($tags))
<div class="mt-5 space-y-1.5">
    <label class="block text-sm font-medium text-slate-700">Tags</label>
    <div class="flex flex-wrap gap-2">
        @foreach($tags as $tag)
            <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer hover:border-indigo-300">
                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', $course?->tags?->pluck('id')->toArray() ?? []))) class="rounded border-slate-300 text-indigo-600">
                <span class="text-sm text-slate-700">{{ $tag->name }}</span>
            </label>
        @endforeach
    </div>
</div>
@endif
