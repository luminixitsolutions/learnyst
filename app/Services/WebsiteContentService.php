<?php

namespace App\Services;

use App\Models\WebsiteContent;

class WebsiteContentService
{
    public static function sections(): array
    {
        return [
            'brand' => [
                'label' => 'Brand & Contact',
                'group' => 'general',
                'description' => 'Site name, tagline, email, phone, and address shown across the marketing site.',
                'sort' => 10,
            ],
            'slides' => [
                'label' => 'Hero Slider',
                'group' => 'home',
                'description' => 'Homepage hero images, titles, and supporting text.',
                'sort' => 20,
            ],
            'video' => [
                'label' => 'Home Video',
                'group' => 'home',
                'description' => 'YouTube video shown under the hero.',
                'sort' => 30,
            ],
            'stats' => [
                'label' => 'Stats Banner',
                'group' => 'home',
                'description' => 'Big numbers under partner logos.',
                'sort' => 40,
            ],
            'partners' => [
                'label' => 'Partner Logos',
                'group' => 'home',
                'description' => 'Institutes / partner logos on the homepage.',
                'sort' => 50,
            ],
            'platform' => [
                'label' => 'Platform Section',
                'group' => 'home',
                'description' => 'All-in-one platform heading and feature cards.',
                'sort' => 60,
            ],
            'marketing' => [
                'label' => 'Marketing Section',
                'group' => 'home',
                'description' => 'Marketing tools split section.',
                'sort' => 70,
            ],
            'drm' => [
                'label' => 'DRM Section',
                'group' => 'home',
                'description' => 'Content protection / DRM section.',
                'sort' => 80,
            ],
            'apps' => [
                'label' => 'Branded Apps',
                'group' => 'home',
                'description' => 'Dark branded apps call-to-action band.',
                'sort' => 90,
            ],
            'domains' => [
                'label' => 'Education Domains',
                'group' => 'home',
                'description' => 'Exam prep, trading, coding, lifestyle cards.',
                'sort' => 100,
            ],
            'support' => [
                'label' => 'Support Section',
                'group' => 'home',
                'description' => 'Customer satisfaction and support cards.',
                'sort' => 110,
            ],
            'testimonials' => [
                'label' => 'Testimonials',
                'group' => 'social',
                'description' => 'Customer quotes shown on home and testimonials page.',
                'sort' => 120,
            ],
            'success_stories' => [
                'label' => 'Success Stories',
                'group' => 'social',
                'description' => 'Educator success story cards.',
                'sort' => 130,
            ],
            'cta' => [
                'label' => 'Final CTA',
                'group' => 'home',
                'description' => 'Bottom get-started banner.',
                'sort' => 140,
            ],
        ];
    }

    public static function defaults(string $key): array
    {
        return match ($key) {
            'brand' => [
                'name' => config('website.brand', 'StudyNest'),
                'tagline' => config('website.tagline', 'The Most Secure LMS to Sell Courses Online'),
                'email' => config('website.email', 'hello@studynest.com'),
                'phone' => config('website.phone', '080 4736 1000'),
                'address' => implode("\n", config('website.address', [])),
            ],
            'slides' => [
                'items' => [
                    [
                        'image' => 'website/upload/shutterstock_218235004-800x533.jpg',
                        'title' => 'Discover Why Top Institutes Choose StudyNest For Unbeatable Growth',
                        'text' => 'Protect your content, grow your student base, and lead with confidence on the most secure LMS built for educators.',
                        'is_active' => true,
                    ],
                    [
                        'image' => 'website/upload/shutterstock_481869205-800x405.jpg',
                        'title' => 'Launch and Scale Your Academy From One Platform',
                        'text' => 'Create courses, run mock tests, host live classes, and market your brand — all from a single secure learning platform.',
                        'is_active' => true,
                    ],
                    [
                        'image' => 'website/upload/shutterstock_361397258-800x533.jpg',
                        'title' => 'Best-in-Class Content Security for Educators',
                        'text' => 'Stop piracy with screenshot blocking, device limits, parallel login restriction, OTP checks, and watch-time controls.',
                        'is_active' => true,
                    ],
                    [
                        'image' => 'website/upload/annie-spratt-294450-unsplash.jpg',
                        'title' => 'Your Own Branded Apps, Built for Learning',
                        'text' => 'Give learners a polished mobile experience with your logo, colors, and secure content delivery.',
                        'is_active' => true,
                    ],
                    [
                        'image' => 'website/upload/shutterstock_734589535-800x534.jpg',
                        'title' => 'Empower Every Student to Learn Without Limits',
                        'text' => 'Deliver engaging lessons, track progress, and support learners across web and mobile — wherever they study.',
                        'is_active' => true,
                    ],
                ],
            ],
            'video' => [
                'youtube_id' => '0q4mL4wqgSo',
            ],
            'stats' => [
                'items' => [
                    ['value' => '1100 Cr+', 'label' => 'Earned by Educators'],
                    ['value' => '11,994', 'label' => 'Institutes Served'],
                    ['value' => '40%', 'label' => 'Engagement Increase'],
                ],
            ],
            'partners' => [
                'items' => [
                    ['image' => 'website/upload/partners/2iim.png', 'name' => '2IIM'],
                    ['image' => 'website/upload/partners/wizako.png', 'name' => 'Wizako'],
                    ['image' => 'website/upload/partners/raja-rami.png', 'name' => 'Raja Rami'],
                    ['image' => 'website/upload/partners/deeksha-vedantu.png', 'name' => 'Deeksha Vedantu'],
                    ['image' => 'website/upload/partners/physics-wallah.svg', 'name' => 'Physics Wallah'],
                    ['image' => 'website/upload/partners/sleepy-classes.png', 'name' => 'Sleepy Classes'],
                ],
            ],
            'platform' => [
                'heading_green' => 'All-in-One',
                'heading_blue' => 'Platform',
                'heading_rest' => 'to Launch and Scale Your Academy',
                'subheading' => 'StudyNest offers the complete toolkit you need to create, manage, and market your online courses.',
                'items' => [
                    ['slug' => 'sell-online-courses', 'title' => 'Course Builder', 'desc' => 'Convert your knowledge into valuable assets.', 'image' => 'website/upload/platform/course.png', 'bg' => 'linear-gradient(149deg, #ccfcd9 10%, #f5fce8 76%)'],
                    ['slug' => 'manage-batches-cohorts', 'title' => 'Batch or Cohort', 'desc' => 'Bring the classroom back and engage learners.', 'image' => 'website/upload/platform/batch.png', 'bg' => 'linear-gradient(326deg, #f0ecff 28%, #c5b8ff 90%)'],
                    ['slug' => 'sell-mock-tests', 'title' => 'Mock Tests', 'desc' => 'Create exam-ready mock tests and test series for students.', 'image' => 'website/upload/platform/digital.png', 'bg' => 'linear-gradient(330deg, #fff7ed 15%, #fdba74 88%)'],
                    ['slug' => 'branded-website-builder', 'title' => 'Branded Website', 'desc' => 'Build and manage your branded website. No code, no worries.', 'image' => 'website/upload/platform/website.png', 'bg' => 'linear-gradient(327deg, #f7f7f7 11%, #fac0d6 96%)'],
                    ['slug' => 'branded-mobile-app', 'title' => 'Branded App', 'desc' => 'Customised apps to reach learners anytime.', 'image' => 'website/upload/platform/app.png', 'bg' => 'linear-gradient(327deg, #ebeced 0%, #aac2f2 100%)'],
                    ['slug' => 'create-learning-community', 'title' => 'Communities', 'desc' => 'Build a space for learning, growth, and motivation.', 'image' => 'website/upload/platform/community.png', 'bg' => 'linear-gradient(328deg, #f7fdff 0%, #beedfa 92%)'],
                    ['slug' => 'sell-digital-products', 'title' => 'Digital Products', 'desc' => 'Boost earnings with eBooks, podcasts, and webinars.', 'image' => 'website/upload/platform/digital.png', 'bg' => 'linear-gradient(330deg, #fef9ea 25%, #ffc6ad 74%)'],
                ],
            ],
            'marketing' => [
                'title' => 'Grow Your Student Base with Smart Marketing Tools',
                'text' => 'Attract the right students, capture their interest, and convert them into loyal learners — all from our Marketing and Sales Hub.',
                'bullets' => "Landing Pages that convert visitors into learners\nLead Capture Forms & Call to Actions\nWorkflows, Funnels & Campaigns\nEmail, Push & WhatsApp messaging\nAffiliate marketing tools",
                'image' => 'website/upload/about-bg-1.jpg',
            ],
            'drm' => [
                'title' => 'Best in Class DRM Security For Content Protection',
                'text' => 'Keep your intellectual property safe with enterprise-grade content protection built for online educators.',
                'bullets' => "Prevent Screenshots and Screen Recording\nParallel Login Restriction\nMulti-Device Limit & Piracy Monitor\nEmail & OTP Verification\nWatch Time Control",
                'image' => 'website/upload/about-bg-2.jpg',
            ],
            'apps' => [
                'title' => 'Your Own Branded Apps, Built for Learning',
                'text' => 'Launch a mobile app that’s truly yours — custom logo, brand colors, and secure content delivery for learners on the go.',
            ],
            'domains' => [
                'title' => 'For Every Education Domain',
                'text' => 'The LMS built for every educator, every subject, everywhere.',
                'items' => [
                    ['slug' => 'sell-mock-tests', 'title' => 'Exam Prep', 'desc' => 'Build confidence with mock tests that mirror real exams.', 'type' => 'product', 'icon' => 'fa-graduation-cap'],
                    ['slug' => 'sell-trading-courses', 'title' => 'Stock Market', 'desc' => 'Grow trading academies with marketing and analytics.', 'type' => 'solution', 'icon' => 'fa-line-chart'],
                    ['slug' => 'sell-coding-courses', 'title' => 'Programming', 'desc' => 'Give students space to learn and practice coding securely.', 'type' => 'solution', 'icon' => 'fa-code'],
                    ['slug' => 'sell-lifestyle-and-fitness-courses', 'title' => 'Lifestyle', 'desc' => 'Sell lifestyle and fitness programs with secure payments.', 'type' => 'solution', 'icon' => 'fa-heartbeat'],
                ],
            ],
            'support' => [
                'title' => '9.1/10 Customer Satisfaction. Support Educators Trust',
                'text' => 'We’re known for support that listens first, acts fast, and keeps getting better with your feedback.',
                'items' => [
                    ['title' => 'Email, Chat & Phone Support', 'desc' => 'Multiple ways to connect so help is always within reach.'],
                    ['title' => 'Feedback Workflow', 'desc' => 'Quality support loops that turn feedback into product improvements.'],
                    ['title' => 'Ticket Resolution SLA', 'desc' => 'Clear response commitments so issues never sit idle.'],
                    ['title' => 'Extensive Knowledge Base', 'desc' => 'Self-serve guides for faster setup and day-to-day operations.'],
                    ['title' => 'Dedicated Support Portals', 'desc' => 'Structured help channels for growing academies and teams.'],
                    ['title' => '4+ App Ratings', 'desc' => 'Loved by educators and learners across Android and iOS.'],
                ],
            ],
            'testimonials' => [
                'title' => 'Real Words, Real Impact',
                'text' => '3000+ happy clients growing with StudyNest.',
                'items' => config('website.testimonials', []),
            ],
            'success_stories' => [
                'title' => 'Success Stories from Our Educators',
                'text' => 'See how institutes scale revenue, enrollments, and brand presence with StudyNest.',
                'items' => config('website.success_stories', []),
            ],
            'cta' => [
                'title' => 'Start Teaching Online Today',
                'text' => 'Share your knowledge, secure your content, and grow your community with StudyNest.',
            ],
            default => [],
        };
    }

    public static function get(string $key): array
    {
        $defaults = self::defaults($key);

        try {
            $stored = WebsiteContent::getContent($key, null);
        } catch (\Throwable $e) {
            return $defaults;
        }

        if ($stored === null || $stored === []) {
            return $defaults;
        }

        // Once customized, prefer saved payload (especially list `items`) over defaults.
        return array_merge($defaults, $stored);
    }

    public static function homePayload(): array
    {
        $slides = collect(self::get('slides')['items'] ?? [])
            ->filter(fn ($item) => ($item['is_active'] ?? true) && ! empty($item['title']))
            ->map(function ($item) {
                $item['image_url'] = WebsiteContent::mediaUrl($item['image'] ?? null);

                return $item;
            })
            ->values()
            ->all();

        $partners = collect(self::get('partners')['items'] ?? [])
            ->map(function ($item) {
                $item['image_url'] = WebsiteContent::mediaUrl($item['image'] ?? null);

                return $item;
            })
            ->values()
            ->all();

        $platform = self::get('platform');
        $platform['items'] = collect($platform['items'] ?? [])
            ->map(function ($item) {
                $item['image_url'] = WebsiteContent::mediaUrl($item['image'] ?? null);

                return $item;
            })
            ->values()
            ->all();

        $marketing = self::get('marketing');
        $marketing['image_url'] = WebsiteContent::mediaUrl($marketing['image'] ?? null);
        $marketing['bullets'] = self::lines($marketing['bullets'] ?? '');

        $drm = self::get('drm');
        $drm['image_url'] = WebsiteContent::mediaUrl($drm['image'] ?? null);
        $drm['bullets'] = self::lines($drm['bullets'] ?? '');

        $testimonials = self::get('testimonials');
        $defaultTestimonialItems = collect(config('website.testimonials', []))->keyBy('name');
        $testimonials['items'] = collect($testimonials['items'] ?? [])
            ->map(function ($item) use ($defaultTestimonialItems) {
                $fallback = $defaultTestimonialItems->get($item['name'] ?? '');
                if (empty($item['image']) && $fallback) {
                    $item['image'] = $fallback['image'] ?? null;
                }
                if (empty($item['rating']) && $fallback) {
                    $item['rating'] = $fallback['rating'] ?? 5;
                }
                if (empty($item['result']) && $fallback) {
                    $item['result'] = $fallback['result'] ?? null;
                }

                return $item;
            })
            ->values()
            ->all();

        $stories = self::get('success_stories');
        $defaultStories = collect(config('website.success_stories', []))->keyBy('title');
        $stories['items'] = collect($stories['items'] ?? [])
            ->map(function ($item) use ($defaultStories) {
                $fallback = $defaultStories->get($item['title'] ?? '');
                if (empty($item['image']) && $fallback) {
                    $item['image'] = $fallback['image'] ?? null;
                }

                return $item;
            })
            ->values()
            ->all();

        return [
            'brand' => self::get('brand'),
            'slides' => $slides,
            'video' => self::get('video'),
            'stats' => self::get('stats')['items'] ?? [],
            'partners' => $partners,
            'platform' => $platform,
            'marketing' => $marketing,
            'drm' => $drm,
            'apps' => self::get('apps'),
            'domains' => self::get('domains'),
            'support' => self::get('support'),
            'testimonialsSection' => $testimonials,
            'testimonials' => $testimonials['items'] ?? [],
            'successStoriesSection' => $stories,
            'successStories' => $stories['items'] ?? [],
            'cta' => self::get('cta'),
        ];
    }

    public static function applyBrandToConfig(): void
    {
        $brand = self::get('brand');
        if (! empty($brand['name'])) {
            config(['website.brand' => $brand['name']]);
        }
        if (! empty($brand['tagline'])) {
            config(['website.tagline' => $brand['tagline']]);
        }
        if (! empty($brand['email'])) {
            config(['website.email' => $brand['email']]);
        }
        if (! empty($brand['phone'])) {
            config(['website.phone' => $brand['phone']]);
        }
        if (! empty($brand['address'])) {
            config(['website.address' => preg_split("/\r\n|\n|\r/", trim($brand['address'])) ?: []]);
        }
    }

    protected static function lines(string|array $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map('trim', $value)));
        }

        return array_values(array_filter(array_map('trim', preg_split("/\r\n|\n|\r/", $value) ?: [])));
    }
}
