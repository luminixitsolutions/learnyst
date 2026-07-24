<a href="{{ route('public.course', $course) }}" class="ly-company-course-card">
    <div class="ly-company-course-media">
        @if($course->thumbnailUrl())
            <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}">
        @else
            <span>{{ strtoupper(substr($course->title, 0, 2)) }}</span>
        @endif
    </div>
    <div class="ly-company-course-body">
        <div class="ly-company-course-badges">
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
        <div class="ly-course-card-meta">
            <span>{{ (int) ($course->lessons_count ?? 0) }} lessons</span>
            @if(!empty($course->approved_reviews_count))
                <span>{{ number_format((float) ($course->avg_rating ?? 0), 1) }}★ ({{ $course->approved_reviews_count }})</span>
            @endif
        </div>
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
