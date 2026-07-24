@extends('website.layouts.app')

@section('title', ($course->seo_title ?: $course->title).' – '.config('website.brand'))
@section('meta_description', $course->seo_description ?: Str::limit(strip_tags($course->description ?? $course->subtitle ?? ''), 160))

@section('content')
@php
    $authUser = auth()->user();
    $isLearner = $authUser && $authUser->isLearner();
    $coursePath = route('public.course', $course, false);
    $loginUrl = route('student.login', ['redirect' => $coursePath]);

    if ($isEnrolled ?? false) {
        $ctaMode = 'access';
        $enrollLabel = 'Go to course';
    } elseif ($isLearner) {
        $ctaMode = 'checkout';
        $enrollLabel = $course->requiresPayment()
            ? 'Buy now — '.$course->displayPrice()
            : 'Enroll for free';
    } else {
        $ctaMode = 'login';
        $enrollLabel = $course->requiresPayment() ? 'Login to buy' : 'Login to enroll';
    }
@endphp

<section class="ly-course-hero">
    <div class="ly-course-hero-glow" aria-hidden="true"></div>
    <div class="ly-container ly-course-hero-inner">
        @if($course->category)
            <p class="ly-product-eyebrow">{{ $course->category->name }}</p>
        @endif
        <h1>{{ $course->title }}</h1>
        <div class="ly-course-meta-row">
            @if($reviewCount > 0)
                <span class="ly-course-rating">
                    <strong>{{ number_format($avgRating, 1) }}</strong>
                    <span class="ly-cp-stars">{{ str_repeat('★', (int) round($avgRating)) }}{{ str_repeat('☆', 5 - (int) round($avgRating)) }}</span>
                    <em>({{ $reviewCount }} {{ Str::plural('review', $reviewCount) }})</em>
                </span>
            @endif
            <span><i class="fa fa-book"></i> {{ $lessonCount }} {{ Str::plural('lesson', $lessonCount) }}</span>
            <span><i class="fa fa-list"></i> {{ $sectionCount }} {{ Str::plural('section', $sectionCount) }}</span>
            @if($course->enrollment_count)
                <span><i class="fa fa-users"></i> {{ number_format($course->enrollment_count) }} enrolled</span>
            @endif
            @if($institute)
                <a class="ly-course-institute-inline" href="{{ route('website.companies.show', $institute->slug) }}">
                    <i class="fa fa-university"></i> {{ $institute->name }}
                </a>
            @endif
        </div>
    </div>
</section>

<section class="ly-section ly-course-body">
    <div class="ly-container ly-course-layout">
        <div class="ly-course-main">
            <div class="ly-course-panel">
                <h2>About this course</h2>
                @if($course->subtitle)
                    <p class="ly-course-panel-lead">{{ $course->subtitle }}</p>
                @endif
                @if($course->description)
                    <div class="ly-course-prose">{!! nl2br(e($course->description)) !!}</div>
                @else
                    <p class="ly-muted">Course overview will be published soon.</p>
                @endif
            </div>

            <div class="ly-course-panel" id="curriculum">
                <h2>Curriculum</h2>
                <p class="ly-course-panel-lead">{{ $sectionCount }} sections · {{ $lessonCount }} lessons</p>
                @forelse($course->sections as $section)
                    <details class="ly-course-section" {{ $loop->first ? 'open' : '' }}>
                        <summary>
                            <span>{{ $section->title }}</span>
                            <em>{{ $section->lessons->count() }} {{ Str::plural('lesson', $section->lessons->count()) }}</em>
                        </summary>
                        <ul>
                            @foreach($section->lessons as $lesson)
                                <li>
                                    <span class="ly-course-lesson-num">{{ $loop->iteration }}</span>
                                    <span class="ly-course-lesson-title">{{ $lesson->title }}</span>
                                    @if($lesson->is_preview)
                                        <span class="ly-course-preview">Preview</span>
                                    @endif
                                    @if(!empty($lesson->duration_minutes))
                                        <span class="ly-course-duration">{{ $lesson->duration_minutes }} min</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @empty
                    <p class="ly-muted">Curriculum coming soon.</p>
                @endforelse
            </div>

            @if($course->instructors->count())
                <div class="ly-course-panel">
                    <h2>Instructors</h2>
                    <div class="ly-course-instructor-grid">
                        @foreach($course->instructors as $instructor)
                            <div class="ly-course-instructor">
                                <span class="ly-course-instructor-avatar">{{ Str::upper(Str::substr($instructor->name, 0, 1)) }}</span>
                                <div>
                                    <strong>{{ $instructor->name }}</strong>
                                    @if($instructor->pivot?->is_primary)
                                        <em>Lead instructor</em>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($course->faqs->count())
                <div class="ly-course-panel" id="faqs">
                    <h2>FAQs</h2>
                    <div class="ly-product-faq">
                        @foreach($course->faqs as $faq)
                            <details class="ly-product-faq-item">
                                <summary>{{ $faq->question }}</summary>
                                <div class="ly-product-faq-body">{{ $faq->answer }}</div>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="ly-course-panel" id="reviews">
                <div class="ly-course-panel-head">
                    <div>
                        <h2>Learner reviews</h2>
                        @if($reviewCount > 0)
                            <p class="ly-course-panel-lead">
                                Average {{ number_format($avgRating, 1) }} / 5 from {{ $reviewCount }} {{ Str::plural('review', $reviewCount) }}
                            </p>
                        @else
                            <p class="ly-course-panel-lead">Be the first to review this course.</p>
                        @endif
                    </div>
                </div>

                @if(session('success') && str_contains(session('success'), 'review'))
                    <div class="ly-flash-success">{{ session('success') }}</div>
                @endif

                <div class="ly-course-review-grid">
                    @forelse($reviews as $review)
                        <article class="ly-cp-review-card">
                            <div class="ly-cp-stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                            <p>{{ $review->review }}</p>
                            <div class="ly-cp-testimonial-author">
                                <span>{{ Str::upper(Str::substr($review->displayName(), 0, 1)) }}</span>
                                <div>
                                    <strong>{{ $review->displayName() }}</strong>
                                    <em style="display:block;color:#94a3b8;font-size:12px;font-style:normal;">{{ optional($review->created_at)->diffForHumans() }}</em>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="ly-muted">No public reviews yet.</p>
                    @endforelse
                </div>

                <div class="ly-course-form-card" id="write-review">
                    <h3>Write a review</h3>
                    <p>Share your experience with this course. Reviews appear after moderation.</p>
                    @if($errors->any() && old('_form') === 'review')
                        <div class="ly-auth-error">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="{{ route('public.course.reviews.store', $course) }}" class="ly-cp-form">
                        @csrf
                        <input type="hidden" name="_form" value="review">
                        <div class="ly-cp-form-row">
                            <label>
                                <span>Your name*</span>
                                <input type="text" name="reviewer_name" value="{{ old('reviewer_name', $authUser?->name) }}" required>
                            </label>
                            <label>
                                <span>Email</span>
                                <input type="email" name="reviewer_email" value="{{ old('reviewer_email', $authUser?->email) }}">
                            </label>
                        </div>
                        <label>
                            <span>Rating*</span>
                            <select name="rating" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('rating', 5) == $i)>{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                @endfor
                            </select>
                        </label>
                        <label>
                            <span>Your review*</span>
                            <textarea name="review" rows="4" required placeholder="What did you like about this course?">{{ old('review') }}</textarea>
                        </label>
                        <button type="submit" class="ly-btn ly-btn-green">Submit review</button>
                    </form>
                </div>
            </div>
        </div>

        <aside class="ly-course-side">
            <div class="ly-course-side-card ly-course-enroll-card">
                <div class="ly-course-thumb">
                    @if($course->thumbnailUrl())
                        <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}">
                    @else
                        <div class="ly-course-thumb-fallback">{{ Str::upper(Str::substr($course->title, 0, 1)) }}</div>
                    @endif
                </div>
                <div class="ly-course-price-block">
                    <div class="ly-course-price">
                        <strong>{{ $course->displayPrice() }}</strong>
                        @if($course->hasDiscount())
                            <s>₹{{ number_format((float) $course->price, 0) }}</s>
                        @endif
                    </div>
                    <p>{{ $course->requiresPayment() ? ucfirst(str_replace('_', ' ', $course->access_type ?: 'paid')).' access' : 'Free access' }}</p>
                </div>

                @if(session('error'))
                    <div class="ly-auth-error" style="margin-bottom:12px">{{ session('error') }}</div>
                @endif
                @if(session('success') && ! str_contains((string) session('success'), 'Enquiry') && ! str_contains((string) session('success'), 'review'))
                    <div class="ly-flash-success" style="margin-bottom:12px">{{ session('success') }}</div>
                @endif

                @if($ctaMode === 'access')
                    <a class="ly-btn ly-btn-green ly-course-enroll" href="{{ route('learner.courses.show', $course) }}">{{ $enrollLabel }}</a>
                @elseif($ctaMode === 'checkout')
                    <form method="POST" action="{{ route('courses.checkout.start', $course) }}">
                        @csrf
                        <button type="submit" class="ly-btn ly-btn-green ly-course-enroll" style="width:100%">{{ $enrollLabel }}</button>
                    </form>
                @else
                    <a class="ly-btn ly-btn-green ly-course-enroll" href="{{ $loginUrl }}">{{ $enrollLabel }}</a>
                    <p class="ly-course-side-lead" style="margin-top:10px">
                        New student?
                        <a href="{{ route('student.register', ['redirect' => $coursePath]) }}">Create account</a>
                    </p>
                @endif
                <ul class="ly-course-card-points">
                    <li><i class="fa fa-check"></i> {{ $lessonCount }} lessons · {{ $sectionCount }} sections</li>
                    <li><i class="fa fa-check"></i> Learn at your pace</li>
                    <li><i class="fa fa-check"></i> Expert-led content</li>
                </ul>
            </div>

            <div class="ly-course-side-card" id="enquiry">
                <h3>Course enquiry</h3>
                <p class="ly-course-side-lead">Ask about syllabus, fees, or batches for <strong>{{ $course->title }}</strong>.</p>

                @if(session('success') && str_contains(session('success'), 'Enquiry'))
                    <div class="ly-flash-success">{{ session('success') }}</div>
                @endif
                @if($errors->any() && old('_form') === 'enquiry')
                    <div class="ly-auth-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('public.course.enquiries.store', $course) }}" class="ly-cp-form ly-course-enquiry-form">
                    @csrf
                    <input type="hidden" name="_form" value="enquiry">
                    <label>
                        <span>Name*</span>
                        <input type="text" name="name" value="{{ old('name', $authUser?->name) }}" required>
                    </label>
                    <label>
                        <span>Email*</span>
                        <input type="email" name="email" value="{{ old('email', $authUser?->email) }}" required>
                    </label>
                    <label>
                        <span>Phone</span>
                        <input type="text" name="phone" value="{{ old('phone') }}">
                    </label>
                    <label>
                        <span>Subject</span>
                        <input type="text" name="subject" value="{{ old('subject', 'Enquiry about '.$course->title) }}">
                    </label>
                    <label>
                        <span>Message*</span>
                        <textarea name="message" rows="4" required placeholder="Your question...">{{ old('message') }}</textarea>
                    </label>
                    <button type="submit" class="ly-btn ly-btn-green" style="width:100%;justify-content:center;">Send enquiry</button>
                </form>
            </div>

            @if($institute)
                <a class="ly-course-side-card ly-course-side-institute" href="{{ route('website.companies.show', $institute->slug) }}">
                    <p class="ly-tag">Institute</p>
                    <strong>{{ $institute->name }}</strong>
                    <span>View academy profile →</span>
                </a>
            @endif
        </aside>
    </div>
</section>

@if($relatedCourses->count())
<section class="ly-section ly-section-soft" id="related">
    <div class="ly-container">
        <div class="ly-section-head">
            <p class="ly-tag">Related courses</p>
            <h2>You may also like</h2>
            <p>More courses from the same category or academy.</p>
        </div>
        <div class="ly-course-related-grid">
            @foreach($relatedCourses as $related)
                <a href="{{ route('public.course', $related) }}" class="ly-course-related-card">
                    <div class="ly-course-related-media">
                        @if($related->thumbnailUrl())
                            <img src="{{ $related->thumbnailUrl() }}" alt="{{ $related->title }}">
                        @endif
                    </div>
                    <div class="ly-course-related-body">
                        @if($related->category)
                            <span class="ly-tag">{{ $related->category->name }}</span>
                        @endif
                        <h3>{{ $related->title }}</h3>
                        <p>{{ Str::limit(strip_tags($related->subtitle ?: $related->description), 90) }}</p>
                        <strong>{{ $related->displayPrice() }}</strong>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
