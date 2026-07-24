@extends('website.layouts.app')

@section('title', 'Courses – '.config('website.brand'))
@section('meta_description', 'Browse all published courses by category on Learnyst. Find free, paid, and trial programs from trusted institutes.')

@section('content')
@php $brand = config('website.brand'); @endphp

<section class="ly-course-hero ly-courses-index-hero">
    <div class="ly-course-hero-glow" aria-hidden="true"></div>
    <div class="ly-container ly-course-hero-inner">
        <p class="ly-product-eyebrow">Course catalog</p>
        <h1>Explore courses</h1>
        <p class="ly-courses-index-lead">
            {{ number_format($totalCourses) }} published {{ Str::plural('course', $totalCourses) }} across
            {{ $allCategories->count() }} {{ Str::plural('category', $allCategories->count()) }}.
        </p>
    </div>
</section>

<section class="ly-section ly-courses-index">
    <div class="ly-container">
        @if($allCategories->count())
            <div class="ly-courses-filter" role="navigation" aria-label="Course categories">
                <a href="{{ route('public.courses') }}" class="ly-courses-filter-chip {{ $selectedCategory === '' ? 'is-active' : '' }}">
                    All categories
                </a>
                @foreach($allCategories as $filterCategory)
                    <a
                        href="{{ route('public.courses', ['category' => $filterCategory->slug]) }}"
                        class="ly-courses-filter-chip {{ $selectedCategory === $filterCategory->slug ? 'is-active' : '' }}"
                    >
                        {{ $filterCategory->name }}
                    </a>
                @endforeach
            </div>
        @endif

        @forelse($categories as $category)
            <div class="ly-courses-category-block" id="category-{{ $category->slug }}">
                <div class="ly-courses-category-head">
                    <div>
                        <p class="ly-tag">{{ $category->name }}</p>
                        <h2>{{ $category->courses->count() }} {{ Str::plural('course', $category->courses->count()) }}</h2>
                        @if($category->description)
                            <p>{{ $category->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="ly-company-course-grid">
                    @foreach($category->courses as $course)
                        @include('public.partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            </div>
        @empty
            @if($uncategorized->isEmpty())
                <div class="ly-empty-state">
                    <h3>No courses found</h3>
                    <p>Published courses will appear here by category.</p>
                </div>
            @endif
        @endforelse

        @if($uncategorized->isNotEmpty() && $selectedCategory === '')
            <div class="ly-courses-category-block" id="category-other">
                <div class="ly-courses-category-head">
                    <div>
                        <p class="ly-tag">Other</p>
                        <h2>{{ $uncategorized->count() }} {{ Str::plural('course', $uncategorized->count()) }}</h2>
                        <p>Courses without a specific category.</p>
                    </div>
                </div>
                <div class="ly-company-course-grid">
                    @foreach($uncategorized as $course)
                        @include('public.partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
