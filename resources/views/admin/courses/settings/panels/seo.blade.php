@php
    $seo = $settings->seo ?? [];
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" enctype="multipart/form-data" class="mt-8 space-y-6"
      x-data="{
          seoTitle: @js(old('seo_title', $course->seo_title ?? $course->title)),
          seoDescription: @js(old('seo_description', $course->seo_description ?? \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 160, ''))),
          slug: @js(old('slug', $course->slug))
      }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-1">
        <p class="text-xs text-emerald-700">{{ url('/courses') }}/<span x-text="slug || 'course-slug'"></span></p>
        <p class="text-lg text-blue-700 font-medium leading-snug" x-text="(seoTitle || 'Page title').slice(0, 70)"></p>
        <p class="text-sm text-slate-600" x-text="(seoDescription || 'Meta description preview will appear here.').slice(0, 160)"></p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">URL slug <span class="text-rose-500">*</span></label>
            <input type="text" name="slug" x-model="slug" required
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('slug')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-sm font-medium text-slate-700">SEO title</label>
                <span class="text-xs text-slate-400"><span x-text="seoTitle.length"></span>/70</span>
            </div>
            <input type="text" name="seo_title" x-model="seoTitle" maxlength="70"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('seo_title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-sm font-medium text-slate-700">SEO description</label>
                <span class="text-xs text-slate-400"><span x-text="seoDescription.length"></span>/160</span>
            </div>
            <textarea name="seo_description" x-model="seoDescription" maxlength="160" rows="3"
                      class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400"></textarea>
            @error('seo_description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Meta keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $seo['meta_keywords'] ?? '') }}" placeholder="Comma-separated keywords"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('meta_keywords')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Canonical URL</label>
            <input type="url" name="canonical_url" value="{{ old('canonical_url', $seo['canonical_url'] ?? '') }}" placeholder="https://"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('canonical_url')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Open Graph title</label>
            <input type="text" name="og_title" value="{{ old('og_title', $seo['og_title'] ?? '') }}" maxlength="70"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('og_title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Open Graph description</label>
            <input type="text" name="og_description" value="{{ old('og_description', $seo['og_description'] ?? '') }}" maxlength="200"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('og_description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Open Graph image</label>
            @if(!empty($seo['og_image']))
                <div class="mb-3 w-48 aspect-video rounded-lg overflow-hidden border border-slate-200">
                    <img src="{{ asset('storage/'.$seo['og_image']) }}" alt="OG image" class="w-full h-full object-cover">
                </div>
            @endif
            <input type="file" name="og_image" accept="image/*"
                   class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-medium">
            @error('og_image')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Allow indexing</p>
                <p class="text-xs text-slate-500">robots index</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="robots_index" value="1" class="sr-only peer" @checked(old('robots_index', $seo['robots_index'] ?? true))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>

        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Follow links</p>
                <p class="text-xs text-slate-500">robots follow</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="robots_follow" value="1" class="sr-only peer" @checked(old('robots_follow', $seo['robots_follow'] ?? true))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>
    </div>

    <div class="sticky bottom-0 -mx-6 md:-mx-8 mt-10 px-6 md:px-8 py-4 border-t border-slate-200 bg-white flex items-center justify-end gap-3 rounded-b-2xl">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           @click.prevent="requestLeave('{{ route('admin.courses.settings.hub', $course) }}')"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" :disabled="!dirty"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:cursor-not-allowed"
                :class="dirty ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-slate-100 text-slate-400'">Save</button>
    </div>
</form>
