@extends('layouts.app')

@section('title', 'Course Builder')
@section('page-title', 'Course Builder')
@section('breadcrumb', $course->title)

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush

@section('content')
<div class="space-y-6" x-data="courseBuilder()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses.index') }}" class="p-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50" title="Back">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $course->title }}</h2>
                <p class="text-sm text-slate-500">{{ $course->sections->count() }} sections · {{ $course->lessons()->count() }} lessons</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <x-badge :type="match($course->status) { 'published' => 'success', 'draft' => 'warning', default => 'default' }">{{ ucfirst($course->status) }}</x-badge>
            @if($course->status !== 'published')
            <form method="POST" action="{{ route('admin.courses.publish', $course) }}">@csrf
                <button type="submit" class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Publish Course</button>
            </form>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-slate-200">
        <nav class="flex gap-6">
            <a href="{{ route('admin.courses.builder', $course) }}"
               class="pb-3 text-sm font-semibold border-b-2 transition {{ $tab === 'curriculum' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Curriculum
            </a>
            <a href="{{ route('admin.courses.builder', ['course' => $course, 'tab' => 'settings']) }}"
               class="pb-3 text-sm font-semibold border-b-2 transition {{ $tab === 'settings' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Settings
            </a>
            <a href="{{ route('admin.courses.builder', ['course' => $course, 'tab' => 'learners']) }}"
               class="pb-3 text-sm font-semibold border-b-2 transition {{ $tab === 'learners' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                Learners
            </a>
        </nav>
    </div>

    @if($tab === 'settings')
        @include('admin.courses.partials.builder-settings')
    @elseif($tab === 'learners')
        @include('admin.courses.partials.builder-learners')
    @else
        @include('admin.courses.partials.builder-curriculum')
    @endif

    @if($tab === 'curriculum')
        @include('admin.courses.partials.add-lesson-drawer')
    @endif

    {{-- Add Section Modal --}}
    <div x-show="sectionModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div @click.outside="sectionModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Add Section</h3>
            <form method="POST" action="{{ route('admin.courses.sections.store', $course) }}">
                @csrf
                <div class="space-y-4">
                    <x-form-input label="Section Title" name="title" required />
                    <x-form-input label="Description" name="description" type="textarea" />
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input label="Sort Order" name="sort_order" type="number" placeholder="Auto" />
                        <x-form-input label="Status" name="status" type="select" value="active">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </x-form-input>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="sectionModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm text-slate-600">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl panel-btn-primary text-sm">Add Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function courseBuilder() {
    return {
        lessonModal: false,
        sectionModal: false,
        lessonFormAction: '',
        lessonTitle: '',
        selectedLessonType: 'video',
        lessonSearch: '',
        expandedSections: @json($course->sections->pluck('id')->toArray()),
        openLessonModal(sectionId) {
            this.lessonFormAction = '{{ url('company/sections') }}/' + sectionId + '/lessons';
            this.lessonTitle = '';
            this.selectedLessonType = 'video';
            this.lessonModal = true;
        },
        closeLessonModal() {
            this.lessonModal = false;
            this.lessonTitle = '';
            this.selectedLessonType = 'video';
        },
        validateLessonForm() {
            if (!this.lessonTitle.trim()) {
                alert('Please enter a lesson title.');
                return false;
            }
            if (!this.selectedLessonType) {
                alert('Please select a lesson type.');
                return false;
            }
            return true;
        },
        toggleSection(id) {
            const idx = this.expandedSections.indexOf(id);
            if (idx > -1) this.expandedSections.splice(idx, 1);
            else this.expandedSections.push(id);
        },
        isExpanded(id) {
            return this.expandedSections.includes(id);
        },
        matchesSearch(title) {
            if (!this.lessonSearch) return true;
            return title.toLowerCase().includes(this.lessonSearch.toLowerCase());
        },
        init() {
            const container = document.getElementById('sections-sortable');
            if (container && typeof Sortable !== 'undefined') {
                Sortable.create(container, {
                    handle: '.section-drag-handle',
                    animation: 150,
                    onEnd: () => {
                        const order = [...container.querySelectorAll('[data-section-id]')].map(el => parseInt(el.dataset.sectionId));
                        fetch('{{ route('admin.courses.sections.reorder', $course) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({ order })
                        });
                    }
                });
            }
            document.querySelectorAll('.lessons-sortable').forEach(el => {
                if (typeof Sortable !== 'undefined') {
                    Sortable.create(el, {
                        handle: '.lesson-drag-handle',
                        animation: 150,
                        group: 'lessons',
                        onEnd: () => {
                            const sectionId = el.dataset.sectionId;
                            const order = [...el.querySelectorAll('[data-lesson-id]')].map(item => parseInt(item.dataset.lessonId));
                            fetch('{{ url('company/sections') }}/' + sectionId + '/lessons/reorder', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                                body: JSON.stringify({ order })
                            });
                        }
                    });
                }
            });
        }
    }
}
</script>
@endpush
@endsection
