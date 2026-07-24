@php
    $assignedIds = collect(old('instructor_ids', $assigned->pluck('id')->all()))->map(fn ($id) => (int) $id)->all();
    $primaryFromAssigned = $assigned->first(fn ($instructor) => (bool) ($instructor->pivot->is_primary ?? false));
    $primaryId = (int) old('primary_instructor_id', $primaryFromAssigned?->id ?? ($assignedIds[0] ?? 0));
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-6"
      x-data="{
          selected: @js($assignedIds),
          primary: {{ $primaryId ?: 'null' }},
          toggle(id, checked) {
              id = parseInt(id);
              if (checked) {
                  if (!this.selected.includes(id)) this.selected.push(id);
                  if (!this.primary) this.primary = id;
              } else {
                  this.selected = this.selected.filter(i => i !== id);
                  if (this.primary === id) this.primary = this.selected[0] || null;
              }
              markDirty();
          }
      }"
      @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    @if($instructors->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">
            No instructors available. Create instructors first, then assign them here.
        </div>
    @else
        <div class="space-y-3">
            @foreach($instructors as $instructor)
                <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 px-4 py-3"
                     :class="selected.includes({{ $instructor->id }}) ? 'border-emerald-300 bg-emerald-50/40' : ''">
                    <label class="flex items-center gap-3 min-w-0 cursor-pointer">
                        <input type="checkbox" name="instructor_ids[]" value="{{ $instructor->id }}"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                               @checked(in_array($instructor->id, $assignedIds, true))
                               @change="toggle({{ $instructor->id }}, $event.target.checked)">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $instructor->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $instructor->email }}</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-slate-600 shrink-0"
                           x-show="selected.includes({{ $instructor->id }})">
                        <input type="radio" name="primary_instructor_id" value="{{ $instructor->id }}"
                               class="text-emerald-600 focus:ring-emerald-500"
                               x-model.number="primary"
                               @checked($primaryId === $instructor->id)>
                        Primary
                    </label>
                </div>
            @endforeach
        </div>
    @endif
    @error('instructor_ids')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
    @error('primary_instructor_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror

    <p class="text-xs text-slate-500">Primary instructor is shown first on the course page and certificate credits.</p>

    <div class="sticky bottom-0 -mx-6 md:-mx-8 mt-10 px-6 md:px-8 py-4 border-t border-slate-200 bg-white flex items-center justify-end gap-3 rounded-b-2xl">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           @click.prevent="requestLeave('{{ route('admin.courses.settings.hub', $course) }}')"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" :disabled="!dirty"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:cursor-not-allowed"
                :class="dirty ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-slate-100 text-slate-400'">Save</button>
    </div>
</form>
