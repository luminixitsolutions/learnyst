<?php

namespace App\Services;

use App\Models\WebsiteContent;

class SolutionPageService
{
    public static function solutions(): array
    {
        $groups = [
            'test_prep' => 'Test Prep',
            'creators' => 'Creators & Upskilling',
        ];

        $items = [];
        foreach (config('website.solutions', []) as $slug => $solution) {
            $group = $solution['group'] ?? null;
            $items[$slug] = [
                'label' => $solution['menu'] ?? $solution['title'] ?? $slug,
                'description' => $solution['menu_desc'] ?? ($solution['summary'] ?? ''),
                'group' => $group,
                'group_label' => $groups[$group] ?? 'Other',
                'sort' => count($items) + 1,
            ];
        }

        return $items;
    }

    public static function contentKey(string $slug): string
    {
        return 'solution_'.$slug;
    }

    public static function defaults(string $slug): array
    {
        $base = config("website.solutions.{$slug}", []);
        $rich = self::richDefaults()[$slug] ?? [];

        return array_merge([
            'title' => $base['title'] ?? '',
            'caption' => $base['caption'] ?? 'Solutions',
            'eyebrow' => $base['menu'] ?? 'Solution',
            'summary' => $base['summary'] ?? '',
            'body' => $base['body'] ?? '',
            'hero_image' => null,
            'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #163663 55%, #1d4ed8 100%)',
            'features' => $base['features'] ?? [],
            'benefits' => [],
            'use_cases' => [],
            'stats' => self::defaultStats(),
            'faq' => [],
            'cta_title' => 'Ready to launch your academy?',
            'cta_text' => 'Start a free trial and build your branded learning business on StudyNest.',
            'cta_primary_label' => 'Start Free Trial',
            'cta_secondary_label' => 'Book a Demo',
        ], $rich);
    }

    public static function get(string $slug): ?array
    {
        if (! config("website.solutions.{$slug}")) {
            return null;
        }

        $defaults = self::defaults($slug);
        $config = config("website.solutions.{$slug}");

        try {
            $stored = WebsiteContent::getContent(self::contentKey($slug), null);
        } catch (\Throwable $e) {
            $stored = null;
        }

        $content = is_array($stored) ? array_merge($defaults, $stored) : $defaults;

        $content['menu'] = $config['menu'] ?? $content['title'];
        $content['menu_desc'] = $config['menu_desc'] ?? '';
        $content['group'] = $config['group'] ?? null;
        $content['icon'] = $config['icon'] ?? 'fa-graduation-cap';

        $content['hero_image_url'] = WebsiteContent::mediaUrl($content['hero_image'] ?? null);
        $content['features'] = ProductPageService::normalizeList($content['features'] ?? []);
        $content['benefits'] = array_values(array_filter($content['benefits'] ?? [], fn ($b) => trim((string) ($b['title'] ?? '')) !== ''));
        $content['use_cases'] = array_values(array_filter($content['use_cases'] ?? [], fn ($u) => trim((string) ($u['title'] ?? '')) !== ''));
        $content['stats'] = array_values(array_filter($content['stats'] ?? [], fn ($s) => trim((string) ($s['value'] ?? '')) !== ''));
        $content['faq'] = array_values(array_filter($content['faq'] ?? [], fn ($f) => trim((string) ($f['question'] ?? '')) !== ''));

        return $content;
    }

    public static function related(string $slug, int $limit = 4): array
    {
        $current = config("website.solutions.{$slug}");
        $group = $current['group'] ?? null;
        $related = [];

        foreach (config('website.solutions', []) as $key => $solution) {
            if ($key === $slug) {
                continue;
            }
            if ($group && ($solution['group'] ?? null) === $group) {
                $related[$key] = $solution;
            }
        }

        if (count($related) < $limit) {
            foreach (config('website.solutions', []) as $key => $solution) {
                if ($key === $slug || isset($related[$key])) {
                    continue;
                }
                $related[$key] = $solution;
                if (count($related) >= $limit) {
                    break;
                }
            }
        }

        return array_slice($related, 0, $limit, true);
    }

    protected static function defaultStats(): array
    {
        return [
            ['value' => '12,000+', 'label' => 'Institutes on StudyNest'],
            ['value' => '10M+', 'label' => 'Learners served'],
            ['value' => 'Best-in-class', 'label' => 'DRM content protection'],
        ];
    }

    protected static function richDefaults(): array
    {
        $testPrepFeatures = [
            'Structured courses with drip content',
            'Mock tests and test series',
            'Live classes and doubt sessions',
            'Student progress analytics',
            'Branded website and secure payments',
        ];

        $creatorFeatures = [
            'Course and cohort builder',
            'Live classes and community',
            'Marketing and lead capture',
            'Secure video and DRM options',
            'Checkout, coupons, and insights',
        ];

        return [
            'sell-upsc-courses' => [
                'eyebrow' => 'Civil Services Prep',
                'hero_image' => 'website/upload/platform/course.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 50%, #60a5fa 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-book', 'title' => 'Syllabus-ready courses', 'desc' => 'Package GS, optional subjects, and current affairs into clear learning paths.'],
                    ['icon' => 'fa-clipboard', 'title' => 'UPSC-style mocks', 'desc' => 'Sectional and full-length tests with rankings and detailed solutions.'],
                    ['icon' => 'fa-users', 'title' => 'Mentorship cohorts', 'desc' => 'Run answer-writing batches and live guidance with schedules.'],
                    ['icon' => 'fa-shield', 'title' => 'Protect premium content', 'desc' => 'Keep high-value video and notes secure with DRM-ready delivery.'],
                ],
                'use_cases' => [
                    ['title' => 'Offline coaching institutes', 'desc' => 'Extend classroom programs online with recorded lessons and mocks.'],
                    ['title' => 'Online UPSC educators', 'desc' => 'Sell courses, test series, and mentorship under your own brand.'],
                    ['title' => 'Hybrid academies', 'desc' => 'Blend live classes, community doubt solving, and self-paced modules.'],
                ],
                'faq' => [
                    ['question' => 'Can I sell both courses and test series?', 'answer' => 'Yes. Offer courses, mock tests, and mentorship products from one academy storefront.'],
                    ['question' => 'Is content protected from piracy?', 'answer' => 'StudyNest supports secure streaming and DRM options designed for high-value exam content.'],
                ],
                'cta_title' => 'Build your UPSC academy on StudyNest',
                'cta_text' => 'Launch courses, mocks, and mentorship with the security and branding serious aspirants expect.',
            ],
            'sell-ca-courses' => [
                'eyebrow' => 'Chartered Accountancy',
                'hero_image' => 'website/upload/platform/course.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #0f766e 50%, #5eead4 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-sitemap', 'title' => 'Foundation to Final', 'desc' => 'Organize level-wise programs with clear modules and milestones.'],
                    ['icon' => 'fa-pencil', 'title' => 'Practice that sticks', 'desc' => 'Pair video lessons with mocks, revisions, and performance reports.'],
                    ['icon' => 'fa-calendar', 'title' => 'Batch-based coaching', 'desc' => 'Run start-date cohorts for classroom-style accountability.'],
                    ['icon' => 'fa-mobile', 'title' => 'Learn on the go', 'desc' => 'Deliver content through your branded website and mobile apps.'],
                ],
                'use_cases' => [
                    ['title' => 'CA coaching centers', 'desc' => 'Digitize lectures, notes, and test series without losing your brand.'],
                    ['title' => 'Subject specialists', 'desc' => 'Sell focused modules like taxation, audit, or costing.'],
                ],
                'faq' => [
                    ['question' => 'Can I run different batches for the same course?', 'answer' => 'Yes. Create cohorts with their own schedules while reusing the same content.'],
                    ['question' => 'Do students get progress tracking?', 'answer' => 'Track completion, attempts, and engagement across your CA programs.'],
                ],
                'cta_title' => 'Scale your CA coaching online',
                'cta_text' => 'Deliver structured CA programs with mocks, batches, and secure content delivery.',
            ],
            'sell-cat-courses' => [
                'eyebrow' => 'MBA Entrance Prep',
                'hero_image' => 'website/upload/platform/digital.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #9a3412 45%, #fb923c 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-calculator', 'title' => 'Quant, VARC & LRDI', 'desc' => 'Build section-wise courses with targeted practice sets.'],
                    ['icon' => 'fa-clock-o', 'title' => 'Timed CAT mocks', 'desc' => 'Simulate exam pressure with analytics students can act on.'],
                    ['icon' => 'fa-line-chart', 'title' => 'Improvement insights', 'desc' => 'Show accuracy, speed, and topic gaps after every attempt.'],
                    ['icon' => 'fa-bullhorn', 'title' => 'Fill every batch', 'desc' => 'Use landing pages and campaigns to convert serious aspirants.'],
                ],
                'use_cases' => [
                    ['title' => 'CAT coaching brands', 'desc' => 'Sell full programs plus sectional test packs.'],
                    ['title' => 'Weekend bootcamps', 'desc' => 'Run intensive live cohorts with recorded backups.'],
                ],
                'faq' => [
                    ['question' => 'Can I offer sectional tests separately?', 'answer' => 'Yes. Sell full mocks, sectional packs, or complete CAT courses as separate products.'],
                    ['question' => 'Will students see detailed analysis?', 'answer' => 'Mock attempts can include rankings, accuracy views, and solution reviews.'],
                ],
                'cta_title' => 'Grow your CAT academy faster',
                'cta_text' => 'Launch courses and mocks that feel exam-ready — under your brand.',
            ],
            'sell-iit-jee-courses' => [
                'eyebrow' => 'Engineering Entrance',
                'hero_image' => 'website/upload/platform/batch.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1d4ed8 50%, #93c5fd 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-video-camera', 'title' => 'Live + recorded teaching', 'desc' => 'Combine live classes with on-demand revision libraries.'],
                    ['icon' => 'fa-flask', 'title' => 'PCM mastery paths', 'desc' => 'Structure Physics, Chemistry, and Math into clear courses.'],
                    ['icon' => 'fa-trophy', 'title' => 'Rank-focused mocks', 'desc' => 'Full-length and chapter tests with competitive leaderboards.'],
                    ['icon' => 'fa-users', 'title' => 'Doubt communities', 'desc' => 'Keep students engaged between classes with discussion spaces.'],
                ],
                'use_cases' => [
                    ['title' => 'JEE coaching institutes', 'desc' => 'Extend classroom batches online with secure content.'],
                    ['title' => 'Online JEE educators', 'desc' => 'Sell crash courses, test series, and year-long programs.'],
                ],
                'faq' => [
                    ['question' => 'Can live classes be tied to batches?', 'answer' => 'Yes. Schedule live sessions for specific cohorts and share recordings afterward.'],
                    ['question' => 'Is high-volume testing supported?', 'answer' => 'Run chapter tests and full mocks at scale with performance reporting.'],
                ],
                'cta_title' => 'Power your IIT-JEE coaching brand',
                'cta_text' => 'Teach live, assign mocks, and protect premium content from one platform.',
            ],
            'sell-gate-courses' => [
                'eyebrow' => 'Engineering PG Prep',
                'hero_image' => 'website/upload/platform/course.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #115e59 50%, #2dd4bf 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-cogs', 'title' => 'Subject-wise modules', 'desc' => 'Organize GATE subjects into sellable courses and practice packs.'],
                    ['icon' => 'fa-file-text-o', 'title' => 'Full-length GATE mocks', 'desc' => 'Deliver exam-like tests with scoring and solution reviews.'],
                    ['icon' => 'fa-bar-chart', 'title' => 'Topic-level insights', 'desc' => 'Help students focus revision where it matters most.'],
                    ['icon' => 'fa-globe', 'title' => 'Your branded academy', 'desc' => 'Sell from your website and apps — not a marketplace listing.'],
                ],
                'use_cases' => [
                    ['title' => 'Branch-specific educators', 'desc' => 'Launch ME, EE, CS, CE, and other GATE verticals.'],
                    ['title' => 'Test-series businesses', 'desc' => 'Specialize in mocks with deep analytics.'],
                ],
                'faq' => [
                    ['question' => 'Can I sell subject packs separately?', 'answer' => 'Yes. Offer full GATE programs or subject-wise courses and tests.'],
                    ['question' => 'Do you support detailed solutions?', 'answer' => 'Attach solutions and analysis so students learn from every attempt.'],
                ],
                'cta_title' => 'Launch a GATE academy students trust',
                'cta_text' => 'Structure modules, publish mocks, and grow enrollments under your brand.',
            ],
            'sell-gmat-focus-courses' => [
                'eyebrow' => 'GMAT Focus Edition',
                'hero_image' => 'website/upload/platform/digital.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #7c2d12 45%, #fdba74 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-crosshairs', 'title' => 'Focus-aligned prep', 'desc' => 'Build lessons around Quant, Verbal, and Data Insights.'],
                    ['icon' => 'fa-tachometer', 'title' => 'Adaptive practice feel', 'desc' => 'Use timed drills and mocks to sharpen pacing and accuracy.'],
                    ['icon' => 'fa-comments', 'title' => 'Mentor support', 'desc' => 'Offer live strategy sessions and community doubt solving.'],
                    ['icon' => 'fa-credit-card', 'title' => 'Global-ready checkout', 'desc' => 'Sell prep packages with coupons and flexible payment options.'],
                ],
                'use_cases' => [
                    ['title' => 'MBA admissions coaches', 'desc' => 'Bundle GMAT prep with application mentoring.'],
                    ['title' => 'Test prep boutiques', 'desc' => 'Sell premium small-batch GMAT Focus cohorts.'],
                ],
                'faq' => [
                    ['question' => 'Can I update content for GMAT Focus?', 'answer' => 'Yes. Refresh lessons and mocks anytime as the exam format evolves.'],
                    ['question' => 'Can I offer both self-paced and live prep?', 'answer' => 'Combine recorded courses with live cohorts in one academy.'],
                ],
                'cta_title' => 'Sell GMAT Focus prep with confidence',
                'cta_text' => 'Deliver modern prep experiences with analytics, live mentoring, and secure content.',
            ],
            'sell-banking-courses' => [
                'eyebrow' => 'Banking & Govt Exams',
                'hero_image' => 'website/upload/platform/batch.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e40af 50%, #93c5fd 120%)',
                'features' => $testPrepFeatures,
                'benefits' => [
                    ['icon' => 'fa-university', 'title' => 'PO & Clerk programs', 'desc' => 'Build courses for IBPS, SBI, RBI, NABARD, and more.'],
                    ['icon' => 'fa-newspaper-o', 'title' => 'Current affairs cadence', 'desc' => 'Publish updates, quizzes, and revision packs on a schedule.'],
                    ['icon' => 'fa-list-ol', 'title' => 'Sectional test banks', 'desc' => 'Quant, reasoning, English, and GA practice at scale.'],
                    ['icon' => 'fa-bell', 'title' => 'Live doubt sessions', 'desc' => 'Keep batches engaged with reminders and live classes.'],
                ],
                'use_cases' => [
                    ['title' => 'Banking exam institutes', 'desc' => 'Sell year-long courses plus monthly current affairs.'],
                    ['title' => 'Mock-test specialists', 'desc' => 'Run high-volume sectional and full-length series.'],
                ],
                'faq' => [
                    ['question' => 'Can I drip current affairs content?', 'answer' => 'Yes. Schedule releases so students get fresh material on a timeline.'],
                    ['question' => 'Are large batch enrollments supported?', 'answer' => 'StudyNest is built for institutes that need to scale enrollments and attempts.'],
                ],
                'cta_title' => 'Scale your banking exam academy',
                'cta_text' => 'Courses, mocks, live batches, and marketing — all under your brand.',
            ],
            'sell-coding-courses' => [
                'eyebrow' => 'Coding & Tech Skills',
                'hero_image' => 'website/upload/platform/app.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #0e7490 50%, #67e8f9 120%)',
                'features' => $creatorFeatures,
                'benefits' => [
                    ['icon' => 'fa-code', 'title' => 'Project-based learning', 'desc' => 'Ship bootcamps with lessons, projects, and progress tracking.'],
                    ['icon' => 'fa-users', 'title' => 'Cohort energy', 'desc' => 'Run start-date batches with live mentoring and community.'],
                    ['icon' => 'fa-rocket', 'title' => 'Launch faster', 'desc' => 'Go live with a branded site, checkout, and student accounts.'],
                    ['icon' => 'fa-lock', 'title' => 'Protect your IP', 'desc' => 'Keep premium coding content secure as your catalog grows.'],
                ],
                'use_cases' => [
                    ['title' => 'Coding bootcamps', 'desc' => 'Full-stack, data, or career-switch programs with cohorts.'],
                    ['title' => 'Developer educators', 'desc' => 'Sell niche courses and live workshops from your brand.'],
                ],
                'faq' => [
                    ['question' => 'Can I mix live and self-paced coding courses?', 'answer' => 'Yes. Offer recorded libraries alongside live cohort programs.'],
                    ['question' => 'Do communities work with courses?', 'answer' => 'Attach discussion spaces so students collaborate and ask questions.'],
                ],
                'cta_title' => 'Build a coding school students love',
                'cta_text' => 'Teach, mentor, and monetize programming programs on your own platform.',
            ],
            'sell-trading-courses' => [
                'eyebrow' => 'Markets & Trading',
                'hero_image' => 'website/upload/platform/digital.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #065f46 50%, #34d399 120%)',
                'features' => $creatorFeatures,
                'benefits' => [
                    ['icon' => 'fa-line-chart', 'title' => 'Market-ready courses', 'desc' => 'Sell stock, options, and trading programs with clear modules.'],
                    ['icon' => 'fa-video-camera', 'title' => 'Live market sessions', 'desc' => 'Host live classes and share recordings with enrolled students.'],
                    ['icon' => 'fa-shield', 'title' => 'Serious content security', 'desc' => 'Protect paid strategies and premium recordings from leaks.'],
                    ['icon' => 'fa-bullhorn', 'title' => 'Grow with marketing tools', 'desc' => 'Capture leads and convert them with landing pages and campaigns.'],
                ],
                'use_cases' => [
                    ['title' => 'Trading educators', 'desc' => 'Package courses, alerts communities, and live mentorship.'],
                    ['title' => 'Finance academies', 'desc' => 'Offer beginner-to-advanced market education under one brand.'],
                ],
                'faq' => [
                    ['question' => 'Can I restrict access after purchase?', 'answer' => 'Yes. Control who can watch live sessions and course content.'],
                    ['question' => 'Is DRM available for trading content?', 'answer' => 'StudyNest offers strong protection options for high-value video content.'],
                ],
                'cta_title' => 'Grow your trading academy securely',
                'cta_text' => 'Teach markets, protect your edge, and scale enrollments with StudyNest.',
            ],
            'sell-lifestyle-and-fitness-courses' => [
                'eyebrow' => 'Wellness & Lifestyle',
                'hero_image' => 'website/upload/platform/community.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #be185d 45%, #fda4af 120%)',
                'features' => $creatorFeatures,
                'benefits' => [
                    ['icon' => 'fa-heartbeat', 'title' => 'Programs that feel premium', 'desc' => 'Sell fitness, yoga, nutrition, and lifestyle courses beautifully.'],
                    ['icon' => 'fa-calendar-check-o', 'title' => 'Live challenges & cohorts', 'desc' => 'Run 21-day or 12-week programs with schedules and reminders.'],
                    ['icon' => 'fa-shopping-bag', 'title' => 'Digital extras', 'desc' => 'Add meal plans, guides, and downloadable products to boost revenue.'],
                    ['icon' => 'fa-mobile', 'title' => 'Mobile-first learning', 'desc' => 'Let members train and learn from your branded app experience.'],
                ],
                'use_cases' => [
                    ['title' => 'Fitness coaches', 'desc' => 'Launch membership-style programs and live workouts.'],
                    ['title' => 'Wellness creators', 'desc' => 'Sell courses, challenges, and community access together.'],
                ],
                'faq' => [
                    ['question' => 'Can I sell programs as cohorts?', 'answer' => 'Yes. Create start-date batches for challenges and group coaching.'],
                    ['question' => 'Can I bundle downloads with courses?', 'answer' => 'Offer ebooks, plans, and other digital products alongside programs.'],
                ],
                'cta_title' => 'Turn lifestyle expertise into a business',
                'cta_text' => 'Build a branded wellness academy with courses, community, and checkout.',
            ],
            'sell-content-creator-courses' => [
                'eyebrow' => 'Creator Academies',
                'hero_image' => 'website/upload/platform/website.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #b45309 45%, #fbbf24 120%)',
                'features' => $creatorFeatures,
                'benefits' => [
                    ['icon' => 'fa-play-circle', 'title' => 'Package your content', 'desc' => 'Turn videos and playbooks into structured paid courses.'],
                    ['icon' => 'fa-users', 'title' => 'Own your audience', 'desc' => 'Move fans from social platforms into your branded community.'],
                    ['icon' => 'fa-money', 'title' => 'Multiple revenue streams', 'desc' => 'Courses, digital products, live sessions, and affiliates.'],
                    ['icon' => 'fa-paint-brush', 'title' => 'Stay on-brand', 'desc' => 'Website, apps, and checkout that look like you — not a marketplace.'],
                ],
                'use_cases' => [
                    ['title' => 'YouTube & Instagram educators', 'desc' => 'Monetize expertise beyond ads and brand deals.'],
                    ['title' => 'Creator coaches', 'desc' => 'Sell masterminds, courses, and community memberships.'],
                ],
                'faq' => [
                    ['question' => 'Do I need technical skills to launch?', 'answer' => 'No. StudyNest gives you course tools, website building, and payments in one place.'],
                    ['question' => 'Can I market to my existing audience?', 'answer' => 'Use landing pages, email, push, and WhatsApp workflows to convert followers into students.'],
                ],
                'cta_title' => 'Build your creator academy today',
                'cta_text' => 'Own the relationship, protect your content, and grow revenue on your terms.',
            ],
        ];
    }
}
