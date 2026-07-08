{{-- Right-side Add Lesson/Quiz drawer --}}
<div x-show="lessonModal" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="closeLessonModal()">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/30" @click="closeLessonModal()"></div>

    {{-- Drawer panel --}}
    <div x-show="lessonModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col">

        <div class="flex-1 overflow-y-auto px-6 py-6">
            <h2 class="text-2xl font-bold text-slate-900">Add Lesson/Quiz</h2>
            <p class="text-sm text-slate-500 mt-1 mb-6">Start creating Lesson/Quiz, select lesson type and add lesson title.</p>

            <form method="POST" :action="lessonFormAction" @submit="return validateLessonForm()">
                @csrf
                <input type="hidden" name="lesson_type" :value="selectedLessonType">

                {{-- Lesson Title --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-semibold text-slate-800">Lesson Title</label>
                        <span class="text-xs text-slate-400" x-text="lessonTitle.length + '/60'"></span>
                    </div>
                    <input type="text" name="title" x-model="lessonTitle" maxlength="60" required
                           placeholder="Enter Lesson Title"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400">
                </div>

                {{-- Type grid --}}
                <div class="mb-6">
                    <label class="text-sm font-semibold text-slate-800 mb-3 block">Select Lesson/Quiz Type</label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([
                            ['value' => 'video', 'label' => 'Video', 'border' => 'border-emerald-400', 'bg' => 'bg-emerald-50', 'icon_bg' => 'text-emerald-500', 'icon' => 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['value' => 'audio', 'label' => 'Audio', 'border' => 'border-orange-400', 'bg' => 'bg-orange-50', 'icon_bg' => 'text-orange-500', 'icon' => 'M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z'],
                            ['value' => 'pdf', 'label' => 'PDF', 'border' => 'border-red-400', 'bg' => 'bg-red-50', 'icon_bg' => 'text-red-500', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                            ['value' => 'slides', 'label' => 'Slides', 'border' => 'border-amber-400', 'bg' => 'bg-amber-50', 'icon_bg' => 'text-amber-500', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                            ['value' => 'live_class', 'label' => 'Live', 'border' => 'border-pink-400', 'bg' => 'bg-pink-50', 'icon_bg' => 'text-pink-500', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                            ['value' => 'text', 'label' => 'Article', 'border' => 'border-slate-400', 'bg' => 'bg-slate-50', 'icon_bg' => 'text-slate-500', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
                            ['value' => 'external_link', 'label' => 'SCORM/Tincan', 'border' => 'border-blue-400', 'bg' => 'bg-blue-50', 'icon_bg' => 'text-blue-500', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                            ['value' => 'quiz', 'label' => 'Quiz', 'border' => 'border-purple-400', 'bg' => 'bg-purple-50', 'icon_bg' => 'text-purple-500', 'icon' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['value' => 'assignment', 'label' => 'Assignment', 'border' => 'border-indigo-400', 'bg' => 'bg-indigo-50', 'icon_bg' => 'text-indigo-500', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                        ] as $type)
                        <button type="button"
                                @click="selectedLessonType = '{{ $type['value'] }}'"
                                :class="selectedLessonType === '{{ $type['value'] }}' ? '{{ $type['border'] }} {{ $type['bg'] }} ring-1' : 'border-slate-200 hover:border-slate-300'"
                                class="flex flex-col items-center justify-center gap-2 p-4 rounded-lg border-2 transition-all min-h-[90px]">
                            <svg class="w-7 h-7 {{ $type['icon_bg'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $type['icon'] }}"/>
                            </svg>
                            <span class="text-xs font-medium text-slate-700">{{ $type['label'] }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Footer buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            :disabled="!lessonTitle.trim() || !selectedLessonType"
                            class="px-6 py-2.5 rounded-lg bg-slate-900 text-white text-sm font-medium hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed">
                        Continue
                    </button>
                    <button type="button" @click="closeLessonModal()"
                            class="px-6 py-2.5 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
