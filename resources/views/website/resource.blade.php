@extends('website.layouts.app')

@section('title', ($page['title'] ?? 'Resources') . ' – ' . config('website.brand'))
@section('meta_description', $page['summary'] ?? '')

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
    $layout = $page['layout'] ?? 'demo';
    $items = $page['items'] ?? [];
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

@if($layout === 'demo')
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-split">
            <div class="ly-split-copy">
                <p class="ly-tag">{{ $page['caption'] ?? 'Demo' }}</p>
                <h3>What you’ll see in the walkthrough</h3>
                <p>{{ $page['body'] }}</p>
                @if(!empty($page['features']))
                    <ul class="ly-checklist">
                        @foreach($page['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="ly-split-media ly-product-panel">
                <div class="ly-product-panel-fallback">
                    <strong>Free demo</strong>
                    <span>Personalized for your academy goals</span>
                </div>
            </div>
        </div>
    </div>
</section>

@if(!empty($items))
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>How the demo works</h2>
            <p>A simple path from first conversation to a clear launch plan.</p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach($items as $index => $step)
                <div class="ly-card">
                    <span class="ly-tag">Step {{ $index + 1 }}</span>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@elseif($layout === 'help')
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Support' }}</p>
            <h2>Browse help by topic</h2>
            <p>{{ $page['body'] }}</p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach($items as $item)
                <article class="ly-card ly-help-card">
                    <div class="ly-card-icon"><i class="fa {{ $item['icon'] ?? 'fa-question-circle' }}"></i></div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

@elseif($layout === 'migration')
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-split">
            <div class="ly-split-copy">
                <p class="ly-tag">{{ $page['caption'] ?? 'Support' }}</p>
                <h3>Move without disrupting learners</h3>
                <p>{{ $page['body'] }}</p>
                @if(!empty($page['features']))
                    <ul class="ly-checklist">
                        @foreach($page['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="ly-split-media ly-product-panel">
                <div class="ly-product-panel-fallback">
                    <strong>Migration support</strong>
                    <span>Plan, move, and launch with guidance</span>
                </div>
            </div>
        </div>
    </div>
</section>

@if(!empty($items))
<section class="ly-section ly-section-soft">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>Your migration path</h2>
            <p>A practical sequence that keeps students and content moving together.</p>
        </div>
        <div class="ly-grid ly-grid-3">
            @foreach($items as $index => $step)
                <div class="ly-card">
                    <span class="ly-tag">Phase {{ $index + 1 }}</span>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@elseif($layout === 'guides')
<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Resources' }}</p>
            <h2>Practical guides for academy growth</h2>
            <p>{{ $page['body'] }}</p>
        </div>
        <div class="ly-story-grid">
            @foreach($items as $guide)
                <article class="ly-story-card">
                    <div class="ly-story-card-top">
                        @if(!empty($guide['tag']))
                            <span class="ly-tag">{{ $guide['tag'] }}</span>
                        @endif
                        @if(!empty($guide['read']))
                            <span class="ly-story-meta"><span>{{ $guide['read'] }}</span></span>
                        @endif
                    </div>
                    <h3>{{ $guide['title'] }}</h3>
                    <p>{{ $guide['desc'] ?? '' }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

@elseif($layout === 'changelog')
<section class="ly-section">
    <div class="ly-container ly-changelog-wrap">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Changelog' }}</p>
            <h2>Latest StudyNest releases</h2>
            <p>{{ $page['body'] }}</p>
        </div>
        <div class="ly-changelog">
            @foreach($items as $release)
                <article class="ly-changelog-card">
                    <div class="ly-changelog-top">
                        <span class="ly-tag">{{ $release['type'] ?? 'New' }}</span>
                        @if(!empty($release['date']))
                            <small>{{ $release['date'] }}</small>
                        @endif
                    </div>
                    <h3>{{ $release['title'] }}</h3>
                    <p>{{ $release['summary'] ?? '' }}</p>
                    @php
                        $highlights = \App\Services\ProductPageService::normalizeList($release['highlights'] ?? []);
                    @endphp
                    @if(!empty($highlights))
                        <ul class="ly-checklist">
                            @foreach($highlights as $line)
                                <li>{{ $line }}</li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($page['faq']))
<section class="ly-section {{ $layout === 'changelog' || $layout === 'guides' ? 'ly-section-soft' : '' }}">
    <div class="ly-container ly-product-faq-wrap">
        <div class="ly-section-head">
            <h2>Frequently asked questions</h2>
            <p>Helpful answers before you take the next step.</p>
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

<section class="ly-cta-banner">
    <div class="ly-container">
        <h2>{{ $page['cta_title'] ?? 'Need a hand getting started?' }}</h2>
        <p>{{ $page['cta_text'] ?? 'Start a free trial or book a demo with our team.' }}</p>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">{{ $page['cta_primary_label'] ?? 'Start Free Trial' }}</a>
            <a class="ly-btn ly-btn-outline" href="{{ url($cta['demo'] ?? '/product-demo') }}">{{ $page['cta_secondary_label'] ?? 'Book a Demo' }}</a>
        </div>
    </div>
</section>
@endsection
