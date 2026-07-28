@extends('website.layouts.app')

@section('title', ($page['title'] ?? 'Customers') . ' – ' . config('website.brand'))
@section('meta_description', $page['summary'] ?? '')

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
    $items = $page['items'] ?? [];
    $featured = collect($items)->firstWhere('featured', true) ?? ($items[0] ?? null);
@endphp

<section class="ly-product-hero ly-customer-hero" style="background: {{ $page['hero_gradient'] ?? 'linear-gradient(135deg, #0b1220 0%, #163663 50%, #15803d 120%)' }};">
    <div class="ly-product-hero-mesh" aria-hidden="true"></div>
    <div class="ly-container ly-customer-hero-inner">
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
</section>

@if(!empty($page['stats']))
<section class="StudyNest-stats-banner">
    <div class="ly-container">
        <div class="StudyNest-stats-grid">
            @foreach($page['stats'] as $stat)
                <div class="StudyNest-stat-item">
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($slug === 'testimonials')
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Customers' }}</p>
            <h2>Trusted by educators building serious academies</h2>
            <p>{{ $page['body'] }}</p>
        </div>

        @if($featured)
            <figure class="ly-testimonial-featured">
                <div class="ly-testimonial-stars" aria-label="{{ ($featured['rating'] ?? 5) }} star rating">
                    @for($i = 0; $i < (int)($featured['rating'] ?? 5); $i++)
                        <i class="fa fa-star"></i>
                    @endfor
                </div>
                <blockquote>“{{ $featured['quote'] }}”</blockquote>
                <figcaption>
                    <span class="ly-testimonial-avatar">{{ strtoupper(substr($featured['name'] ?? 'L', 0, 1)) }}</span>
                    <span>
                        <strong>{{ $featured['name'] ?? '' }}</strong>
                        <em>{{ $featured['role'] ?? '' }}</em>
                        @if(!empty($featured['result']))
                            <small>{{ $featured['result'] }}</small>
                        @endif
                    </span>
                </figcaption>
            </figure>
        @endif

        <div class="ly-testimonial-grid">
            @foreach($items as $item)
                @if($featured && ($item['quote'] ?? '') === ($featured['quote'] ?? '') && ($item['name'] ?? '') === ($featured['name'] ?? ''))
                    @continue
                @endif
                <article class="ly-testimonial-card">
                    <div class="ly-testimonial-stars">
                        @for($i = 0; $i < (int)($item['rating'] ?? 5); $i++)
                            <i class="fa fa-star"></i>
                        @endfor
                    </div>
                    <p class="ly-testimonial-quote">“{{ $item['quote'] }}”</p>
                    @if(!empty($item['result']))
                        <p class="ly-testimonial-result">{{ $item['result'] }}</p>
                    @endif
                    <div class="ly-testimonial-person">
                        @php
                            $photo = $item['image'] ?? null;
                            $initial = strtoupper(substr($item['name'] ?? 'L', 0, 1));
                        @endphp
                        <span class="ly-testimonial-avatar" style="overflow:hidden;padding:0;">
                            @if($photo)
                                <img src="{{ $photo }}" alt="{{ $item['name'] }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy"
                                     onerror="this.style.display='none'; this.parentElement.textContent='{{ $initial }}';">
                            @else
                                {{ $initial }}
                            @endif
                        </span>
                        <span>
                            <strong>{{ $item['name'] ?? '' }}</strong>
                            <em>{{ $item['role'] ?? '' }}</em>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@elseif($slug === 'success-stories')
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Customers' }}</p>
            <h2>Growth stories from institutes like yours</h2>
            <p>{{ $page['body'] }}</p>
        </div>
        <div class="ly-story-grid">
            @foreach($items as $story)
                <article class="ly-story-card">
                    <div class="ly-story-card-top">
                        @if(!empty($story['tag']))
                            <span class="ly-tag">{{ $story['tag'] }}</span>
                        @endif
                        @if(!empty($story['metric']))
                            <div class="ly-story-metric">
                                <strong>{{ $story['metric'] }}</strong>
                                <span>{{ $story['metric_label'] ?? '' }}</span>
                            </div>
                        @endif
                    </div>
                    <h3>{{ $story['title'] }}</h3>
                    @if(!empty($story['summary']))
                        <p>{{ $story['summary'] }}</p>
                    @endif
                    <div class="ly-story-meta">
                        @if(!empty($story['date']))<span>{{ $story['date'] }}</span>@endif
                        @if(!empty($story['read']))<span>{{ $story['read'] }}</span>@endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@else
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Customers' }}</p>
            <h2>Unfiltered love from the StudyNest community</h2>
            <p>{{ $page['body'] }}</p>
        </div>
        <div class="ly-love-wall">
            @foreach($items as $note)
                <article class="ly-love-card">
                    <i class="fa fa-heart ly-love-icon" aria-hidden="true"></i>
                    <p>“{{ $note['quote'] }}”</p>
                    <div class="ly-love-footer">
                        <span>
                            <strong>{{ $note['name'] ?? '' }}</strong>
                            <em>{{ $note['role'] ?? '' }}</em>
                        </span>
                        @if(!empty($note['source']))
                            <small>{{ $note['source'] }}</small>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($related))
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>More from our customers</h2>
            <p>Explore stories, reviews, and community love across StudyNest.</p>
        </div>
        <div class="ly-grid ly-grid-2">
            @foreach($related as $relatedSlug => $relatedItem)
                <a class="ly-product-related" href="{{ route('website.customer', $relatedSlug) }}">
                    <span class="ly-product-related-icon"><i class="fa {{ $relatedItem['icon'] ?? 'fa-heart-o' }}"></i></span>
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
