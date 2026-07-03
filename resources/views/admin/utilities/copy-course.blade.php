@extends('layouts.app')

@section('title', 'Copy Course')
@section('page-title', 'Copy Course')
@section('breadcrumb', 'Utilities / Copy Product / Copy Course')

@section('content')
<div class="pb-28" x-data="copyCourseForm(@js($courses))">
    <div class="space-y-8 max-w-3xl">
        <div>
            <a href="{{ route('admin.utilities.copy-product') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Copy Product
            </a>
            <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mt-3">
                Utilities <span class="mx-1">/</span> Copy Product <span class="mx-1">/</span>
                <span class="text-emerald-600">Copy Course</span>
            </p>
        </div>

        <div>
            <h3 class="text-2xl font-bold text-slate-900">Copy Course</h3>
            <p class="text-sm text-slate-500 mt-2">Select course you want to copy</p>
        </div>

        <div class="glass-card rounded-2xl p-6 space-y-8">
            <div>
                <label class="block text-sm font-bold text-slate-900 mb-3">Select Course</label>
                <div class="flex gap-3">
                    <input type="text"
                           readonly
                           :value="selectedCourse ? selectedCourse.title : ''"
                           placeholder="Select a course to copy"
                           class="panel-input flex-1 cursor-default bg-white">
                    <button type="button"
                            @click="courseModalOpen = true; courseSearch = ''"
                            class="shrink-0 px-5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                        Select
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-900 mb-3">Select Sections</label>
                <div class="flex gap-3">
                    <input type="text"
                           readonly
                           :value="sectionsLabel"
                           placeholder="Select sections to copy"
                           :class="selectedCourse ? 'panel-input flex-1 cursor-default bg-white' : 'panel-input flex-1 cursor-not-allowed bg-slate-50 text-slate-400'"
                           :disabled="!selectedCourse">
                    <button type="button"
                            @click="openSectionModal()"
                            :disabled="!selectedCourse"
                            :class="selectedCourse
                                ? 'shrink-0 px-5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 hover:bg-slate-100 transition'
                                : 'shrink-0 px-5 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-sm font-medium text-slate-300 cursor-not-allowed'">
                        Select
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Course picker modal --}}
    <div x-show="courseModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="courseModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/40" @click="courseModalOpen = false"></div>
        <div class="relative w-full max-w-lg glass-card rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-base font-bold text-slate-900">Select Course</h4>
                <button type="button" @click="courseModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 border-b border-slate-100">
                <input type="text"
                       x-model="courseSearch"
                       placeholder="Search courses..."
                       class="panel-input">
            </div>
            <div class="max-h-80 overflow-y-auto">
                <template x-for="course in filteredCourses" :key="course.id">
                    <button type="button"
                            @click="selectCourse(course)"
                            class="w-full text-left px-6 py-3.5 text-sm text-slate-700 hover:bg-emerald-50 border-b border-slate-50 last:border-0 transition"
                            :class="selectedCourse?.id === course.id ? 'bg-emerald-50 text-emerald-700 font-medium' : ''">
                        <span x-text="course.title"></span>
                        <span class="text-xs text-slate-400 ml-2" x-text="`${course.sections.length} section(s)`"></span>
                    </button>
                </template>
                <p x-show="filteredCourses.length === 0" class="px-6 py-8 text-sm text-slate-500 text-center">No courses found.</p>
            </div>
        </div>
    </div>

    {{-- Section picker modal --}}
    <div x-show="sectionModalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="sectionModalOpen = false">
        <div class="absolute inset-0 bg-slate-900/40" @click="sectionModalOpen = false"></div>
        <div class="relative w-full max-w-lg glass-card rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-base font-bold text-slate-900">Select Sections</h4>
                <button type="button" @click="sectionModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-3 border-b border-slate-100 flex items-center justify-between">
                <button type="button"
                        @click="toggleAllSections()"
                        class="text-sm font-medium text-emerald-600 hover:text-emerald-700">
                    <span x-text="allSectionsSelected ? 'Deselect all' : 'Select all'"></span>
                </button>
                <span class="text-xs text-slate-400" x-text="`${selectedSectionIds.length} selected`"></span>
            </div>
            <div class="max-h-80 overflow-y-auto">
                <template x-for="section in courseSections" :key="section.id">
                    <label class="flex items-center gap-3 px-6 py-3.5 border-b border-slate-50 last:border-0 cursor-pointer hover:bg-slate-50">
                        <input type="checkbox"
                               :value="section.id"
                               x-model="selectedSectionIds"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-slate-700" x-text="section.title"></span>
                    </label>
                </template>
                <p x-show="courseSections.length === 0" class="px-6 py-8 text-sm text-slate-500 text-center">This course has no sections.</p>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button type="button"
                        @click="sectionModalOpen = false"
                        class="px-5 py-2.5 rounded-xl panel-btn-primary transition">
                    Done
                </button>
            </div>
        </div>
    </div>

    {{-- Footer action bar --}}
    <div class="fixed bottom-0 left-0 right-0 lg:left-72 z-40 bg-white border-t border-slate-200 shadow-[0_-4px_24px_rgba(15,23,42,0.06)]">
        <div class="max-w-3xl px-4 sm:px-6 lg:px-8 py-4 flex items-center gap-3">
            <button type="button"
                    :disabled="!canProceed"
                    :class="canProceed
                        ? 'px-8 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition'
                        : 'px-8 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-300 cursor-not-allowed'">
                Next
            </button>
            <a href="{{ route('admin.utilities.copy-product') }}"
               class="px-6 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Cancel
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyCourseForm(courses) {
    return {
        courses,
        selectedCourse: null,
        selectedSectionIds: [],
        courseModalOpen: false,
        sectionModalOpen: false,
        courseSearch: '',

        get filteredCourses() {
            const query = this.courseSearch.trim().toLowerCase();
            if (!query) {
                return this.courses;
            }
            return this.courses.filter(course => course.title.toLowerCase().includes(query));
        },

        get courseSections() {
            if (!this.selectedCourse) {
                return [];
            }
            const course = this.courses.find(item => item.id === this.selectedCourse.id);
            return course?.sections ?? [];
        },

        get sectionsLabel() {
            if (!this.selectedCourse || this.selectedSectionIds.length === 0) {
                return '';
            }
            const titles = this.courseSections
                .filter(section => this.selectedSectionIds.includes(section.id))
                .map(section => section.title);
            return titles.join(', ');
        },

        get allSectionsSelected() {
            return this.courseSections.length > 0
                && this.selectedSectionIds.length === this.courseSections.length;
        },

        get canProceed() {
            return this.selectedCourse && this.selectedSectionIds.length > 0;
        },

        selectCourse(course) {
            this.selectedCourse = course;
            this.selectedSectionIds = [];
            this.courseModalOpen = false;
        },

        openSectionModal() {
            if (!this.selectedCourse) {
                return;
            }
            this.sectionModalOpen = true;
        },

        toggleAllSections() {
            if (this.allSectionsSelected) {
                this.selectedSectionIds = [];
                return;
            }
            this.selectedSectionIds = this.courseSections.map(section => section.id);
        },
    };
}
</script>
@endpush
