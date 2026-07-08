<div x-show="showSettings" x-cloak
     class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl z-50 overflow-y-auto border-l border-slate-200">
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800">Lesson Settings</h3>
            <button type="button" @click="showSettings = false" class="p-2 rounded-lg text-slate-400 hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.lessons.settings.update', $lesson) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <x-form-input label="Lesson Name" name="title" :value="$lesson->title" required />
            <x-form-input label="Status" name="status" type="select" :value="$lesson->status ?? 'draft'">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </x-form-input>
            <x-form-input label="Preview Allowed" name="is_preview" type="select" :value="$lesson->is_preview ? '1' : '0'">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </x-form-input>
            <x-form-input label="Lock Lesson" name="is_locked" type="select" :value="$lesson->is_locked ? '1' : '0'">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </x-form-input>
            <x-form-input label="Drip Content" name="drip_enabled" type="select" :value="$lesson->drip_enabled ? '1' : '0'">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </x-form-input>
            <x-form-input label="Drip Date" name="drip_date" type="date" :value="$lesson->drip_date?->format('Y-m-d')" />
            <x-form-input label="Completion Required" name="completion_required" type="select" :value="$lesson->completion_required ? '1' : '0'">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </x-form-input>
            <x-form-input label="Allow Download" name="allow_download" type="select" :value="$lesson->allow_download ? '1' : '0'">
                <option value="1">Yes</option>
                <option value="0">No</option>
            </x-form-input>
            <x-form-input label="Sort Order" name="sort_order" type="number" :value="$lesson->sort_order" />
            <button type="submit" class="w-full px-4 py-2.5 rounded-xl panel-btn-primary text-sm">Save</button>
        </form>
    </div>
</div>
