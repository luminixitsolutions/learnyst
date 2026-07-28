@extends('website.layouts.app')

@section('title', ($page['title'] ?? 'StudyNest') . ' – StudyNest')
@section('meta_description', $page['summary'] ?? '')

@section('content')
@php $cta = config('website.cta'); @endphp

<div class="kingster-page-title-wrap kingster-style-custom kingster-left-align">
    <div class="kingster-header-transparent-substitute"></div>
    <div class="kingster-page-title-overlay"></div>
    <div class="kingster-page-title-bottom-gradient"></div>
    <div class="kingster-page-title-container kingster-container">
        <div class="kingster-page-title-content kingster-item-pdlr">
            <div class="kingster-page-caption">{{ $page['caption'] ?? ucfirst($type) }}</div>
            <h1 class="kingster-page-title">{{ $page['title'] }}</h1>
        </div>
    </div>
</div>

<div class="kingster-breadcrumbs">
    <div class="kingster-breadcrumbs-container kingster-container">
        <div class="kingster-breadcrumbs-item kingster-item-pdlr">
            <a href="{{ route('home') }}" class="home"><span>Home</span></a>
            &gt;
            <span>{{ $page['title'] }}</span>
        </div>
    </div>
</div>

<div class="kingster-page-wrapper" id="kingster-page-wrapper">
    <div class="gdlr-core-page-builder-body">
        <div class="gdlr-core-pbf-wrapper" style="padding:70px 0;">
            <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
                <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
                    <div class="gdlr-core-pbf-column gdlr-core-column-40 gdlr-core-column-first">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding:10px 20px;">
                            @if(!empty($page['summary']))
                                <h3 style="font-size:24px;margin-bottom:16px;">{{ $page['summary'] }}</h3>
                            @endif
                            <div class="gdlr-core-text-box-item-content">
                                <p>{{ $page['body'] ?? '' }}</p>
                            </div>

                            @if(!empty($page['features']))
                                <ul class="StudyNest-feature-list">
                                    @foreach($page['features'] as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if(!empty($page['testimonials']))
                                <div class="clearfix" style="margin-top:40px;">
                                    @foreach($page['testimonials'] as $item)
                                        <div style="padding:20px;background:#f8f9fb;margin-bottom:16px;">
                                            <p class="StudyNest-quote">“{{ $item['quote'] }}”</p>
                                            <strong>{{ $item['name'] }}</strong> — {{ $item['role'] }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($page['stories']))
                                <div class="clearfix" style="margin-top:40px;">
                                    @foreach($page['stories'] as $story)
                                        <div style="padding:18px;border-bottom:1px solid #e5e8ee;">
                                            <span style="color:#3db166;font-weight:600;">{{ $story['tag'] }}</span>
                                            <h3 style="font-size:18px;margin:6px 0;">{{ $story['title'] }}</h3>
                                            <p style="color:#777;font-size:13px;">{{ $story['date'] }} · {{ $story['read'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <p style="margin-top:30px;">
                                <a class="StudyNest-cta" href="{{ route('signup.show') }}">Start Free Trial</a>
                                <a class="StudyNest-cta outline" href="{{ route('website.page', 'product-demo') }}" style="border-color:#163663;color:#163663 !important;">Book Demo</a>
                            </p>
                        </div>
                    </div>

                    <div class="gdlr-core-pbf-column gdlr-core-column-20">
                        <div class="gdlr-core-pbf-column-content-margin gdlr-core-js" style="padding:10px 20px;">
                            <div style="background:#f3f5f8;padding:24px;">
                                <h3 style="font-size:18px;margin-bottom:14px;">Explore More</h3>
                                <ul class="StudyNest-feature-list" style="padding-left:16px;">
                                    <li><a href="{{ route('website.product', 'sell-online-courses') }}">Online Courses</a></li>
                                    <li><a href="{{ route('website.product', 'sell-mock-tests') }}">Mock Tests</a></li>
                                    <li><a href="{{ route('website.product', 'branded-mobile-app') }}">Branded App</a></li>
                                    <li><a href="{{ route('website.page', 'drm-security') }}">DRM Security</a></li>
                                    <li><a href="{{ route('website.pricing') }}">Pricing</a></li>
                                </ul>
                            </div>
                            <div style="margin-top:20px;">
                                <img src="{{ asset('website/upload/about-bg-3.jpg') }}" alt="" style="width:100%;border-radius:4px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
