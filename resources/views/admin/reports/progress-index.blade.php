@extends('layouts.app')

@section('title', 'Product Progress Reports')
@section('page-title', 'Product Progress Reports')
@section('breadcrumb', 'Reports / Product Progress')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-indigo-600">← All Reports</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            ['ebook', 'Ebook Progress'],
            ['podcast', 'Podcast Progress'],
            ['custom-product', 'Custom Product Progress'],
            ['webinar', 'Webinar Progress'],
            ['digital-evaluation', 'Digital Evaluation'],
            ['course-quiz', 'Course Quiz Scores'],
            ['mock-test', 'Mock Test Scores'],
            ['test-series', 'Test Series Scores'],
            ['bundle-quiz', 'Bundle Quiz Score'],
            ['code-submission', 'Code Submission Area'],
            ['quiz-insights', 'Quiz Insights'],
        ] as [$type, $title])
        <a href="{{ route('admin.reports.progress.type', $type) }}" class="glass-card rounded-2xl p-6 hover:border-indigo-400/30 transition group">
            <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600">{{ $title }}</h3>
            <span class="inline-block mt-4 text-sm text-indigo-600">View report →</span>
        </a>
        @endforeach
    </div>
</div>
@endsection
