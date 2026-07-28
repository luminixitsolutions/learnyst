@extends('website.layouts.app')

@section('title', ($brand['name'] ?? 'StudyNest') . ' – ' . ($brand['tagline'] ?? 'The Most Secure LMS to Sell Courses Online'))

@section('content')
@php
    $brandName = $brand['name'] ?? config('website.brand');
    $videoId = $video['youtube_id'] ?? '0q4mL4wqgSo';
@endphp

<div class="ly-home">
{{-- Hero: one composition — brand, headline, lead, CTAs, full-bleed image --}}
<div class="ly-hero" id="StudyNest-hero-slider">
    <div class="ly-hero-slides">
        @foreach($slides as $index => $slide)
            <div class="ly-hero-slide {{ $index === 0 ? 'is-active' : '' }}" style="--ly-hero-image: url('{{ $slide['image_url'] }}');">
                <div class="ly-hero-media" aria-hidden="true"></div>
                <div class="ly-hero-veil"></div>
                <div class="ly-hero-content">
                    <div class="ly-container">
                        <p class="ly-hero-brand">{{ $brandName }}</p>
                        <h1 class="ly-hero-title">{{ $slide['title'] }}</h1>
                        <p class="ly-hero-lead">{{ $slide['text'] }}</p>
                        <div class="ly-hero-actions">
                            <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Start Free Trial</a>
                            <a class="ly-btn ly-btn-outline" href="{{ route('website.page', 'product-demo') }}">Book Demo</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if(count($slides) > 1)
        <button type="button" class="ly-hero-nav prev" aria-label="Previous slide">‹</button>
        <button type="button" class="ly-hero-nav next" aria-label="Next slide">›</button>
        <div class="ly-hero-dots">
            @foreach($slides as $index => $slide)
                <button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" data-go="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</div>

{{-- Quiet trust strip --}}
<section class="ly-trust">
    <div class="ly-container">
        <p class="ly-trust-label">Trusted by institutes growing with {{ $brandName }}</p>
        <div class="ly-trust-logos">
            @foreach($partners as $partner)
                <div class="ly-trust-logo">
                    <img src="{{ $partner['image_url'] }}" alt="{{ $partner['name'] }}" loading="lazy">
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Product tour video --}}
<section class="ly-section ly-video-band">
    <div class="ly-container">
        <div class="ly-section-head ly-reveal">
            <h2>See {{ $brandName }} in action</h2>
            <p>A quick look at how institutes create courses, run live classes, and sell securely — from one platform.</p>
        </div>
        <div class="ly-video-frame ly-reveal" id="StudyNest-home-video" data-video-id="{{ $videoId }}">
            <img class="StudyNest-video-poster" src="https://i.ytimg.com/vi_webp/{{ $videoId }}/maxresdefault.webp" alt="{{ $brandName }} product video" loading="lazy">
            <button type="button" class="ly-video-play" aria-label="Play video">
                <span class="ly-video-play-icon"></span>
            </button>
            <div class="StudyNest-video-embed" hidden></div>
        </div>
    </div>
</section>

{{-- Impact numbers --}}
<section class="ly-impact">
    <div class="ly-impact-bg" aria-hidden="true"></div>
    <div class="ly-container">
        <div class="ly-impact-grid">
            @foreach($stats as $stat)
                <div class="ly-impact-item ly-reveal">
                    <strong>{{ $stat['value'] }}</strong>
                    <span>{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Platform --}}
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head ly-reveal">
            <h2>
                <span class="tone-green">{{ $platform['heading_green'] ?? 'All-in-One' }}</span>
                <span class="tone-blue">{{ $platform['heading_blue'] ?? 'Platform' }}</span>
                {{ $platform['heading_rest'] ?? '' }}
            </h2>
            <p>{{ $platform['subheading'] ?? '' }}</p>
        </div>
        <div class="ly-platform-wrap">
            <div class="ly-platform-rail" id="StudyNest-platform-carousel">
                @foreach(($platform['items'] ?? []) as $card)
                    <a class="ly-platform-tile" href="{{ route('website.product', $card['slug']) }}" style="--tile-bg: {{ $card['bg'] }};">
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['desc'] }}</p>
                        <div class="ly-platform-media">
                            <img src="{{ $card['image_url'] }}" alt="{{ $card['title'] }}" loading="lazy">
                        </div>
                        <span class="ly-platform-more">Learn more</span>
                    </a>
                @endforeach
            </div>
            <button type="button" class="ly-platform-nav prev" id="platform-prev" aria-label="Previous">‹</button>
            <button type="button" class="ly-platform-nav next" id="platform-next" aria-label="Next">›</button>
        </div>
    </div>
</section>

{{-- Marketing --}}
<section class="ly-section ly-section-mist">
    <div class="ly-container">
        <div class="ly-split ly-reveal">
            <div class="ly-split-copy">
                <p class="ly-kicker">Growth</p>
                <h3>{{ $marketing['title'] }}</h3>
                <p>{{ $marketing['text'] }}</p>
                <ul class="ly-checklist">
                    @foreach($marketing['bullets'] as $bullet)
                        <li>{{ $bullet }}</li>
                    @endforeach
                </ul>
                <div class="ly-hero-actions">
                    <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Start Free Trial</a>
                    <a class="ly-btn ly-btn-ghost" href="{{ route('website.product', 'marketing-sales-hub') }}">Explore Hub</a>
                </div>
            </div>
            <div class="ly-split-media">
                <figure class="ly-media-frame">
                    <img src="{{ $marketing['image_url'] }}" alt="Marketing tools" loading="lazy">
                </figure>
            </div>
        </div>
    </div>
</section>

{{-- DRM --}}
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-split reverse ly-reveal">
            <div class="ly-split-copy">
                <p class="ly-kicker">Security</p>
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
                <figure class="ly-media-frame">
                    <img src="{{ $drm['image_url'] }}" alt="DRM Security" loading="lazy">
                </figure>
            </div>
        </div>
    </div>
</section>

{{-- Branded apps --}}
<section class="ly-apps-band">
    <div class="ly-apps-bg" aria-hidden="true"></div>
    <div class="ly-container ly-reveal">
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
<section class="ly-section ly-domains-band">
    <div class="ly-domains-bg" aria-hidden="true"></div>
    <div class="ly-container">
        <div class="ly-section-head ly-reveal">
            <p class="ly-kicker">Built for educators</p>
            <h2>{{ $domains['title'] }}</h2>
            <p>{{ $domains['text'] }}</p>
        </div>
        <div class="ly-domain-grid">
            @foreach(($domains['items'] ?? []) as $index => $domain)
                <a class="ly-domain-tile ly-reveal" href="{{ ($domain['type'] ?? 'product') === 'product' ? route('website.product', $domain['slug']) : route('website.solution', $domain['slug']) }}" style="--delay: {{ $index * 0.06 }}s">
                    <span class="ly-domain-icon" aria-hidden="true"><i class="fa {{ $domain['icon'] }}"></i></span>
                    <strong class="ly-domain-title">{{ $domain['title'] }}</strong>
                    <em class="ly-domain-desc">{{ $domain['desc'] }}</em>
                    <span class="ly-domain-link">Explore <i class="fa fa-arrow-right"></i></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Support --}}
@php
    $supportIcons = ['fa-headphones', 'fa-comments', 'fa-clock-o', 'fa-book', 'fa-sitemap', 'fa-star'];
    $supportScore = '9.1';
    $supportTitle = $support['title'] ?? '';
    $supportTitleClean = trim(preg_replace('/^\s*9\.1\s*\/\s*10\s*/i', '', $supportTitle));
@endphp
<section class="ly-section ly-support-band">
    <div class="ly-support-bg" aria-hidden="true"></div>
    <div class="ly-container">
        <div class="ly-support-layout ly-reveal">
            <aside class="ly-support-score">
                <p class="ly-kicker">Support you can trust</p>
                <div class="ly-support-score-value">
                    <strong>{{ $supportScore }}</strong>
                    <span>/10</span>
                </div>
                <h2>{{ $supportTitleClean !== '' ? $supportTitleClean : 'Customer Satisfaction' }}</h2>
                <p>{{ $support['text'] }}</p>
                <a class="ly-btn ly-btn-green" href="{{ route('website.page', 'product-demo') }}">Talk to Support</a>
            </aside>
            <div class="ly-support-grid">
                @foreach(($support['items'] ?? []) as $index => $item)
                    <div class="ly-support-item">
                        <span class="ly-support-icon" aria-hidden="true">
                            <i class="fa {{ $supportIcons[$index % count($supportIcons)] }}"></i>
                        </span>
                        <div class="ly-support-copy">
                            <h3>{{ $item['title'] }}</h3>
                            <p>{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="ly-section ly-testimonials-band">
    <div class="ly-container">
        <div class="ly-section-head ly-reveal">
            <p class="ly-kicker">Social proof</p>
            <h2>{{ $testimonialsSection['title'] ?? 'Real Words, Real Impact' }}</h2>
            <p>{{ $testimonialsSection['text'] ?? '' }}</p>
        </div>
        <div class="ly-testi-grid">
            @foreach(collect($testimonials)->take(3) as $item)
                @php
                    $photo = $item['image'] ?? $item['image_url'] ?? null;
                    $initials = collect(explode(' ', $item['name'] ?? 'A'))
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                        ->implode('');
                    $rating = (int) ($item['rating'] ?? 5);
                @endphp
                <article class="ly-testi-card ly-reveal">
                    <div class="ly-testi-stars" aria-label="{{ $rating }} star rating">
                        @for($i = 0; $i < max(1, $rating); $i++)
                            <i class="fa fa-star"></i>
                        @endfor
                    </div>
                    <p class="ly-testi-quote">“{{ $item['quote'] }}”</p>
                    @if(!empty($item['result']))
                        <p class="ly-testi-result">{{ $item['result'] }}</p>
                    @endif
                    <div class="ly-testi-person">
                        <span class="ly-testi-avatar" data-initials="{{ $initials }}">
                            @if($photo)
                                <img src="{{ $photo }}" alt="{{ $item['name'] }}" loading="lazy" width="56" height="56"
                                     onerror="this.style.display='none'; this.parentElement.classList.add('is-fallback');">
                            @else
                                <span class="ly-testi-initials">{{ $initials }}</span>
                            @endif
                        </span>
                        <span class="ly-testi-meta">
                            <strong>{{ $item['name'] }}</strong>
                            <em>{{ $item['role'] }}</em>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="ly-center">
            <a class="ly-btn ly-btn-ghost" href="{{ route('website.customer', 'testimonials') }}">See More Stories</a>
        </div>
    </div>
</section>

{{-- Success stories --}}
<section class="ly-section ly-stories-band">
    <div class="ly-container">
        <div class="ly-section-head ly-reveal">
            <p class="ly-kicker">Case studies</p>
            <h2>{{ $successStoriesSection['title'] ?? 'Success Stories from Our Educators' }}</h2>
            <p>{{ $successStoriesSection['text'] ?? '' }}</p>
        </div>
        <div class="ly-cases-grid">
            @foreach(collect($successStories)->take(6) as $index => $story)
                @php
                    $covers = [
                        ['bg' => 'linear-gradient(145deg, #0b1220 0%, #163663 55%, #1d4ed8 100%)', 'icon' => 'fa-university'],
                        ['bg' => 'linear-gradient(145deg, #052e16 0%, #166534 50%, #22c55e 100%)', 'icon' => 'fa-stethoscope'],
                        ['bg' => 'linear-gradient(145deg, #0f172a 0%, #334155 45%, #0ea5e9 100%)', 'icon' => 'fa-code'],
                        ['bg' => 'linear-gradient(145deg, #1c1917 0%, #9a3412 50%, #f59e0b 100%)', 'icon' => 'fa-line-chart'],
                        ['bg' => 'linear-gradient(145deg, #042f2e 0%, #0f766e 50%, #2dd4bf 100%)', 'icon' => 'fa-bar-chart'],
                        ['bg' => 'linear-gradient(145deg, #1e1b4b 0%, #4338ca 50%, #818cf8 100%)', 'icon' => 'fa-book'],
                    ];
                    $cover = $covers[$index % count($covers)];
                    $image = $story['image'] ?? null;
                @endphp
                <article class="ly-case-card ly-reveal">
                    <div class="ly-case-media" style="--case-bg: {{ $cover['bg'] }};">
                        <div class="ly-case-cover" aria-hidden="true">
                            <i class="fa {{ $cover['icon'] }}"></i>
                            <span class="ly-case-cover-title">{{ $story['tag'] }}</span>
                        </div>
                        @if($image)
                            <img src="{{ $image }}" alt="{{ $story['title'] }}" loading="lazy"
                                 onerror="this.remove();">
                        @endif
                        <span class="ly-case-badge">{{ $story['tag'] }}</span>
                    </div>
                    <div class="ly-case-body">
                        <h3>{{ $story['title'] }}</h3>
                        <p>{{ $story['date'] }} · {{ $story['read'] }}</p>
                        <span class="ly-case-link">Read story <i class="fa fa-arrow-right"></i></span>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="ly-center">
            <a class="ly-btn ly-btn-green" href="{{ route('website.customer', 'success-stories') }}">Explore Success Stories</a>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="ly-final-cta">
    <div class="ly-final-cta-bg" aria-hidden="true"></div>
    <div class="ly-container ly-reveal">
        <h2>{{ $cta['title'] }}</h2>
        <p>{{ $cta['text'] }}</p>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ route('signup.show') }}">Get Started</a>
            <a class="ly-btn ly-btn-outline" href="{{ route('website.page', 'product-demo') }}">Book Demo</a>
        </div>
    </div>
</section>
</div>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
@endpush

@push('scripts')
<script>
(function ($) {
    var $slider = $('#StudyNest-hero-slider');
    if ($slider.length) {
        var $slides = $slider.find('.ly-hero-slide');
        var $dots = $slider.find('.ly-hero-dots button');
        var index = 0;
        var total = $slides.length;
        var timer = null;

        function goTo(i) {
            if (!total) return;
            index = (i + total) % total;
            $slides.removeClass('is-active').eq(index).addClass('is-active');
            $dots.removeClass('is-active').eq(index).addClass('is-active');
        }
        function next() { goTo(index + 1); }
        function prev() { goTo(index - 1); }
        function start() { stop(); if (total > 1) timer = setInterval(next, 6000); }
        function stop() { if (timer) clearInterval(timer); }

        $slider.find('.ly-hero-nav.next').on('click', function () { next(); start(); });
        $slider.find('.ly-hero-nav.prev').on('click', function () { prev(); start(); });
        $dots.on('click', function () { goTo(parseInt($(this).data('go'), 10)); start(); });
        $slider.on('mouseenter', stop).on('mouseleave', start);
        start();
    }

    var $video = $('#StudyNest-home-video');
    $video.on('click', '.ly-video-play, .StudyNest-video-poster', function (e) {
        e.preventDefault();
        if ($video.hasClass('is-playing')) return;
        var id = $video.data('video-id');
        $video.find('.StudyNest-video-embed').prop('hidden', false).html(
            '<iframe src="https://www.youtube.com/embed/' + id + '?autoplay=1&rel=0&modestbranding=1&playsinline=1" title="StudyNest Youtube Video" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe>'
        );
        $video.addClass('is-playing');
    });

    var $carousel = $('#StudyNest-platform-carousel');
    if ($carousel.length) {
        var scrollBy = function (dir) {
            $carousel.get(0).scrollBy({ left: Math.min(360, $carousel.width() * 0.85) * dir, behavior: 'smooth' });
        };
        $('#platform-next').on('click', function () { scrollBy(1); });
        $('#platform-prev').on('click', function () { scrollBy(-1); });
    }

    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.ly-reveal').forEach(function (el) { io.observe(el); });
    } else {
        document.querySelectorAll('.ly-reveal').forEach(function (el) { el.classList.add('is-visible'); });
    }
})(jQuery);
</script>
@endpush
@endsection
