@extends('website.layouts.app')

@section('title', ($brand['name'] ?? 'Learnyst') . ' – ' . ($brand['tagline'] ?? 'The Most Secure LMS to Sell Courses Online'))

@section('content')
@php
    $brandName = $brand['name'] ?? config('website.brand');
    $videoId = $video['youtube_id'] ?? '0q4mL4wqgSo';
@endphp

{{-- Hero --}}
<div class="learnyst-hero-slider" id="learnyst-hero-slider">
    <div class="slides">
        @foreach($slides as $index => $slide)
            <div class="slide {{ $index === 0 ? 'is-active' : '' }}" style="background-image: url('{{ $slide['image_url'] }}');">
                <div class="slide-overlay"></div>
                <div class="slide-content">
                    <div class="ly-container">
                        <p class="ly-brand-mark">{{ $brandName }}</p>
                        <h1>{{ $slide['title'] }}</h1>
                        <p class="ly-lead">{{ $slide['text'] }}</p>
                        <div class="ly-hero-actions">
                            <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Start Free Trial</a>
                            <a class="ly-btn ly-btn-outline" href="{{ route('website.page', 'product-demo') }}">Book Demo</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <button type="button" class="learnyst-hero-nav prev" aria-label="Previous slide">‹</button>
    <button type="button" class="learnyst-hero-nav next" aria-label="Next slide">›</button>
    <div class="learnyst-hero-dots">
        @foreach($slides as $index => $slide)
            <button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" data-go="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
</div>

{{-- Video --}}
<section class="learnyst-video-section">
    <div class="ly-container">
        <div class="learnyst-video-ctas">
            <a class="ly-btn ly-btn-primary" href="{{ route('signup.show') }}">Start Free Trial</a>
            <a class="ly-btn ly-btn-outline-dark" href="{{ route('website.page', 'product-demo') }}">Book Demo</a>
        </div>
        <div class="learnyst-video-player" id="learnyst-home-video" data-video-id="{{ $videoId }}">
            <img class="learnyst-video-poster" src="https://i.ytimg.com/vi_webp/{{ $videoId }}/maxresdefault.webp" alt="{{ $brandName }} product video">
            <button type="button" class="learnyst-video-play" aria-label="Play video"><span class="learnyst-video-play-icon"></span></button>
            <div class="learnyst-video-embed" hidden></div>
        </div>
    </div>
</section>

{{-- Partners + stats --}}
<section class="learnyst-social-proof">
    <div class="ly-container">
        <div class="learnyst-partners">
            @foreach($partners as $partner)
                <div class="learnyst-partner-item">
                    <img src="{{ $partner['image_url'] }}" alt="{{ $partner['name'] }}">
                </div>
            @endforeach
        </div>
    </div>
    <div class="learnyst-stats-banner">
        <div class="ly-container">
            <div class="learnyst-stats-grid">
                @foreach($stats as $stat)
                    <div class="learnyst-stat-item"><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Platform --}}
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>
                <span class="tone-green">{{ $platform['heading_green'] ?? 'All-in-One' }}</span>
                <span class="tone-blue">{{ $platform['heading_blue'] ?? 'Platform' }}</span>
                {{ $platform['heading_rest'] ?? '' }}
            </h2>
            <p>{{ $platform['subheading'] ?? '' }}</p>
        </div>
        <div class="learnyst-platform-carousel-wrap">
            <div class="learnyst-platform-carousel" id="learnyst-platform-carousel">
                @foreach(($platform['items'] ?? []) as $card)
                    <a class="learnyst-platform-card" href="{{ route('website.product', $card['slug']) }}" style="background: {{ $card['bg'] }};">
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['desc'] }}</p>
                        <div class="learnyst-platform-card-media">
                            <img src="{{ $card['image_url'] }}" alt="{{ $card['title'] }}">
                        </div>
                        <span class="learnyst-platform-link">Learn More →</span>
                    </a>
                @endforeach
            </div>
            <button type="button" class="learnyst-platform-nav prev" id="platform-prev" aria-label="Previous">‹</button>
            <button type="button" class="learnyst-platform-nav next" id="platform-next" aria-label="Next">›</button>
        </div>
    </div>
</section>

{{-- Marketing --}}
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-split">
            <div class="ly-split-copy">
                <h3>{{ $marketing['title'] }}</h3>
                <p>{{ $marketing['text'] }}</p>
                <ul class="ly-checklist">
                    @foreach($marketing['bullets'] as $bullet)
                        <li>{{ $bullet }}</li>
                    @endforeach
                </ul>
                <div class="ly-hero-actions">
                    <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Start Free Trial</a>
                    <a class="ly-btn ly-btn-outline-dark" href="{{ route('website.product', 'marketing-sales-hub') }}">Explore Hub</a>
                </div>
            </div>
            <div class="ly-split-media">
                <img src="{{ $marketing['image_url'] }}" alt="Marketing tools">
            </div>
        </div>
    </div>
</section>

{{-- DRM --}}
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-split reverse">
            <div class="ly-split-copy">
                <h3>{{ $drm['title'] }}</h3>
                <p>{{ $drm['text'] }}</p>
                <ul class="ly-checklist">
                    @foreach($drm['bullets'] as $bullet)
                        <li>{{ $bullet }}</li>
                    @endforeach
                </ul>
                <a class="ly-btn ly-btn-green" href="{{ route('website.page', 'drm-security') }}">Know More</a>
            </div>
            <div class="ly-split-media">
                <img src="{{ $drm['image_url'] }}" alt="DRM Security">
            </div>
        </div>
    </div>
</section>

{{-- Branded apps --}}
<section class="ly-section ly-section-dark">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $apps['title'] }}</h2>
            <p>{{ $apps['text'] }}</p>
        </div>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ route('website.product', 'branded-mobile-app') }}">Get App</a>
            <a class="ly-btn ly-btn-outline" href="{{ route('website.page', 'product-demo') }}">Book Demo</a>
        </div>
    </div>
</section>

{{-- Domains --}}
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $domains['title'] }}</h2>
            <p>{{ $domains['text'] }}</p>
        </div>
        <div class="ly-grid ly-grid-4">
            @foreach(($domains['items'] ?? []) as $domain)
                <div class="ly-card">
                    <div class="ly-card-icon"><i class="fa {{ $domain['icon'] }}"></i></div>
                    <h3>{{ $domain['title'] }}</h3>
                    <p>{{ $domain['desc'] }}</p>
                    <p style="margin-top:14px;">
                        <a href="{{ ($domain['type'] ?? 'product') === 'product' ? route('website.product', $domain['slug']) : route('website.solution', $domain['slug']) }}">Learn More →</a>
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Support --}}
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $support['title'] }}</h2>
            <p>{{ $support['text'] }}</p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach(($support['items'] ?? []) as $item)
                <div class="ly-card">
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $testimonialsSection['title'] ?? 'Real Words, Real Impact' }}</h2>
            <p>{{ $testimonialsSection['text'] ?? '' }}</p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach(collect($testimonials)->take(3) as $item)
                <div class="ly-card">
                    <p class="ly-quote">“{{ $item['quote'] }}”</p>
                    <div class="ly-person">
                        <strong>{{ $item['name'] }}</strong>
                        <span>{{ $item['role'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="ly-center">
            <a class="ly-btn ly-btn-outline-dark" href="{{ route('website.customer', 'testimonials') }}">See More</a>
        </div>
    </div>
</section>

{{-- Success stories --}}
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>{{ $successStoriesSection['title'] ?? 'Success Stories from Our Educators' }}</h2>
            <p>{{ $successStoriesSection['text'] ?? '' }}</p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach(collect($successStories)->take(6) as $story)
                <div class="ly-card">
                    <span class="ly-tag">{{ $story['tag'] }}</span>
                    <h3>{{ $story['title'] }}</h3>
                    <p>{{ $story['date'] }} · {{ $story['read'] }}</p>
                </div>
            @endforeach
        </div>
        <div class="ly-center">
            <a class="ly-btn ly-btn-green" href="{{ route('website.customer', 'success-stories') }}">Explore Success Stories</a>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="ly-cta-banner">
    <div class="ly-container">
        <h2>{{ $cta['title'] }}</h2>
        <p>{{ $cta['text'] }}</p>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Get Started</a>
            <a class="ly-btn ly-btn-outline" href="{{ route('website.page', 'product-demo') }}">Book Demo</a>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function ($) {
    var $slider = $('#learnyst-hero-slider');
    if ($slider.length) {
        var $slides = $slider.find('.slide');
        var $dots = $slider.find('.learnyst-hero-dots button');
        var index = 0;
        var total = $slides.length;
        var timer = null;

        function goTo(i) {
            index = (i + total) % total;
            $slides.removeClass('is-active').eq(index).addClass('is-active');
            $dots.removeClass('is-active').eq(index).addClass('is-active');
        }
        function next() { goTo(index + 1); }
        function prev() { goTo(index - 1); }
        function start() { stop(); timer = setInterval(next, 5500); }
        function stop() { if (timer) clearInterval(timer); }

        $slider.find('.learnyst-hero-nav.next').on('click', function () { next(); start(); });
        $slider.find('.learnyst-hero-nav.prev').on('click', function () { prev(); start(); });
        $dots.on('click', function () { goTo(parseInt($(this).data('go'), 10)); start(); });
        $slider.on('mouseenter', stop).on('mouseleave', start);
        start();
    }

    var $video = $('#learnyst-home-video');
    $video.on('click', '.learnyst-video-play, .learnyst-video-poster', function (e) {
        e.preventDefault();
        if ($video.hasClass('is-playing')) return;
        var id = $video.data('video-id');
        $video.find('.learnyst-video-embed').prop('hidden', false).html(
            '<iframe src="https://www.youtube.com/embed/' + id + '?autoplay=1&rel=0&modestbranding=1&playsinline=1" title="Learnyst Youtube Video" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe>'
        );
        $video.addClass('is-playing');
    });

    var $carousel = $('#learnyst-platform-carousel');
    if ($carousel.length) {
        var scrollBy = function (dir) {
            $carousel.get(0).scrollBy({ left: Math.min(340, $carousel.width() * 0.85) * dir, behavior: 'smooth' });
        };
        $('#platform-next').on('click', function () { scrollBy(1); });
        $('#platform-prev').on('click', function () { scrollBy(-1); });
    }
})(jQuery);
</script>
@endpush
@endsection
