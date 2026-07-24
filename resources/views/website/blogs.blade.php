@extends('website.layouts.app')

@section('title', ($page['title'] ?? 'Blogs') . ' – ' . config('website.brand'))
@section('meta_description', $page['summary'] ?? '')

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
    $posts = $page['posts'] ?? [];
    $featured = collect($posts)->firstWhere('featured', true) ?? ($posts[0] ?? null);
@endphp

<section class="ly-product-hero ly-customer-hero" style="background: {{ $page['hero_gradient'] ?? 'linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%)' }};">
    <div class="ly-product-hero-mesh" aria-hidden="true"></div>
    <div class="ly-container ly-customer-hero-inner">
        @if(!empty($page['eyebrow']))
            <p class="ly-product-eyebrow">{{ $page['eyebrow'] }}</p>
        @endif
        <h1>{{ $page['title'] }}</h1>
        <p class="ly-product-lead">{{ $page['summary'] }}</p>
    </div>
</section>

<section class="ly-section">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">{{ $page['caption'] ?? 'Resources' }}</p>
            <h2>Ideas to grow your course business</h2>
            <p>{{ $page['body'] }}</p>
        </div>

        @if($featured)
            <a class="ly-blog-featured" href="{{ route('website.blog.show', $featured['slug']) }}">
                <div>
                    @if(!empty($featured['tag']))
                        <span class="ly-tag">{{ $featured['tag'] }}</span>
                    @endif
                    <h3>{{ $featured['title'] }}</h3>
                    <p>{{ $featured['excerpt'] ?? '' }}</p>
                    <div class="ly-story-meta">
                        @if(!empty($featured['author']))<span>{{ $featured['author'] }}</span>@endif
                        @if(!empty($featured['date']))<span>{{ $featured['date'] }}</span>@endif
                        @if(!empty($featured['read']))<span>{{ $featured['read'] }}</span>@endif
                    </div>
                </div>
                <span class="ly-blog-featured-cta">Read article →</span>
            </a>
        @endif

        <div class="ly-story-grid" style="margin-top:24px;">
            @foreach($posts as $post)
                @if($featured && ($post['slug'] ?? '') === ($featured['slug'] ?? ''))
                    @continue
                @endif
                <a class="ly-story-card ly-blog-card" href="{{ route('website.blog.show', $post['slug']) }}">
                    @if(!empty($post['tag']))
                        <span class="ly-tag">{{ $post['tag'] }}</span>
                    @endif
                    <h3>{{ $post['title'] }}</h3>
                    <p>{{ $post['excerpt'] ?? '' }}</p>
                    <div class="ly-story-meta">
                        @if(!empty($post['date']))<span>{{ $post['date'] }}</span>@endif
                        @if(!empty($post['read']))<span>{{ $post['read'] }}</span>@endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="ly-cta-banner">
    <div class="ly-container">
        <h2>{{ $page['cta_title'] ?? 'Ready to put these ideas into practice?' }}</h2>
        <p>{{ $page['cta_text'] ?? 'Start your free trial and build your academy with Learnyst.' }}</p>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">{{ $page['cta_primary_label'] ?? 'Start Free Trial' }}</a>
            <a class="ly-btn ly-btn-outline" href="{{ url($cta['demo'] ?? '/product-demo') }}">{{ $page['cta_secondary_label'] ?? 'Book a Demo' }}</a>
        </div>
    </div>
</section>
@endsection
