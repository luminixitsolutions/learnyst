@php $course = $course ?? null; @endphp
<div class="space-y-5" id="course-form-fields"
     data-ai-url="{{ route('admin.courses.ai-analyze') }}"
     data-csrf="{{ csrf_token() }}">
    <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">
        <div class="flex flex-col md:flex-row md:items-end gap-3">
            <div class="flex-1 space-y-1.5">
                <label for="title" class="block text-sm font-semibold text-slate-700">Course Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $course?->title) }}" required
                       placeholder="e.g. Complete Laravel Mastery"
                       class="panel-input w-full">
                @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="button" id="ai-analyze-btn"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shrink-0 disabled:opacity-60 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span id="ai-analyze-label">AI Fill Details</span>
            </button>
        </div>
        <div class="mt-3 space-y-1.5">
            <label for="ai_brief" class="block text-xs font-medium text-slate-600">Optional notes for AI (audience, goals, level)</label>
            <input type="text" id="ai_brief" class="panel-input w-full text-sm" placeholder="e.g. Beginners, 8 weeks, job-oriented, Hindi+English">
        </div>
        <p id="ai-analyze-status" class="mt-2 text-xs text-slate-500 hidden"></p>
        <div id="ai-outline-preview" class="mt-3 hidden">
            <p class="text-xs font-semibold text-slate-600 mb-1">Suggested outline (for curriculum later)</p>
            <ul id="ai-outline-list" class="text-xs text-slate-600 list-disc pl-5 space-y-0.5"></ul>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
    <div class="space-y-1.5">
        <label class="block text-sm font-medium text-slate-700">Instructor(s)</label>
        <select name="instructor_ids[]" multiple size="4" class="panel-input">
            @foreach($instructors as $instructor)
                <option value="{{ $instructor->id }}" @selected(in_array($instructor->id, old('instructor_ids', $course?->instructors?->pluck('id')->toArray() ?? [])))>{{ $instructor->name }}</option>
            @endforeach
        </select>
        <p class="text-xs text-slate-500">Hold Ctrl/Cmd to select multiple instructors</p>
    </div>
    @endif

    <div>
        <x-form-input label="Course Description" name="description" type="textarea" :value="$course?->description" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <x-form-input label="SEO Title" name="seo_title" :value="$course?->seo_title" />
        <x-form-input label="SEO Description" name="seo_description" type="textarea" :value="$course?->seo_description" />
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_free" value="0">
            <input type="checkbox" name="is_free" id="is_free" value="1" @checked(old('is_free', $course?->is_free ?? false)) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Free Course</span>
        </label>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="drip_enabled" value="0">
            <input type="checkbox" name="drip_enabled" value="1" @checked(old('drip_enabled', $course?->drip_enabled ?? false)) class="rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Enable Drip Content</span>
        </label>
    </div>

    @if(isset($tags))
    <div class="space-y-1.5">
        <label class="block text-sm font-medium text-slate-700">Tags</label>
        <div class="flex flex-wrap gap-2" id="course-tags">
            @foreach($tags as $tag)
                <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 cursor-pointer hover:border-indigo-300">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" data-tag-id="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', $course?->tags?->pluck('id')->toArray() ?? []))) class="rounded border-slate-300 text-indigo-600">
                    <span class="text-sm text-slate-700">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
    @endif
</div>

@once
@push('scripts')
<script>
(function () {
    const root = document.getElementById('course-form-fields');
    if (!root) return;

    const btn = document.getElementById('ai-analyze-btn');
    const label = document.getElementById('ai-analyze-label');
    const statusEl = document.getElementById('ai-analyze-status');
    const outlineBox = document.getElementById('ai-outline-preview');
    const outlineList = document.getElementById('ai-outline-list');
    const titleInput = document.getElementById('title');
    const briefInput = document.getElementById('ai_brief');

    function setStatus(msg, isError) {
        statusEl.classList.remove('hidden');
        statusEl.textContent = msg;
        statusEl.className = 'mt-2 text-xs ' + (isError ? 'text-rose-600' : 'text-emerald-700');
    }

    function setValue(name, value) {
        const el = root.querySelector('[name="' + name + '"]');
        if (!el || value === undefined || value === null) return;
        if (el.tagName === 'SELECT' || el.tagName === 'TEXTAREA' || el.tagName === 'INPUT') {
            el.value = value;
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }

    btn?.addEventListener('click', async function () {
        const title = (titleInput?.value || '').trim();
        if (!title) {
            setStatus('Enter a course title first, then click AI Fill Details.', true);
            titleInput?.focus();
            return;
        }

        btn.disabled = true;
        label.textContent = 'Analyzing…';
        setStatus('AI is generating subtitle, description, SEO, pricing & category…', false);

        try {
            const res = await fetch(root.dataset.aiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': root.dataset.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    title: title,
                    brief: (briefInput?.value || '').trim() || null,
                }),
            });

            const json = await res.json().catch(() => ({}));
            if (!res.ok || !json.ok) {
                const msg = json.message || json.errors?.title?.[0] || json.errors?.ai?.[0] || 'AI request failed.';
                throw new Error(msg);
            }

            const d = json.data || {};
            setValue('subtitle', d.subtitle || '');
            setValue('description', d.description || '');
            setValue('seo_title', d.seo_title || '');
            setValue('seo_description', d.seo_description || '');
            setValue('product_type', d.product_type || 'course');
            setValue('access_type', d.access_type || 'paid');
            setValue('price', d.price ?? 0);
            setValue('sale_price', d.sale_price ?? '');
            setValue('validity_days', d.validity_days ?? '');
            if (d.category_id) setValue('category_id', d.category_id);

            const free = !!d.is_free;
            const freeBox = document.getElementById('is_free');
            if (freeBox) {
                freeBox.checked = free;
                if (free) setValue('access_type', 'free');
            }

            const tagIds = (d.tag_ids || []).map(String);
            root.querySelectorAll('input[name="tags[]"]').forEach(function (cb) {
                cb.checked = tagIds.includes(String(cb.value));
            });

            if (Array.isArray(d.suggested_outline) && d.suggested_outline.length) {
                outlineList.innerHTML = d.suggested_outline.map(function (item) {
                    return '<li>' + String(item).replace(/</g, '&lt;') + '</li>';
                }).join('');
                outlineBox.classList.remove('hidden');
            } else {
                outlineBox.classList.add('hidden');
            }

            setStatus(json.message || 'Details filled. Review and create the course.', false);
        } catch (err) {
            setStatus(err.message || 'Something went wrong.', true);
        } finally {
            btn.disabled = false;
            label.textContent = 'AI Fill Details';
        }
    });
})();
</script>
@endpush
@endonce
