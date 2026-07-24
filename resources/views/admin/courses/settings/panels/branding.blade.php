@php
    $branding = $settings->branding ?? [];
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" enctype="multipart/form-data" class="mt-8 space-y-6" @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Course title <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $course->title) }}" required
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('title')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Subtitle</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $course->subtitle) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('subtitle')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Description</label>
            <textarea name="description" rows="5"
                      class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">{{ old('description', $course->description) }}</textarea>
            @error('description')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Category</label>
            <select name="category_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $course->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Language</label>
            <input type="text" name="language" value="{{ old('language', $branding['language'] ?? '') }}" placeholder="e.g. English"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('language')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Difficulty</label>
            <select name="difficulty" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                <option value="">Select difficulty</option>
                @foreach(['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('difficulty', $branding['difficulty'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('difficulty')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Estimated duration</label>
            <input type="text" name="estimated_duration" value="{{ old('estimated_duration', $branding['estimated_duration'] ?? '') }}" placeholder="e.g. 8 hours"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('estimated_duration')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Intro video URL</label>
            <input type="url" name="intro_video_url" value="{{ old('intro_video_url', $course->intro_video_url) }}" placeholder="https://"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('intro_video_url')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Accent color</label>
            <div class="flex items-center gap-3">
                <input type="color" name="accent_color" value="{{ old('accent_color', $branding['accent_color'] ?? '#10b981') }}"
                       class="h-10 w-14 rounded-lg border border-slate-200 cursor-pointer">
                <input type="text" value="{{ old('accent_color', $branding['accent_color'] ?? '#10b981') }}" readonly
                       class="flex-1 rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm bg-slate-50 text-slate-600">
            </div>
            @error('accent_color')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Featured course</p>
                <p class="text-xs text-slate-500">Highlight this course on discovery pages</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" class="sr-only peer" @checked(old('is_featured', $branding['is_featured'] ?? false))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 peer-focus:ring-2 peer-focus:ring-emerald-200 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>
    </div>

    <div class="space-y-3" x-data="{ preview: '{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : '' }}', remove: false }">
        <label class="block text-sm font-medium text-slate-700">Course thumbnail</label>
        <p class="text-xs text-slate-500">Upload image with resolution of 1024 x 576 pixels.</p>

        <div x-show="preview && !remove" class="relative w-full max-w-md aspect-video rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
            <img :src="preview" alt="Thumbnail preview" class="w-full h-full object-cover">
            <button type="button" @click="remove = true; markDirty()" class="absolute top-2 right-2 px-2.5 py-1 rounded-lg bg-white/90 text-xs font-medium text-rose-600 border border-slate-200">Remove</button>
        </div>

        <input type="hidden" name="remove_thumbnail" :value="remove ? 1 : 0">
        <input type="file" name="thumbnail" accept="image/*"
               @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview; remove = false; markDirty()"
               class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:text-emerald-700 file:font-medium hover:file:bg-emerald-100">
        @error('thumbnail')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
