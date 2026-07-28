@extends('website.layouts.app')

@section('title', 'Pricing – ' . config('website.brand'))
@section('meta_description', $page['summary'] ?? 'Flexible StudyNest plans for startups and growing institutes.')

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
@endphp

<section class="ly-pricing-hero">
    <div class="ly-pricing-hero-glow" aria-hidden="true"></div>
    <div class="ly-container ly-pricing-hero-inner">
        <p class="ly-pricing-eyebrow">{{ $page['caption'] ?? 'Plans' }}</p>
        <h1>{{ $brand }}</h1>
        <p class="ly-pricing-lead">{{ $page['summary'] ?? 'Flexible plans for startups and growing institutes.' }}</p>
        @if(!empty($packages) && $packages->count())
            <div class="ly-billing-toggle" data-billing-toggle>
                <button type="button" class="is-active" data-billing="monthly">Monthly</button>
                <button type="button" data-billing="yearly">Yearly <span>Save more</span></button>
            </div>
        @endif
    </div>
</section>

<section class="ly-section ly-pricing-section">
    <div class="ly-container">
        @if($packages->isEmpty())
            <div class="ly-pricing-empty">
                <h2>Plans coming soon</h2>
                <p>{{ $page['body'] ?? 'Start with a free trial and scale as you grow.' }}</p>
                <div class="ly-hero-actions">
                    <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">Start Free Trial</a>
                    <a class="ly-btn ly-btn-outline-dark" href="{{ url($cta['demo'] ?? '/product-demo') }}">Book a Demo</a>
                </div>
            </div>
        @else
            <div class="ly-pricing-grid ly-pricing-grid-{{ min(4, max(1, $packages->count())) }}">
                @foreach($packages as $package)
                    @php
                        $features = $package->featureList();
                        $monthly = $package->formattedPrice('monthly');
                        $yearly = $package->formattedPrice('yearly');
                        $periodMonthly = $package->is_free || $package->is_custom ? '' : '/month';
                        $periodYearly = $package->is_free || $package->is_custom ? '' : '/year';
                    @endphp
                    <article class="ly-price-card {{ $package->is_featured ? 'is-featured' : '' }}">
                        @if($package->badge || $package->is_featured)
                            <div class="ly-price-badge">{{ $package->badge ?: 'Recommended' }}</div>
                        @endif

                        <header class="ly-price-card-head">
                            <h2>{{ $package->name }}</h2>
                            @if($package->tagline)
                                <p class="ly-price-tagline">{{ $package->tagline }}</p>
                            @endif
                        </header>

                        <div class="ly-price-amount" data-price-amount>
                            <span class="ly-price-value" data-price-monthly="{{ $monthly }}" data-price-yearly="{{ $yearly }}">{{ $monthly }}</span>
                            <span class="ly-price-period" data-period-monthly="{{ $periodMonthly }}" data-period-yearly="{{ $periodYearly }}">{{ $periodMonthly }}</span>
                        </div>

                        @if($package->trial_days > 0 && ! $package->is_custom)
                            <p class="ly-price-trial">{{ $package->trial_days }}-day free trial</p>
                        @elseif($package->description)
                            <p class="ly-price-trial">{{ \Illuminate\Support\Str::limit($package->description, 90) }}</p>
                        @else
                            <p class="ly-price-trial">&nbsp;</p>
                        @endif

                        <a class="ly-btn {{ $package->is_featured ? 'ly-btn-green' : 'ly-btn-outline-dark' }} ly-price-cta"
                           href="{{ $package->resolvedCtaUrl() }}">
                            {{ $package->cta_label }}
                        </a>

                        @if(count($features))
                            <ul class="ly-price-features">
                                @foreach($features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if(!empty($page['body']))
<section class="ly-section ly-section-soft ly-pricing-note">
    <div class="ly-container">
        <div class="ly-pricing-note-inner">
            <h2>Everything you need to grow</h2>
            <p>{{ $page['body'] }}</p>
            <div class="ly-hero-actions">
                <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">Start Free Trial</a>
                <a class="ly-btn ly-btn-outline-dark" href="{{ url($cta['demo'] ?? '/product-demo') }}">Talk to sales</a>
            </div>
        </div>
    </div>
</section>
@endif

<section class="ly-section ly-pricing-faq">
    <div class="ly-container">
        <div class="ly-section-head">
            <h2>Common questions</h2>
            <p>Simple answers before you pick a plan.</p>
        </div>
        <div class="ly-pricing-faq-grid">
            <div>
                <h3>Can I change plans later?</h3>
                <p>Yes. Start where you are and upgrade as your academy grows — your content and learners stay with you.</p>
            </div>
            <div>
                <h3>Is there a free trial?</h3>
                <p>Most plans include a free trial so you can explore course builder, live classes, and marketing tools risk-free.</p>
            </div>
            <div>
                <h3>Do you support custom enterprise needs?</h3>
                <p>Absolutely. Choose a custom plan or book a demo and we’ll tailor DRM, apps, and onboarding to your institute.</p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    var toggle = document.querySelector('[data-billing-toggle]');
    if (!toggle) return;

    var buttons = toggle.querySelectorAll('[data-billing]');
    var cards = document.querySelectorAll('[data-price-amount]');

    function setBilling(mode) {
        buttons.forEach(function (btn) {
            btn.classList.toggle('is-active', btn.getAttribute('data-billing') === mode);
        });
        cards.forEach(function (wrap) {
            var value = wrap.querySelector('.ly-price-value');
            var period = wrap.querySelector('.ly-price-period');
            if (!value) return;
            value.textContent = value.getAttribute('data-price-' + mode) || value.textContent;
            if (period) {
                period.textContent = period.getAttribute('data-period-' + mode) || '';
            }
        });
    }

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            setBilling(btn.getAttribute('data-billing'));
        });
    });
})();
</script>
@endpush
