@extends('website.layouts.app')

@section('title', 'Institutes – ' . config('website.brand'))
@section('meta_description', 'Discover institutes building branded learning businesses on StudyNest.')

@section('content')
@php $brand = config('website.brand'); @endphp

<section class="ly-product-hero ly-customer-hero" style="background: linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%);">
    <div class="ly-product-hero-mesh" aria-hidden="true"></div>
    <div class="ly-container ly-customer-hero-inner">
        <p class="ly-product-eyebrow">Institute Directory</p>
        <h1>Institutes growing on {{ $brand }}</h1>
        <p class="ly-product-lead">Explore registered institutes. Open a profile to see their courses and brand story.</p>
        <form method="GET" action="{{ route('website.companies.index') }}" class="ly-company-search">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name, tagline, or city" class="ly-company-search-input">
            <button type="submit" class="ly-btn ly-btn-green">Search</button>
        </form>
    </div>
</section>

<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Institutes</p>
            <h2>{{ $companies->total() }} registered {{ Str::plural('institute', $companies->total()) }}</h2>
            <p>Every public profile is managed by the institute from their StudyNest panel.</p>
        </div>

        @if($companies->count())
            <div class="ly-company-grid">
                @foreach($companies as $company)
                    <a href="{{ route('website.companies.show', $company->slug) }}" class="ly-company-card">
                        <div class="ly-company-card-cover" @if($company->coverUrl()) style="background-image:url('{{ $company->coverUrl() }}')" @endif></div>
                        <div class="ly-company-card-body">
                            <div class="ly-company-avatar">
                                @if($company->logoUrl())
                                    <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}">
                                @else
                                    <span>{{ $company->initials() }}</span>
                                @endif
                            </div>
                            <h3>{{ $company->name }}</h3>
                            @php
                                $instituteRating = round((float) ($company->institute_avg_rating ?? 0), 1);
                                $instituteReviews = (int) ($company->reviews_count ?? 0);
                                $courseRating = round((float) ($company->course_avg_rating ?? 0), 1);
                                $courseReviews = (int) ($company->course_reviews_count ?? 0);
                                $isFeatured = $company->slug === 'luminix-it-solution';
                                if ($isFeatured && $instituteRating < 5) {
                                    $instituteRating = 5.0;
                                }
                            @endphp
                            <div class="ly-company-rating">
                                <span class="ly-company-stars" aria-label="{{ $instituteRating }} out of 5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa {{ $i <= round($instituteRating) ? 'fa-star' : 'fa-star-o' }}"></i>
                                    @endfor
                                </span>
                                <strong>{{ $instituteRating > 0 ? number_format($instituteRating, 1) : 'New' }}</strong>
                                <span class="ly-company-rating-count">
                                    {{ $instituteReviews > 0 ? $instituteReviews.' '.Str::plural('review', $instituteReviews) : 'No reviews yet' }}
                                </span>
                            </div>
                            @if($company->tagline)
                                <p class="ly-company-tagline">{{ $company->tagline }}</p>
                            @elseif($company->about)
                                <p class="ly-company-tagline">{{ Str::limit($company->about, 110) }}</p>
                            @endif
                            <div class="ly-company-meta">
                                @if($company->city)<span><i class="fa fa-map-marker"></i> {{ $company->city }}</span>@endif
                                <span><i class="fa fa-book"></i> {{ $company->courses_count }} {{ Str::plural('course', $company->courses_count) }}</span>
                                @if($courseReviews > 0)
                                    <span><i class="fa fa-graduation-cap"></i> Courses {{ number_format($courseRating, 1) }} ({{ $courseReviews }})</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="ly-center" style="margin-top:36px;">
                {{ $companies->links() }}
            </div>
        @else
            <div class="ly-empty-state">
                <h3>No public institutes yet</h3>
                <p>When institutes publish their profile, they will appear here.</p>
                <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Start Free Trial</a>
            </div>
        @endif
    </div>
</section>
@endsection
