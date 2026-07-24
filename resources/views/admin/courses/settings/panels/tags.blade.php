@php
    $selected = collect(old('tags', $selectedTagIds ?? []))->map(fn ($id) => (int) $id)->all();
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6"
      x-data="{ selected: @js($selected) }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-600">Select tags that describe this course.</p>
        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">
            <span x-text="selected.length"></span> selected
        </span>
    </div>

    @if($tags->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">
            No tags available yet. Create one below.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($tags as $tag)
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 cursor-pointer hover:border-emerald-300 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50/50">
                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                           @checked(in_array($tag->id, $selected, true))
                           @change="selected = [...$el.form.querySelectorAll('input[name=\'tags[]\']:checked')].map(i => parseInt(i.value)); markDirty()">
                    <span class="text-sm text-slate-800">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    @endif
    @error('tags')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    @error('tags.*')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Create new tag</label>
        <input type="text" name="new_tag" value="{{ old('new_tag') }}" placeholder="Enter tag name"
               class="w-full max-w-md rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
        @error('new_tag')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
