@extends('layouts.app')

@section('title', 'Edit Quiz')
@section('page-title', 'Edit Quiz')
@section('breadcrumb', 'Quizzes / Edit')

@section('content')
@php
    $quizData = $quiz->quiz_data ?? [];
    $selectedCourseId = old('course_id', $quiz->section?->course_id);
    $selectedSectionId = old('section_id', $quiz->course_section_id);
    $existingQuestions = collect($quizData['questions'] ?? [])->map(function ($q) {
        if (is_string($q)) {
            return ['question' => $q, 'options' => ['', '', '', ''], 'correct' => 0, 'marks' => 1];
        }
        $options = array_values((array) ($q['options'] ?? ['', '', '', '']));
        while (count($options) < 4) {
            $options[] = '';
        }

        return [
            'question' => $q['question'] ?? $q['text'] ?? '',
            'options' => array_slice($options, 0, 4),
            'correct' => (int) ($q['correct'] ?? 0),
            'marks' => $q['marks'] ?? 1,
        ];
    })->values()->all();
@endphp
<div class="max-w-3xl"
     x-data="quizForm({
        courseId: '{{ $selectedCourseId }}',
        sections: @js($courses->mapWithKeys(fn($c) => [$c->id => $c->sections->map(fn($s) => ['id' => $s->id, 'title' => $s->title])])),
        questions: @js($existingQuestions),
        aiUrl: @js(route('admin.quizzes.ai-analyze')),
        csrf: @js(csrf_token()),
     })">
    <div class="glass-card rounded-2xl p-6" id="quiz-form-fields">
        <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-4">
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1 space-y-1.5">
                        <label for="title" class="block text-sm font-semibold text-slate-700">Quiz Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" x-model="title" value="{{ old('title', $quiz->title) }}" required
                               placeholder="e.g. Laravel Basics Midterm"
                               class="panel-input w-full">
                        @error('title')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="button" @click="runAi()" :disabled="aiLoading"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shrink-0 disabled:opacity-60 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span x-text="aiLoading ? 'Analyzing…' : 'AI Fill Details'"></span>
                    </button>
                </div>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="ai_brief" class="block text-xs font-medium text-slate-600">Optional notes for AI</label>
                        <input type="text" id="ai_brief" x-model="aiBrief" class="panel-input w-full text-sm" placeholder="e.g. Beginner, cover routing & Eloquent">
                    </div>
                    <div class="space-y-1.5">
                        <label for="ai_question_count" class="block text-xs font-medium text-slate-600">Questions to generate</label>
                        <input type="number" id="ai_question_count" x-model.number="questionCount" min="3" max="15" class="panel-input w-full text-sm">
                    </div>
                </div>
                <p class="mt-2 text-xs" :class="aiError ? 'text-rose-600' : 'text-emerald-700'" x-show="aiStatus" x-text="aiStatus" x-cloak></p>
            </div>

            <div class="space-y-1.5">
                <label for="course_id" class="block text-sm font-semibold text-slate-700">Course <span class="text-red-500">*</span></label>
                <select name="course_id" id="course_id" required class="panel-select w-full" x-model="courseId">
                    <option value="">Select course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected((string) $selectedCourseId === (string) $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('course_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700">Lesson / Section</label>
                <select name="section_id" required class="panel-input">
                    <option value="">Select section</option>
                    <template x-for="section in sections[courseId] || []" :key="section.id">
                        <option :value="section.id" x-text="section.title" :selected="section.id == '{{ $selectedSectionId }}'"></option>
                    </template>
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="total_marks" class="block text-sm font-semibold text-slate-700">Total Marks</label>
                    <input type="number" name="total_marks" id="total_marks" step="0.01" x-model="totalMarks" class="panel-input">
                </div>
                <div class="space-y-1.5">
                    <label for="passing_marks" class="block text-sm font-semibold text-slate-700">Passing Marks</label>
                    <input type="number" name="passing_marks" id="passing_marks" step="0.01" x-model="passingMarks" class="panel-input">
                </div>
                <div class="space-y-1.5">
                    <label for="time_limit" class="block text-sm font-semibold text-slate-700">Time Limit (min)</label>
                    <input type="number" name="time_limit" id="time_limit" x-model="timeLimit" class="panel-input">
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-800">Questions <span class="font-normal text-slate-500" x-text="'(' + questions.length + ')'"></span></h3>
                    <button type="button" @click="addQuestion()" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">+ Add question</button>
                </div>

                <template x-for="(q, qi) in questions" :key="qi">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold text-slate-500" x-text="'Question ' + (qi + 1)"></p>
                            <button type="button" @click="removeQuestion(qi)" class="text-xs text-rose-600 hover:text-rose-800">Remove</button>
                        </div>
                        <textarea :name="'questions[' + qi + '][question]'" x-model="q.question" rows="2" class="panel-input" placeholder="Question text"></textarea>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <template x-for="(opt, oi) in q.options" :key="oi">
                                <div class="flex items-center gap-2">
                                    <input type="radio" :name="'questions[' + qi + '][correct]'" :value="oi" x-model.number="q.correct" class="text-indigo-600">
                                    <input type="text" :name="'questions[' + qi + '][options][]'" x-model="q.options[oi]" class="panel-input text-sm" :placeholder="'Option ' + String.fromCharCode(65 + oi)">
                                </div>
                            </template>
                        </div>
                        <div class="w-28">
                            <label class="block text-xs text-slate-500 mb-1">Marks</label>
                            <input type="number" step="0.5" :name="'questions[' + qi + '][marks]'" x-model.number="q.marks" class="panel-input text-sm">
                        </div>
                    </div>
                </template>

                <p class="text-xs text-slate-500" x-show="questions.length === 0">No questions yet. Use AI Fill Details or add questions manually.</p>
                <p class="text-xs text-slate-500">Tip: you can also manage this quiz from the <a href="{{ route('admin.lessons.edit', $quiz) }}" class="font-semibold text-indigo-600 hover:text-indigo-800">lesson editor</a>.</p>
            </div>

            <div class="flex justify-between pt-4 border-t">
                <a href="{{ route('admin.quizzes.index') }}" class="text-sm text-slate-500">Cancel</a>
                <button type="submit" class="panel-btn-primary">Save Quiz</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quizForm(config) {
    return {
        courseId: config.courseId || '',
        sections: config.sections || {},
        title: @js(old('title', $quiz->title)),
        aiBrief: '',
        questionCount: 5,
        totalMarks: @js(old('total_marks', $quizData['total_marks'] ?? '')),
        passingMarks: @js(old('passing_marks', $quizData['passing_marks'] ?? '')),
        timeLimit: @js(old('time_limit', $quizData['time_limit'] ?? '')),
        questions: config.questions || [],
        aiUrl: config.aiUrl,
        csrf: config.csrf,
        aiLoading: false,
        aiStatus: '',
        aiError: false,
        emptyQuestion() {
            return { question: '', options: ['', '', '', ''], correct: 0, marks: 1 };
        },
        addQuestion() {
            this.questions.push(this.emptyQuestion());
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        },
        async runAi() {
            const title = (this.title || '').trim();
            if (!title) {
                this.aiError = true;
                this.aiStatus = 'Enter a quiz title first, then click AI Fill Details.';
                return;
            }

            this.aiLoading = true;
            this.aiError = false;
            this.aiStatus = 'AI is generating marks, time limit & MCQ questions…';

            try {
                const res = await fetch(this.aiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        title: title,
                        brief: (this.aiBrief || '').trim() || null,
                        course_id: this.courseId || null,
                        question_count: this.questionCount || 5,
                    }),
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json.ok) {
                    throw new Error(json.message || json.errors?.title?.[0] || json.errors?.ai?.[0] || 'AI request failed.');
                }

                const d = json.data || {};
                this.totalMarks = d.total_marks ?? '';
                this.passingMarks = d.passing_marks ?? '';
                this.timeLimit = d.time_limit ?? '';
                this.questions = (d.questions || []).map((q) => ({
                    question: q.question || '',
                    options: (q.options && q.options.length ? q.options.slice(0, 4) : ['', '', '', '']),
                    correct: Number.isInteger(q.correct) ? q.correct : 0,
                    marks: q.marks ?? 1,
                }));
                this.questions.forEach((q) => {
                    while (q.options.length < 4) q.options.push('');
                });

                this.aiError = false;
                this.aiStatus = json.message || 'Details filled. Review questions and save the quiz.';
            } catch (err) {
                this.aiError = true;
                this.aiStatus = err.message || 'Something went wrong.';
            } finally {
                this.aiLoading = false;
            }
        },
    };
}
</script>
@endpush
