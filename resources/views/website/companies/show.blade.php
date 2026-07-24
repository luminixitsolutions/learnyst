@extends('website.layouts.app')

@section('title', $company->name . ' – ' . config('website.brand'))
@section('meta_description', $company->tagline ?: Str::limit(strip_tags($company->about ?? ''), 160))

@section('content')
@php
    $cta = config('website.cta');
    $brand = config('website.brand');
    $social = $company->social_links ?? [];
    $highlights = $company->highlights ?? [];
    $profile = $company->profile ?? [];
    $stats = $profile['stats'] ?? [];
    $specialties = $profile['specialties'] ?? [];
    $whyUs = $profile['why_us'] ?? [];
    $faqs = $profile['faqs'] ?? [];
    $mission = $profile['mission'] ?? '';
    $vision = $profile['vision'] ?? '';
    $founded = $profile['founded_year'] ?? '';
    $state = $profile['state'] ?? '';
    $country = $profile['country'] ?? '';
    $hours = $profile['working_hours'] ?? '';
    $locationParts = array_filter([$company->city, $state, $country]);
    $aboutParagraphs = preg_split("/\n\s*\n/", trim((string) ($company->about ?? ''))) ?: [];
    $socialIcons = [
        'website' => 'fa-globe',
        'facebook' => 'fa-facebook',
        'instagram' => 'fa-instagram',
        'youtube' => 'fa-youtube-play',
        'linkedin' => 'fa-linkedin',
        'twitter' => 'fa-twitter',
        'telegram' => 'fa-paper-plane',
    ];
    $authUser = auth()->user();
@endphp

@if(session('success'))
    <div class="ly-flash-success">{{ session('success') }}</div>
@endif

<section class="ly-cp-hero" @if($company->coverUrl()) style="--ly-cp-cover:url('{{ $company->coverUrl() }}')" @endif>
    <div class="ly-cp-hero-overlay"></div>
    <div class="ly-container ly-cp-hero-inner">
        <a class="ly-company-back" href="{{ route('website.companies.index') }}">← All institutes</a>
        <div class="ly-cp-hero-grid">
            <div class="ly-cp-brand-block">
                <div class="ly-company-avatar ly-company-avatar-xl">
                    @if($company->logoUrl())
                        <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}">
                    @else
                        <span>{{ $company->initials() }}</span>
                    @endif
                </div>
                <div>
                    <p class="ly-cp-eyebrow">Academy on {{ $brand }}</p>
                    <h1>{{ $company->name }}</h1>
                    @if($company->tagline)
                        <p class="ly-cp-lead">{{ $company->tagline }}</p>
                    @endif
                    <div class="ly-cp-chips">
                        @if(!empty($locationParts))
                            <span><i class="fa fa-map-marker"></i> {{ implode(', ', $locationParts) }}</span>
                        @endif
                        @if($founded)
                            <span><i class="fa fa-flag"></i> Est. {{ $founded }}</span>
                        @endif
                        <span><i class="fa fa-book"></i> {{ $courses->total() }} {{ Str::plural('course', $courses->total()) }}</span>
                        @if($hours)
                            <span><i class="fa fa-clock-o"></i> {{ $hours }}</span>
                        @endif
                    </div>
                    <div class="ly-hero-actions">
                        <a class="ly-btn ly-btn-green" href="#courses">Browse Courses</a>
                        @if($company->website_url)
                            <a class="ly-btn ly-btn-outline" href="{{ $company->website_url }}" target="_blank" rel="noopener">Visit Website</a>
                        @endif
                        <a class="ly-btn ly-btn-outline" href="#contact">Contact</a>
                    </div>
                </div>
            </div>
            @if(!empty($stats))
                <div class="ly-cp-stat-panel">
                    @foreach($stats as $stat)
                        <div class="ly-cp-stat">
                            <strong>{{ $stat['value'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>

<nav class="ly-cp-tabs" aria-label="Institute profile sections">
    <div class="ly-container">
        <div class="ly-cp-tabs-inner" role="tablist">
            <a href="#overview" class="is-active" data-cp-tab role="tab" aria-selected="true">Overview</a>
            <a href="#courses" data-cp-tab role="tab" aria-selected="false">Courses</a>
            @if($testimonials->count())<a href="#testimonials" data-cp-tab role="tab" aria-selected="false">Testimonials</a>@endif
            <a href="#reviews" data-cp-tab role="tab" aria-selected="false">Reviews</a>
            @if($gallery->count())<a href="#gallery" data-cp-tab role="tab" aria-selected="false">Gallery</a>@endif
            @if($videos->count())<a href="#videos" data-cp-tab role="tab" aria-selected="false">Videos</a>@endif
            @if($blogs->count())<a href="#blogs" data-cp-tab role="tab" aria-selected="false">Blogs</a>@endif
            @if($team->count())<a href="#team" data-cp-tab role="tab" aria-selected="false">Team</a>@endif
            @if(!empty($faqs))<a href="#faqs" data-cp-tab role="tab" aria-selected="false">FAQ</a>@endif
            <a href="#contact" data-cp-tab role="tab" aria-selected="false">Contact</a>
        </div>
    </div>
</nav>

<section class="ly-section" id="overview">
    <div class="ly-container">
        <div class="ly-cp-layout">
            <div class="ly-cp-main">
                <div class="ly-cp-card">
                    <p class="ly-tag">About the academy</p>
                    <h2>Who we are</h2>
                    @forelse($aboutParagraphs as $para)
                        <p>{{ $para }}</p>
                    @empty
                        <p>This academy is building its learning business on {{ $brand }}.</p>
                    @endforelse

                    @if(!empty($specialties))
                        <div class="ly-cp-tags">
                            @foreach($specialties as $item)
                                <span>{{ $item }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if($mission || $vision)
                    <div class="ly-cp-mv-grid">
                        @if($mission)
                            <div class="ly-cp-card ly-cp-card-soft">
                                <p class="ly-tag">Mission</p>
                                <h3>What drives us</h3>
                                <p>{{ $mission }}</p>
                            </div>
                        @endif
                        @if($vision)
                            <div class="ly-cp-card ly-cp-card-soft">
                                <p class="ly-tag">Vision</p>
                                <h3>Where we’re headed</h3>
                                <p>{{ $vision }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!empty($whyUs))
                    <div class="ly-cp-card">
                        <p class="ly-tag">Why learners choose us</p>
                        <h2>Built for real outcomes</h2>
                        <div class="ly-cp-why-grid">
                            @foreach($whyUs as $item)
                                <article class="ly-cp-why-item">
                                    <span class="ly-cp-why-icon"><i class="fa {{ $item['icon'] ?? 'fa-check-circle' }}"></i></span>
                                    <h4>{{ $item['title'] }}</h4>
                                    <p>{{ $item['text'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(!empty($highlights))
                    <div class="ly-cp-card">
                        <p class="ly-tag">Highlights</p>
                        <h2>At a glance</h2>
                        <ul class="ly-checklist">
                            @foreach($highlights as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <aside class="ly-cp-aside">
                <div class="ly-cp-card ly-cp-sticky">
                    <h3>Quick facts</h3>
                    <ul class="ly-company-contact">
                        @if($founded)<li><strong>Founded</strong><span>{{ $founded }}</span></li>@endif
                        @if(!empty($locationParts))<li><strong>Location</strong><span>{{ implode(', ', $locationParts) }}</span></li>@endif
                        @if($hours)<li><strong>Hours</strong><span>{{ $hours }}</span></li>@endif
                        <li><strong>Courses</strong><span>{{ $courses->total() }} published</span></li>
                        @if($company->email)<li><strong>Email</strong><span>{{ $company->email }}</span></li>@endif
                        @if($company->phone)<li><strong>Phone</strong><span>{{ $company->phone }}</span></li>@endif
                    </ul>
                    @if(!empty($social))
                        <div class="ly-company-social ly-cp-social">
                            @foreach($social as $network => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener" title="{{ ucfirst($network) }}">
                                    <i class="fa {{ $socialIcons[$network] ?? 'fa-link' }}"></i>
                                    <span>{{ ucfirst($network) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <a class="ly-btn ly-btn-green ly-cp-full-btn" href="#courses">Explore courses</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<section class="ly-section ly-section-soft" id="courses">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Courses</p>
            <h2>Learn with {{ $company->name }}</h2>
            <p>Published programs created by this academy on {{ $brand }}.</p>
        </div>

        @if($courses->count())
            <div class="ly-company-course-grid">
                @foreach($courses as $course)
                    <a href="{{ route('public.course', $course) }}" class="ly-company-course-card">
                        <div class="ly-company-course-media">
                            @if($course->thumbnail)
                                <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}">
                            @else
                                <span>{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="ly-company-course-body">
                            <div class="ly-company-course-badges">
                                @if($course->category)
                                    <span class="ly-tag">{{ $course->category->name }}</span>
                                @endif
                                @if($course->access_type === 'trial')
                                    <span class="ly-course-badge ly-course-badge-trial">Trial</span>
                                @elseif($course->is_free || $course->access_type === 'free')
                                    <span class="ly-course-badge ly-course-badge-free">Free</span>
                                @else
                                    <span class="ly-course-badge ly-course-badge-paid">Paid</span>
                                @endif
                            </div>
                            <h3>{{ $course->title }}</h3>
                            <p>{{ Str::limit(strip_tags($course->subtitle ?: $course->description ?: ''), 110) }}</p>
                            <div class="ly-cp-course-foot">
                                <strong>
                                    {{ $course->displayPrice() }}
                                    @if($course->hasDiscount())
                                        <s>₹{{ number_format((float) $course->price, 0) }}</s>
                                    @endif
                                </strong>
                                <span>View details →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="ly-center" style="margin-top:32px;">
                {{ $courses->links() }}
            </div>
        @else
            <div class="ly-empty-state">
                <h3>No published courses yet</h3>
                <p>{{ $company->name }} has not published courses on their public profile.</p>
            </div>
        @endif
    </div>
</section>

@if($testimonials->count())
<section class="ly-section" id="testimonials">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Testimonials</p>
            <h2>What learners say</h2>
            <p>Stories shared by students and parents about {{ $company->name }}.</p>
        </div>
        <div class="ly-cp-testimonial-grid">
            @foreach($testimonials as $item)
                <article class="ly-cp-testimonial-card">
                    <div class="ly-cp-stars">@for($i=1;$i<=5;$i++)<i class="fa fa-star{{ $i <= $item->rating ? '' : '-o' }}"></i>@endfor</div>
                    <p>“{{ $item->content }}”</p>
                    <div class="ly-cp-testimonial-author">
                        @if($item->avatarUrl())
                            <img src="{{ $item->avatarUrl() }}" alt="{{ $item->author_name }}">
                        @else
                            <span>{{ strtoupper(substr($item->author_name, 0, 1)) }}</span>
                        @endif
                        <div>
                            <strong>{{ $item->author_name }}</strong>
                            @if($item->author_title)<em>{{ $item->author_title }}</em>@endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="ly-section ly-section-soft" id="reviews">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Reviews</p>
            <h2>Student reviews</h2>
            <p>
                @if($reviewCount)
                    {{ $avgRating }}/5 average from {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}.
                @else
                    Be the first to review {{ $company->name }}.
                @endif
            </p>
        </div>

        <div class="ly-cp-review-layout">
            <div class="ly-cp-review-list">
                @forelse($reviews as $review)
                    <article class="ly-cp-review-card">
                        <div class="ly-cp-stars">@for($i=1;$i<=5;$i++)<i class="fa fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>@endfor</div>
                        <p>{{ $review->content }}</p>
                        <strong>{{ $review->reviewer_name }}</strong>
                        <span>{{ optional($review->created_at)->format('M d, Y') }}</span>
                    </article>
                @empty
                    <div class="ly-empty-state">
                        <h3>No reviews yet</h3>
                        <p>Share your experience with this academy.</p>
                    </div>
                @endforelse
            </div>

            <div class="ly-cp-card">
                <h3>Write a review</h3>
                <p>Students and visitors can submit a review. It appears after academy approval.</p>
                <form method="POST" action="{{ route('website.companies.reviews.store', $company->slug) }}" class="ly-cp-form">
                    @csrf
                    <label>Your name<input type="text" name="reviewer_name" value="{{ old('reviewer_name', $authUser?->name) }}" required></label>
                    <label>Email<input type="email" name="reviewer_email" value="{{ old('reviewer_email', $authUser?->email) }}"></label>
                    <label>Rating
                        <select name="rating" required>
                            @for($r=5;$r>=1;$r--)
                                <option value="{{ $r }}" @selected(old('rating', 5) == $r)>{{ $r }} star{{ $r > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </label>
                    <label>Review<textarea name="content" rows="4" required>{{ old('content') }}</textarea></label>
                    <button type="submit" class="ly-btn ly-btn-green">Submit review</button>
                </form>
            </div>
        </div>
    </div>
</section>

@if($gallery->count())
<section class="ly-section" id="gallery">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Gallery</p>
            <h2>Inside the academy</h2>
            <p>A look at learning spaces, sessions, and community moments.</p>
        </div>
        <div class="ly-cp-gallery" id="lyCompanyGallery">
            @foreach($gallery as $item)
                <figure class="ly-cp-gallery-item">
                    <button
                        type="button"
                        class="ly-cp-gallery-link strip"
                        data-ly-gallery-index="{{ $loop->index }}"
                        data-ly-gallery-src="{{ $item->imageUrl() }}"
                        data-ly-gallery-caption="{{ $item->caption }}"
                        aria-label="Open gallery image {{ $loop->iteration }}"
                    >
                        <img src="{{ $item->imageUrl() }}" alt="{{ $item->caption ?: $company->name }}">
                        <span class="ly-cp-gallery-zoom" aria-hidden="true"><i class="fa fa-search"></i></span>
                    </button>
                    @if($item->caption)
                        <figcaption>{{ $item->caption }}</figcaption>
                    @endif
                </figure>
            @endforeach
        </div>

        <div class="ly-lightbox" id="lyCompanyLightbox" hidden>
            <div class="ly-lightbox-backdrop" data-ly-lightbox-close></div>
            <div class="ly-lightbox-dialog" role="dialog" aria-modal="true" aria-label="Gallery preview">
                <button type="button" class="ly-lightbox-close" data-ly-lightbox-close aria-label="Close">&times;</button>
                @if($gallery->count() > 1)
                    <button type="button" class="ly-lightbox-nav ly-lightbox-prev" data-ly-lightbox-prev aria-label="Previous">‹</button>
                    <button type="button" class="ly-lightbox-nav ly-lightbox-next" data-ly-lightbox-next aria-label="Next">›</button>
                @endif
                <div class="ly-lightbox-counter" id="lyLightboxCounter"></div>
                <img src="" alt="" class="ly-lightbox-image" id="lyLightboxImage">
                <p class="ly-lightbox-caption" id="lyLightboxCaption" hidden></p>
            </div>
        </div>
    </div>
</section>
@endif

@if($videos->count())
<section class="ly-section ly-section-soft" id="videos">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Videos</p>
            <h2>Watch & learn</h2>
            <p>Campus stories, intros, and learning highlights from {{ $company->name }}.</p>
        </div>
        <div class="ly-cp-video-grid">
            @foreach($videos as $video)
                <article class="ly-cp-video-card">
                    <div class="ly-cp-video-frame gdlr-core-fluid-video-wrapper">
                        <iframe
                            src="{{ $video->embedUrl() }}"
                            title="{{ $video->title }}"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                    <h3>{{ $video->title }}</h3>
                    @if($video->description)<p>{{ $video->description }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($blogs->count())
<section class="ly-section" id="blogs">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Blogs</p>
            <h2>From the academy</h2>
            <p>Articles and updates published by {{ $company->name }}.</p>
        </div>
        <div class="ly-cp-blog-grid">
            @foreach($blogs as $blog)
                <a href="{{ route('website.companies.blog', [$company->slug, $blog->slug]) }}" class="ly-cp-blog-card">
                    <div class="ly-cp-blog-media">
                        @if($blog->coverUrl())
                            <img src="{{ $blog->coverUrl() }}" alt="{{ $blog->title }}">
                        @endif
                    </div>
                    <div class="ly-cp-blog-body">
                        <h3>{{ $blog->title }}</h3>
                        <p>{{ $blog->excerpt ?: Str::limit(strip_tags($blog->body ?? ''), 120) }}</p>
                        <span>Read more →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($team->count())
<section class="ly-section ly-section-soft" id="team">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Team</p>
            <h2>People behind the learning</h2>
            <p>Instructors and leaders shaping the {{ $company->name }} experience.</p>
        </div>
        <div class="ly-cp-team-grid">
            @foreach($team as $member)
                <article class="ly-cp-team-card">
                    <div class="ly-cp-team-photo">
                        @if($member->photoUrl())
                            <img src="{{ $member->photoUrl() }}" alt="{{ $member->name }}">
                        @else
                            <span>{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <h3>{{ $member->name }}</h3>
                    @if($member->role)<p class="ly-cp-team-role">{{ $member->role }}</p>@endif
                    @if($member->bio)<p>{{ $member->bio }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($faqs))
<section class="ly-section" id="faqs">
    <div class="ly-container ly-cp-faq-wrap">
        <div class="ly-section-head">
            <p class="ly-tag">FAQ</p>
            <h2>Common questions</h2>
            <p>Answers for learners exploring {{ $company->name }}.</p>
        </div>
        <div class="ly-cp-faq-list">
            @foreach($faqs as $i => $faq)
                <details class="ly-cp-faq" @if($i === 0) open @endif>
                    <summary>{{ $faq['q'] }}</summary>
                    <p>{{ $faq['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="ly-section ly-section-soft" id="contact">
    <div class="ly-container">
        <div class="ly-cp-contact-grid">
            <div class="ly-cp-card">
                <p class="ly-tag">Contact</p>
                <h2>Get in touch</h2>
                <p>Reach {{ $company->name }} for admissions, partnerships, or course guidance.</p>
                <ul class="ly-company-contact ly-cp-contact-list">
                    @if($company->email)<li><strong>Email</strong><a href="mailto:{{ $company->email }}">{{ $company->email }}</a></li>@endif
                    @if($company->phone)<li><strong>Phone</strong><a href="tel:{{ preg_replace('/\s+/', '', $company->phone) }}">{{ $company->phone }}</a></li>@endif
                    @if($company->address)<li><strong>Address</strong><span>{{ $company->address }}@if(!empty($locationParts)), {{ implode(', ', $locationParts) }}@endif</span></li>@endif
                    @if($hours)<li><strong>Working hours</strong><span>{{ $hours }}</span></li>@endif
                    @if($company->website_url)<li><strong>Website</strong><a href="{{ $company->website_url }}" target="_blank" rel="noopener">{{ $company->website_url }}</a></li>@endif
                </ul>
                @if(!empty($social))
                    <div class="ly-company-social ly-cp-social">
                        @foreach($social as $network => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener">
                                <i class="fa {{ $socialIcons[$network] ?? 'fa-link' }}"></i>
                                <span>{{ ucfirst($network) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="ly-cp-card">
                <h3>Send an enquiry</h3>
                <p>Ask about courses, admissions, or partnerships.</p>
                <form method="POST" action="{{ route('website.companies.enquiries.store', $company->slug) }}" class="ly-cp-form">
                    @csrf
                    <label>Name<input type="text" name="name" value="{{ old('name', $authUser?->name) }}" required></label>
                    <label>Email<input type="email" name="email" value="{{ old('email', $authUser?->email) }}" required></label>
                    <label>Phone<input type="text" name="phone" value="{{ old('phone', $authUser?->phone) }}"></label>
                    <label>Subject<input type="text" name="subject" value="{{ old('subject') }}" placeholder="Course enquiry"></label>
                    <label>Message<textarea name="message" rows="4" required>{{ old('message') }}</textarea></label>
                    <button type="submit" class="ly-btn ly-btn-green">Send enquiry</button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="ly-cta-banner">
    <div class="ly-container">
        <h2>Build your academy like {{ $company->name }}</h2>
        <p>Create your branded school, publish courses, and grow enrollments with {{ $brand }}.</p>
        <div class="ly-cta-actions">
            <a class="ly-btn ly-btn-green" href="{{ url($cta['trial'] ?? '/signup') }}">Start Free Trial</a>
            <a class="ly-btn ly-btn-outline" href="{{ url($cta['demo'] ?? '/product-demo') }}">Book a Demo</a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.ly-cp-gallery-link {
    width: 100%;
    border: 0;
    padding: 0;
    background: transparent;
    font: inherit;
    text-align: left;
}
.ly-lightbox[hidden] { display: none !important; }
.ly-lightbox {
    position: fixed !important;
    inset: 0 !important;
    z-index: 2000000 !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
    padding: 96px 24px 32px;
    box-sizing: border-box;
}
.ly-lightbox-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, .88);
}
.ly-lightbox-dialog {
    position: relative;
    z-index: 1;
    width: min(960px, 100%);
    max-height: calc(100vh - 128px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.ly-lightbox-image {
    display: block;
    max-width: 100%;
    max-height: min(70vh, 760px);
    width: auto;
    height: auto;
    object-fit: contain;
    border-radius: 12px;
    background: #000;
    box-shadow: 0 24px 80px rgba(0,0,0,.45);
}
.ly-lightbox-caption {
    margin: 14px 0 0;
    color: #fff;
    text-align: center;
    font-size: 15px;
    font-weight: 500;
    text-shadow: 0 1px 3px rgba(0,0,0,.45);
}
.ly-lightbox-counter {
    position: absolute;
    top: -6px;
    left: 0;
    color: rgba(255,255,255,.85);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .02em;
}
.ly-lightbox-close {
    position: absolute;
    top: -8px;
    right: -4px;
    z-index: 2;
    width: 40px;
    height: 40px;
    border: 0;
    border-radius: 50%;
    background: rgba(255,255,255,.14);
    color: #fff;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
}
.ly-lightbox-close:hover { background: rgba(255,255,255,.24); }
.ly-lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    width: 48px;
    height: 48px;
    border: 0;
    border-radius: 50%;
    background: rgba(255,255,255,.14);
    color: #fff;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
}
.ly-lightbox-nav:hover { background: rgba(255,255,255,.28); }
.ly-lightbox-prev { left: -8px; }
.ly-lightbox-next { right: -8px; }
body.ly-lightbox-open { overflow: hidden !important; }
body.ly-lightbox-open .kingster-header-wrap,
body.ly-lightbox-open .kingster-mobile-header,
body.ly-lightbox-open .kingster-top-bar,
body.ly-lightbox-open .kingster-sticky-navigation {
    z-index: 1 !important;
}
@media (max-width: 767px) {
    .ly-lightbox { padding: 72px 16px 20px; }
    .ly-lightbox-dialog { max-height: calc(100vh - 92px); }
    .ly-lightbox-image { max-height: min(62vh, 640px); }
    .ly-lightbox-prev { left: 4px; }
    .ly-lightbox-next { right: 4px; }
    .ly-lightbox-close { top: 0; right: 0; }
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    var tabs = document.querySelectorAll('[data-cp-tab]');
    if (tabs.length) {
        var sections = Array.prototype.map.call(tabs, function (tab) {
            return document.querySelector(tab.getAttribute('href'));
        }).filter(Boolean);

        function setActive() {
            var y = window.scrollY + 140;
            var current = sections[0];
            sections.forEach(function (section) {
                if (section.offsetTop <= y) current = section;
            });
            tabs.forEach(function (tab) {
                var active = tab.getAttribute('href') === '#' + current.id;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }

        window.addEventListener('scroll', setActive, { passive: true });
        setActive();
    }

    var triggers = Array.prototype.slice.call(document.querySelectorAll('[data-ly-gallery-index]'));
    var lightbox = document.getElementById('lyCompanyLightbox');
    if (!triggers.length || !lightbox) return;

    var imageEl = document.getElementById('lyLightboxImage');
    var captionEl = document.getElementById('lyLightboxCaption');
    var counterEl = document.getElementById('lyLightboxCounter');
    var items = triggers.map(function (trigger) {
        return {
            src: trigger.getAttribute('data-ly-gallery-src') || '',
            caption: trigger.getAttribute('data-ly-gallery-caption') || '',
            alt: (trigger.querySelector('img') && trigger.querySelector('img').getAttribute('alt')) || ''
        };
    });
    var index = 0;

    function render() {
        var item = items[index];
        if (!item) return;
        imageEl.src = item.src;
        imageEl.alt = item.alt || item.caption || 'Gallery image';
        if (item.caption) {
            captionEl.hidden = false;
            captionEl.textContent = item.caption;
        } else {
            captionEl.hidden = true;
            captionEl.textContent = '';
        }
        if (counterEl) {
            counterEl.textContent = (index + 1) + ' / ' + items.length;
        }
    }

    function openAt(i) {
        index = ((i % items.length) + items.length) % items.length;
        render();
        if (lightbox.parentElement !== document.body) {
            document.body.appendChild(lightbox);
        }
        lightbox.hidden = false;
        document.body.classList.add('ly-lightbox-open');
    }

    function closeLightbox() {
        lightbox.hidden = true;
        document.body.classList.remove('ly-lightbox-open');
        imageEl.removeAttribute('src');
    }

    function next() { openAt(index + 1); }
    function prev() { openAt(index - 1); }

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var i = parseInt(trigger.getAttribute('data-ly-gallery-index'), 10) || 0;
            openAt(i);
        });
    });

    lightbox.querySelectorAll('[data-ly-lightbox-close]').forEach(function (el) {
        el.addEventListener('click', closeLightbox);
    });
    var prevBtn = lightbox.querySelector('[data-ly-lightbox-prev]');
    var nextBtn = lightbox.querySelector('[data-ly-lightbox-next]');
    if (prevBtn) prevBtn.addEventListener('click', function (e) { e.stopPropagation(); prev(); });
    if (nextBtn) nextBtn.addEventListener('click', function (e) { e.stopPropagation(); next(); });

    document.addEventListener('keydown', function (e) {
        if (lightbox.hidden) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prev();
        if (e.key === 'ArrowRight') next();
    });
})();
</script>
@endpush
