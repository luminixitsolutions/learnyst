@php
    $config = $settings->drip_config ?? [];
    $modes = [
        'immediate' => 'Immediate — all content available on enrollment',
        'after_enrollment' => 'After enrollment — unlock based on enrollment date',
        'calendar' => 'Calendar — unlock on specific dates',
        'previous_completion' => 'Previous completion — unlock next after finishing previous',
        'days_after' => 'Days after — unlock N days after enrollment',
        'weekly' => 'Weekly drip',
        'monthly' => 'Monthly drip',
        'manual' => 'Manual unlock by admin/instructor',
    ];
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
    Schedule times are stored in UTC. Choose a timezone below for how drip dates are interpreted in the admin UI.
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6" @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Drip mode</label>
        <div class="space-y-2">
            @foreach($modes as $value => $label)
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/40">
                    <input type="radio" name="drip_mode" value="{{ $value }}" class="mt-0.5 text-emerald-600 focus:ring-emerald-500"
                           @checked(old('drip_mode', $settings->drip_mode ?? 'immediate') === $value)>
                    <span class="text-sm text-slate-800">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('drip_mode')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Days after enrollment</label>
            <input type="number" name="days_after_enrollment" min="0"
                   value="{{ old('days_after_enrollment', $config['days_after_enrollment'] ?? 0) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('days_after_enrollment')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Timezone</label>
            <input type="text" name="timezone" value="{{ old('timezone', $config['timezone'] ?? config('app.timezone')) }}"
                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
            @error('timezone')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Locked content message</label>
            <textarea name="locked_message" rows="3" maxlength="500"
                      class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">{{ old('locked_message', $config['locked_message'] ?? 'This lesson is locked. Complete previous content to unlock.') }}</textarea>
            @error('locked_message')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
