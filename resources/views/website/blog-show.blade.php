@extends('website.layouts.app')

@section('title', ($post['title'] ?? 'Blog') . ' – ' . config('website.brand'))
@section('meta_description', $post['excerpt'] ?? '')

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
@endphp

<article>
    <section class="ly-product-hero ly-customer-hero" style="background: linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%);">
        <div class="ly-product-hero-mesh" aria-hidden="true"></div>
        <div class="ly-container ly-customer-hero-inner">
            @if(!empty($post['tag']))
                <p class="ly-product-eyebrow">{{ $post['tag'] }}</p>
            @endif
            <h1>{{ $post['title'] }}</h1>
            <p class="ly-product-lead">{{ $post['excerpt'] ?? '' }}</p>
            <div class="ly-story-meta" style="color:rgba(255,255,255,.75);">
                @if(!empty($post['author']))<span>{{ $post['author'] }}</span>@endif
                @if(!empty($post['date']))<span>{{ $post['date'] }}</span>@endif
                @if(!empty($post['read']))<span>{{ $post['read'] }}</span>@endif
            </div>
        </div>
    </section>

    <section class="ly-section">
        <div class="ly-container ly-blog-detail">
            <p class="ly-blog-back"><a href="{{ route('website.blogs') }}">← Back to blogs</a></p>
            <div class="ly-blog-prose">
                @foreach(($post['paragraphs'] ?? []) as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </section>

    @if(!empty($relatedPosts))
    <section class="ly-section ly-section-soft">
        <div class="ly-container">
            <div class="ly-section-head">
                <h2>More articles</h2>
                <p>Keep learning with related guides and growth tips.</p>
            </div>
            <div class="ly-story-grid">
                @foreach($relatedPosts as $item)
                    <a class="ly-story-card ly-blog-card" href="{{ route('website.blog.show', $item['slug']) }}">
                        @if(!empty($item['tag']))
                            <span class="ly-tag">{{ $item['tag'] }}</span>
                        @endif
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['excerpt'] ?? '' }}</p>
                        <div class="ly-story-meta">
                            @if(!empty($item['date']))<span>{{ $item['date'] }}</span>@endif
                            @if(!empty($item['read']))<span>{{ $item['read'] }}</span>@endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="ly-cta-banner">
        <div class="ly-container">
            <h2>Ready to put this into practice?</h2>
            <p>Start your free trial and build your academy with StudyNest.</p>
            <div class="ly-cta-actions">
                <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">Start Free Trial</a>
                <a class="ly-btn ly-btn-outline" href="{{ url($cta['demo'] ?? '/product-demo') }}">Book a Demo</a>
            </div>
        </div>
    </section>
</article>
@endsection
