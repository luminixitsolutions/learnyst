@php
    $perms = $settings->permissions ?? [];
    $platforms = old('selling_platforms', $settings->selling_platforms ?? ['all']);
    if (! is_array($platforms)) {
        $platforms = ['all'];
    }
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6"
      x-data="{ platforms: @js(array_values($platforms)) }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
        <div>
            <p class="text-sm font-medium text-slate-800">Sell independently</p>
            <p class="text-xs text-slate-500">Allow learners to purchase this course on its own</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="sell_independently" value="1" class="sr-only peer" @checked(old('sell_independently', $settings->sell_independently ?? true))>
            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Access visibility</label>
        <select name="access_visibility" class="w-full max-w-md rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @foreach([
                'public' => 'Public',
                'private' => 'Private',
                'unlisted' => 'Unlisted',
                'invitation' => 'Invitation only',
                'membership' => 'Membership',
                'organization' => 'Organization',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('access_visibility', $settings->access_visibility ?? 'public') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('access_visibility')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Selling platforms</label>
        <p class="text-xs text-slate-500 mb-3">Selecting <strong>All</strong> covers every platform and disables individual options.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach(['all' => 'All', 'web' => 'Web', 'android' => 'Android', 'ios' => 'iOS'] as $value => $label)
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm"
                       :class="{
                           'opacity-40 pointer-events-none': platforms.includes('all') && '{{ $value }}' !== 'all',
                           'border-emerald-300 bg-emerald-50/50': platforms.includes('{{ $value }}')
                       }">
                    <input type="checkbox" name="selling_platforms[]" value="{{ $value }}"
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                           :checked="platforms.includes('{{ $value }}')"
                           @change="
                               if ('{{ $value }}' === 'all') {
                                   platforms = $event.target.checked ? ['all'] : [];
                               } else {
                                   platforms = platforms.filter(p => p !== 'all');
                                   if ($event.target.checked) platforms.push('{{ $value }}');
                                   else platforms = platforms.filter(p => p !== '{{ $value }}');
                               }
                               markDirty();
                           ">
                    {{ $label }}
                </label>
            @endforeach
        </div>
        @error('selling_platforms')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach([
            'allow_guest_preview' => ['Guest preview', 'Let guests preview limited content'],
            'allow_manual_enrollment' => ['Manual enrollment', 'Admins can enroll learners manually'],
            'allow_instructor_enrollment' => ['Instructor enrollment', 'Instructors can enroll learners'],
            'allow_batch_enrollment' => ['Batch enrollment', 'Allow enrollment via batches'],
            'offline_sync' => ['Offline sync', 'Allow content sync for offline apps'],
        ] as $field => [$title, $help])
            <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $title }}</p>
                    <p class="text-xs text-slate-500">{{ $help }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer"
                           @checked(old($field, $perms[$field] ?? in_array($field, ['allow_manual_enrollment', 'allow_batch_enrollment'], true)))>
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>
        @endforeach
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Max active sessions</label>
        <input type="number" name="max_active_sessions" min="1" max="20"
               value="{{ old('max_active_sessions', $perms['max_active_sessions'] ?? '') }}"
               placeholder="Optional"
               class="w-full max-w-xs rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
        @error('max_active_sessions')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
