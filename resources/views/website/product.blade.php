@extends('website.layouts.app')

@section('title', ($page['title'] ?? 'Learnyst') . ' – ' . config('website.brand'))
@section('meta_description', $page['summary'] ?? '')

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
    $pageType = $type ?? 'product';
    $relatedRoute = $relatedRoute ?? 'website.product';
    $isSolution = $pageType === 'solution';
@endphp

<section class="ly-product-hero" style="background: {{ $page['hero_gradient'] ?? 'linear-gradient(135deg, #0b1220 0%, #163663 50%, #15803d 120%)' }};">
    <div class="ly-product-hero-mesh" aria-hidden="true"></div>
    <div class="ly-container ly-product-hero-inner">
        <div class="ly-product-hero-copy">
            @if(!empty($page['eyebrow']))
                <p class="ly-product-eyebrow">{{ $page['eyebrow'] }}</p>
            @endif
            <h1>{{ $page['title'] }}</h1>
            <p class="ly-product-lead">{{ $page['summary'] }}</p>
            <div class="ly-hero-actions">
                <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">{{ $page['cta_primary_label'] ?? 'Start Free Trial' }}</a>
                <a class="ly-btn ly-btn-outline" href="{{ url($cta['demo'] ?? '/product-demo') }}">{{ $page['cta_secondary_label'] ?? 'Book a Demo' }}</a>
            </div>
        </div>
        @if(!empty($page['hero_image_url']))
            <div class="ly-product-hero-visual">
                <img src="{{ $page['hero_image_url'] }}" alt="{{ $page['title'] }}">
            </div>
        @endif
    </div>
</section>

<section class="ly-section">
    <div class="ly-container">
        <div class="ly-split">
            <div class="ly-split-copy">
                <p class="ly-tag">{{ $page['caption'] ?? ($isSolution ? 'Solutions' : 'Products') }}</p>
                <h3>{{ $isSolution ? 'Built for your niche academy' : 'Why educators choose this' }}</h3>
                <p>{{ $page['body'] }}</p>
                @if(!empty($page['features']))
                    <ul class="ly-checklist">
                        @foreach($page['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="ly-hero-actions">
                    <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">{{ $page['cta_primary_label'] ?? 'Start Free Trial' }}</a>
                    <a class="ly-btn ly-btn-outline-dark" href="{{ url($cta['demo'] ?? '/product-demo') }}">{{ $page['cta_secondary_label'] ?? 'Book a Demo' }}</a>
                </div>
            </div>
            <div class="ly-split-media ly-product-panel">
                @if(!empty($page['hero_image_url']))
                    <img src="{{ $page['hero_image_url'] }}" alt="">
                @else
                    <div class="ly-product-panel-fallback">
                        <strong>{{ $brand }}</strong>
                        <span>{{ $page['title'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@if(!empty($page['benefits']))
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $isSolution ? 'Everything your students need' : 'Built for real academy workflows' }}</h2>
            <p>
                @if($isSolution)
                    A complete stack to teach, test, engage, and sell {{ $page['title'] }} programs under your brand.
                @else
                    Everything you need to deliver {{ strtolower($page['title']) }} with less friction and more polish.
                @endif
            </p>
        </div>
        <div class="ly-grid ly-grid-2 ly-product-benefits">
            @foreach($page['benefits'] as $benefit)
                <div class="ly-product-benefit">
                    <div class="ly-card-icon"><i class="fa {{ $benefit['icon'] ?? 'fa-star' }}"></i></div>
                    <div>
                        <h3>{{ $benefit['title'] }}</h3>
                        <p>{{ $benefit['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($page['stats']))
<section class="learnyst-stats-banner">
    <div class="ly-container">
        <div class="learnyst-stats-grid">
            @foreach($page['stats'] as $stat)
                <div class="learnyst-stat-item">
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($page['use_cases']))
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>Who it’s perfect for</h2>
            <p>
                @if($isSolution)
                    From specialist tutors to full institutes — adapt this solution to how you teach.
                @else
                    From first launch to scaled academies — adapt this product to your teaching model.
                @endif
            </p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach($page['use_cases'] as $useCase)
                <div class="ly-card">
                    <h3>{{ $useCase['title'] }}</h3>
                    <p>{{ $useCase['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($page['faq']))
<section class="ly-section ly-section-soft">
    <div class="ly-container ly-product-faq-wrap">
        <div class="ly-section-head">
            <h2>Frequently asked questions</h2>
            <p>Quick answers before you start your trial.</p>
        </div>
        <div class="ly-product-faq">
            @foreach($page['faq'] as $item)
                <details class="ly-product-faq-item">
                    <summary>{{ $item['question'] }}</summary>
                    <p>{{ $item['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($related))
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $isSolution ? 'Explore related solutions' : 'Explore related products' }}</h2>
            <p>
                @if($isSolution)
                    Discover more verticals educators grow successfully on Learnyst.
                @else
                    Build a complete teaching and sales stack under one brand.
                @endif
            </p>
        </div>
        <div class="ly-grid ly-grid-2">
            @foreach($related as $relatedSlug => $relatedItem)
                <a class="ly-product-related" href="{{ route($relatedRoute, $relatedSlug) }}">
                    <span class="ly-product-related-icon"><i class="fa {{ $relatedItem['icon'] ?? 'fa-cube' }}"></i></span>
                    <span>
                        <strong>{{ $relatedItem['menu'] ?? $relatedItem['title'] }}</strong>
                        <em>{{ $relatedItem['menu_desc'] ?? ($relatedItem['summary'] ?? '') }}</em>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="ly-cta-banner">
    <div class="ly-container">
        <h2>{{ $page['cta_title'] ?? 'Ready to get started?' }}</h2>
        <p>{{ $page['cta_text'] ?? 'Start your free trial and launch your academy in minutes.' }}</p>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">{{ $page['cta_primary_label'] ?? 'Start Free Trial' }}</a>
            <a class="ly-btn ly-btn-outline" href="{{ url($cta['demo'] ?? '/product-demo') }}">{{ $page['cta_secondary_label'] ?? 'Book a Demo' }}</a>
        </div>
    </div>
</section>
@endsection
