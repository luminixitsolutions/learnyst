<div class="space-y-4">
    {{-- Curriculum toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <input type="search" x-model="lessonSearch" placeholder="Search lesson..."
               class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/50 focus:outline-none max-w-xs">
        <div class="flex gap-2">
            <button type="button" @click="sectionModal = true" class="px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm text-slate-700 hover:bg-slate-50">
                + Add Section
            </button>
        </div>
    </div>

    <div id="sections-sortable" class="space-y-4">
        @forelse($course->sections as $section)
        <div data-section-id="{{ $section->id }}" class="glass-card rounded-2xl overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 bg-slate-50 border-b border-slate-200">
                <button type="button" class="section-drag-handle cursor-grab text-slate-400 hover:text-slate-600" title="Drag to reorder">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                </button>
                <button type="button" @click="toggleSection({{ $section->id }})" class="flex-1 text-left">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500 transition" :class="isExpanded({{ $section->id }}) && 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <h4 class="font-semibold text-slate-800">{{ $section->title }}</h4>
                        <x-badge :type="$section->status === 'active' ? 'success' : 'default'">{{ ucfirst($section->status ?? 'active') }}</x-badge>
                        <span class="text-xs text-slate-400">{{ $section->lessons->count() }} lessons</span>
                    </div>
                    @if($section->description)<p class="text-xs text-slate-500 mt-1 ml-6">{{ $section->description }}</p>@endif
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="openLessonModal({{ $section->id }})" class="text-xs px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">+ Add Lesson</button>
                    <form method="POST" action="{{ route('admin.sections.destroy', $section) }}">@csrf @method('DELETE')
                        <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-500 hover:text-red-700">Delete</button>
                    </form>
                </div>
            </div>

            <div x-show="isExpanded({{ $section->id }})" x-cloak class="p-4">
                <div class="lessons-sortable space-y-2 min-h-[40px]" data-section-id="{{ $section->id }}">
                    @foreach($section->lessons as $lesson)
                    <div data-lesson-id="{{ $lesson->id }}"
                         x-show="matchesSearch('{{ addslashes($lesson->title) }}')"
                         class="flex items-center gap-3 py-2.5 px-3 rounded-xl bg-white border border-slate-200 hover:border-indigo-200 transition">
                        <button type="button" class="lesson-drag-handle cursor-grab text-slate-300 hover:text-slate-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM7 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4zM13 14a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/></svg>
                        </button>
                        <x-badge type="info">{{ $lesson->typeLabel() }}</x-badge>
                        <a href="{{ route('admin.lessons.edit', $lesson) }}" class="flex-1 text-sm text-slate-700 hover:text-indigo-600 font-medium">{{ $lesson->title }}</a>
                        <x-badge :type="$lesson->status === 'published' ? 'success' : 'warning'">{{ ucfirst($lesson->status ?? 'draft') }}</x-badge>
                        @if($lesson->is_preview)<x-badge type="warning">Preview</x-badge>@endif
                        <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}">@csrf @method('DELETE')
                            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-400 hover:text-red-600">Remove</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @if($section->lessons->isEmpty())
                <p class="text-sm text-slate-400 text-center py-4">No lessons yet. Click "Add Lesson" to create one.</p>
                @endif
            </div>
        </div>
        @empty
        <div class="glass-card rounded-2xl p-12 text-center">
            <p class="text-slate-500 mb-4">No sections yet. Start building your curriculum.</p>
            <button type="button" @click="sectionModal = true" class="px-5 py-2.5 rounded-xl panel-btn-primary text-sm">+ Add Section</button>
        </div>
        @endforelse
    </div>
</div>
