@php
    $products = config('website.products');
    $solutions = config('website.solutions');
    $cta = config('website.cta');
@endphp

<footer>
    <div class="kingster-footer-wrapper">
        <div class="kingster-footer-container kingster-container clearfix">
            <div class="kingster-footer-column kingster-item-pdlr kingster-column-15">
                <div class="widget widget_text kingster-widget">
                    <div class="textwidget">
                        <p>
                            <strong style="font-size:20px;color:#fff;">{{ config('website.brand') }}</strong><br />
                            <span class="gdlr-core-space-shortcode" style="margin-top:18px;display:block;"></span>
                            @foreach(config('website.address') as $line)
                                {{ $line }}<br />
                            @endforeach
                        </p>
                        <p>
                            <strong>Reach Out</strong> — <a href="mailto:{{ config('website.email') }}">{{ config('website.email') }}</a><br />
                            <strong>Call Us</strong> — {{ config('website.phone') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="kingster-footer-column kingster-item-pdlr kingster-column-15">
                <div class="widget widget_gdlr-core-custom-menu-widget kingster-widget">
                    <h3 class="kingster-widget-title">Products</h3><span class="clear"></span>
                    <ul class="gdlr-core-custom-menu-widget gdlr-core-menu-style-plain">
                        @foreach(array_slice($products, 0, 6, true) as $slug => $item)
                            <li class="menu-item"><a href="{{ route('website.product', $slug) }}">{{ $item['menu'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="kingster-footer-column kingster-item-pdlr kingster-column-15">
                <div class="widget widget_gdlr-core-custom-menu-widget kingster-widget">
                    <h3 class="kingster-widget-title">Solutions</h3><span class="clear"></span>
                    <ul class="gdlr-core-custom-menu-widget gdlr-core-menu-style-plain">
                        @foreach(array_slice($solutions, 0, 6, true) as $slug => $item)
                            <li class="menu-item"><a href="{{ route('website.solution', $slug) }}">{{ $item['menu'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="kingster-footer-column kingster-item-pdlr kingster-column-15">
                <div class="widget widget_gdlr-core-custom-menu-widget kingster-widget">
                    <h3 class="kingster-widget-title">Institute</h3><span class="clear"></span>
                    <ul class="gdlr-core-custom-menu-widget gdlr-core-menu-style-plain">
                        <li class="menu-item"><a href="{{ route('website.page', 'about-us') }}">About Us</a></li>
                        <li class="menu-item"><a href="{{ route('website.companies.index') }}">Institutes</a></li>
                        <li class="menu-item"><a href="{{ route('public.courses') }}">Courses</a></li>
                        <li class="menu-item"><a href="{{ route('website.customer', 'success-stories') }}">Success Stories</a></li>
                        <li class="menu-item"><a href="{{ route('website.customer', 'testimonials') }}">Testimonials</a></li>
                        <li class="menu-item"><a href="{{ route('website.page', 'careers') }}">Career</a></li>
                        <li class="menu-item"><a href="{{ route('website.page', 'privacy-policy') }}">Privacy Policy</a></li>
                        <li class="menu-item"><a href="{{ route('website.page', 'terms-and-conditions') }}">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="kingster-copyright-wrapper">
        <div class="kingster-copyright-container kingster-container clearfix">
            <div class="kingster-copyright-left kingster-item-pdlr">
                Copyright © {{ date('Y') }} Learnyst. All Rights Reserved
            </div>
            <div class="kingster-copyright-right kingster-item-pdlr">
                <a href="{{ route('website.page', 'product-demo') }}" style="margin-right:14px;">Book Demo</a>
                <a href="{{ route('signup.show') }}">Start Free Trial</a>
            </div>
        </div>
    </div>
</footer>
