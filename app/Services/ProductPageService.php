<?php

namespace App\Services;

use App\Models\WebsiteContent;

class ProductPageService
{
    public static function products(): array
    {
        $groups = [
            'sell' => 'Sell & Deliver',
            'presence' => 'Brand Presence',
            'market' => 'Marketing & Sales',
        ];

        $items = [];
        foreach (config('website.products', []) as $slug => $product) {
            $group = $product['group'] ?? null;
            $items[$slug] = [
                'label' => $product['menu'] ?? $product['title'] ?? $slug,
                'description' => $product['menu_desc'] ?? ($product['summary'] ?? ''),
                'group' => $group,
                'group_label' => $groups[$group] ?? 'Other',
                'sort' => count($items) + 1,
            ];
        }

        return $items;
    }

    public static function contentKey(string $slug): string
    {
        return 'product_'.$slug;
    }

    public static function defaults(string $slug): array
    {
        $base = config("website.products.{$slug}", []);
        $rich = self::richDefaults()[$slug] ?? [];

        return array_merge([
            'title' => $base['title'] ?? '',
            'caption' => $base['caption'] ?? 'Products',
            'eyebrow' => $base['menu'] ?? 'Product',
            'summary' => $base['summary'] ?? '',
            'body' => $base['body'] ?? '',
            'hero_image' => null,
            'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #163663 55%, #1d4ed8 100%)',
            'features' => $base['features'] ?? [],
            'benefits' => [],
            'use_cases' => [],
            'stats' => self::defaultStats(),
            'faq' => [],
            'cta_title' => 'Ready to get started?',
            'cta_text' => 'Start your free trial and launch your academy in minutes.',
            'cta_primary_label' => 'Start Free Trial',
            'cta_secondary_label' => 'Book a Demo',
        ], $rich);
    }

    public static function get(string $slug): ?array
    {
        if (! config("website.products.{$slug}")) {
            return null;
        }

        $defaults = self::defaults($slug);
        $config = config("website.products.{$slug}");

        try {
            $stored = WebsiteContent::getContent(self::contentKey($slug), null);
        } catch (\Throwable $e) {
            $stored = null;
        }

        $content = is_array($stored) ? array_merge($defaults, $stored) : $defaults;

        // Preserve nav metadata from config
        $content['menu'] = $config['menu'] ?? $content['title'];
        $content['menu_desc'] = $config['menu_desc'] ?? '';
        $content['group'] = $config['group'] ?? null;
        $content['icon'] = $config['icon'] ?? 'fa-cube';

        $content['hero_image_url'] = WebsiteContent::mediaUrl($content['hero_image'] ?? null);
        $content['features'] = self::normalizeList($content['features'] ?? []);
        $content['benefits'] = array_values(array_filter($content['benefits'] ?? [], fn ($b) => trim((string) ($b['title'] ?? '')) !== ''));
        $content['use_cases'] = array_values(array_filter($content['use_cases'] ?? [], fn ($u) => trim((string) ($u['title'] ?? '')) !== ''));
        $content['stats'] = array_values(array_filter($content['stats'] ?? [], fn ($s) => trim((string) ($s['value'] ?? '')) !== ''));
        $content['faq'] = array_values(array_filter($content['faq'] ?? [], fn ($f) => trim((string) ($f['question'] ?? '')) !== ''));

        return $content;
    }

    public static function related(string $slug, int $limit = 4): array
    {
        $current = config("website.products.{$slug}");
        $group = $current['group'] ?? null;
        $related = [];

        foreach (config('website.products', []) as $key => $product) {
            if ($key === $slug) {
                continue;
            }
            if ($group && ($product['group'] ?? null) === $group) {
                $related[$key] = $product;
            }
        }

        if (count($related) < $limit) {
            foreach (config('website.products', []) as $key => $product) {
                if ($key === $slug || isset($related[$key])) {
                    continue;
                }
                $related[$key] = $product;
                if (count($related) >= $limit) {
                    break;
                }
            }
        }

        return array_slice($related, 0, $limit, true);
    }

    /** @return list<string> */
    public static function normalizeList(array|string $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\r\n|\r|\n/', $value) ?: [];
        }

        return array_values(array_filter(array_map(
            fn ($item) => trim(is_array($item) ? (string) ($item['label'] ?? $item['text'] ?? '') : (string) $item),
            $value
        )));
    }

    protected static function defaultStats(): array
    {
        return [
            ['value' => '50,000+', 'label' => 'Educators trust StudyNest'],
            ['value' => '10M+', 'label' => 'Learners reached'],
            ['value' => '99.9%', 'label' => 'Uptime for live academies'],
        ];
    }

    protected static function richDefaults(): array
    {
        return [
            'sell-online-courses' => [
                'eyebrow' => 'Course Builder',
                'hero_image' => 'website/upload/platform/course.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #14532d 50%, #22c55e 120%)',
                'benefits' => [
                    ['icon' => 'fa-magic', 'title' => 'Build faster', 'desc' => 'Drag-and-drop lessons, quizzes, and resources into polished courses.'],
                    ['icon' => 'fa-shield', 'title' => 'Protect content', 'desc' => 'Secure streaming and DRM so your intellectual property stays yours.'],
                    ['icon' => 'fa-line-chart', 'title' => 'Track outcomes', 'desc' => 'See progress, completion, and engagement across every student.'],
                    ['icon' => 'fa-money', 'title' => 'Monetize your way', 'desc' => 'One-time prices, subscriptions, coupons, and bundled offers.'],
                ],
                'use_cases' => [
                    ['title' => 'Coaching institutes', 'desc' => 'Package syllabus-aligned video courses with tests and doubt support.'],
                    ['title' => 'Creators & coaches', 'desc' => 'Launch signature programs under your brand with a smooth checkout.'],
                    ['title' => 'Corporate trainers', 'desc' => 'Deliver structured upskilling paths with progress reporting.'],
                ],
                'faq' => [
                    ['question' => 'Can I drip content over time?', 'answer' => 'Yes. Schedule lessons to unlock on a timeline or after prerequisites are completed.'],
                    ['question' => 'Do students get certificates?', 'answer' => 'You can issue certificates on course completion and customize the design.'],
                    ['question' => 'Is video content protected?', 'answer' => 'StudyNest supports secure streaming and DRM options to reduce piracy risk.'],
                ],
                'cta_title' => 'Start selling courses under your brand',
                'cta_text' => 'Create your first course today and invite students to enroll in minutes.',
            ],
            'sell-mock-tests' => [
                'eyebrow' => 'Assessments',
                'hero_image' => 'website/upload/platform/digital.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 55%, #38bdf8 120%)',
                'benefits' => [
                    ['icon' => 'fa-clock-o', 'title' => 'Exam-like timers', 'desc' => 'Sectional timing, negative marking, and realistic exam flow.'],
                    ['icon' => 'fa-database', 'title' => 'Question pools', 'desc' => 'Reuse banks across mock tests and full test series.'],
                    ['icon' => 'fa-bar-chart', 'title' => 'Deep analytics', 'desc' => 'Rankings, accuracy, topic gaps, and solution reviews.'],
                    ['icon' => 'fa-users', 'title' => 'Scale for batches', 'desc' => 'Run large attempt volumes without losing clarity of insights.'],
                ],
                'use_cases' => [
                    ['title' => 'Competitive exam prep', 'desc' => 'UPSC, NEET, CAT, banking, and more with ranked leaderboards.'],
                    ['title' => 'University assessments', 'desc' => 'Practice papers with detailed solution reports.'],
                    ['title' => 'Skill certifications', 'desc' => 'Timed evaluations tied to course enrollment.'],
                ],
                'faq' => [
                    ['question' => 'Can I create a full test series?', 'answer' => 'Yes. Group mocks into series, set schedules, and track series-level performance.'],
                    ['question' => 'Is negative marking supported?', 'answer' => 'Configure scoring rules per test, including negative marks and sectional cutoffs.'],
                ],
                'cta_title' => 'Launch your mock test platform',
                'cta_text' => 'Publish exam-ready assessments and help students practice with confidence.',
            ],
            'sell-digital-products' => [
                'eyebrow' => 'Digital Downloads',
                'hero_image' => 'website/upload/platform/digital.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #9a3412 50%, #fb923c 120%)',
                'benefits' => [
                    ['icon' => 'fa-file-pdf-o', 'title' => 'Sell any file', 'desc' => 'eBooks, notes, worksheets, audio, and more with secure delivery.'],
                    ['icon' => 'fa-bolt', 'title' => 'Instant access', 'desc' => 'Students get downloads right after successful checkout.'],
                    ['icon' => 'fa-cubes', 'title' => 'Bundle smartly', 'desc' => 'Combine digital products with courses for higher average order value.'],
                    ['icon' => 'fa-lock', 'title' => 'Access control', 'desc' => 'Gate downloads behind purchase and manage entitlement easily.'],
                ],
                'use_cases' => [
                    ['title' => 'Study material shops', 'desc' => 'Sell PDF notes and revision packs alongside live courses.'],
                    ['title' => 'Creator product lines', 'desc' => 'Offer ebooks, templates, and podcasts from one storefront.'],
                ],
                'faq' => [
                    ['question' => 'What formats can I sell?', 'answer' => 'Common formats like PDF, ZIP, audio, and other downloadable files are supported.'],
                    ['question' => 'Can I sell bundles?', 'answer' => 'Yes. Bundle digital products with courses or sell them standalone.'],
                ],
                'cta_title' => 'Add digital products to your academy',
                'cta_text' => 'Create another revenue stream without leaving your StudyNest brand.',
            ],
            'manage-batches-cohorts' => [
                'eyebrow' => 'Cohort Learning',
                'hero_image' => 'website/upload/platform/batch.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #0f766e 50%, #5eead4 120%)',
                'benefits' => [
                    ['icon' => 'fa-calendar', 'title' => 'Start-date cohorts', 'desc' => 'Run time-bound batches with clear schedules and milestones.'],
                    ['icon' => 'fa-video-camera', 'title' => 'Live + recorded', 'desc' => 'Blend live classes with on-demand content for every cohort.'],
                    ['icon' => 'fa-user', 'title' => 'Instructor assignment', 'desc' => 'Assign mentors and keep groups accountable.'],
                    ['icon' => 'fa-tasks', 'title' => 'Group progress', 'desc' => 'See how each batch is progressing at a glance.'],
                ],
                'use_cases' => [
                    ['title' => 'Bootcamps', 'desc' => 'Fixed-duration intensive programs with live mentoring.'],
                    ['title' => 'Classroom coaching', 'desc' => 'Mirror offline batches online with attendance and schedules.'],
                ],
                'faq' => [
                    ['question' => 'Can batches have different start dates?', 'answer' => 'Yes. Create multiple cohorts for the same program with their own timelines.'],
                    ['question' => 'Do cohorts work with live classes?', 'answer' => 'Batches pair naturally with live sessions, recordings, and reminders.'],
                ],
                'cta_title' => 'Bring classroom energy online',
                'cta_text' => 'Launch your next cohort with schedules, live sessions, and group accountability.',
            ],
            'branded-website-builder' => [
                'eyebrow' => 'Your Brand Online',
                'hero_image' => 'website/upload/platform/website.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #be185d 45%, #fda4af 120%)',
                'benefits' => [
                    ['icon' => 'fa-paint-brush', 'title' => 'No-code builder', 'desc' => 'Launch landing pages and product pages without developers.'],
                    ['icon' => 'fa-globe', 'title' => 'Custom domain', 'desc' => 'Teach and sell from your own domain and brand identity.'],
                    ['icon' => 'fa-search', 'title' => 'SEO-friendly', 'desc' => 'Publish crawlable pages that help students discover you.'],
                    ['icon' => 'fa-mouse-pointer', 'title' => 'Conversion ready', 'desc' => 'Lead forms, CTAs, and checkout flows built for enrollments.'],
                ],
                'use_cases' => [
                    ['title' => 'New academies', 'desc' => 'Go live with a professional site on day one.'],
                    ['title' => 'Established brands', 'desc' => 'Migrate your catalog to a conversion-focused website.'],
                ],
                'faq' => [
                    ['question' => 'Do I need a developer?', 'answer' => 'No. The builder is designed for educators and marketing teams.'],
                    ['question' => 'Can I use my own domain?', 'answer' => 'Yes. Connect a custom domain and keep your brand front and center.'],
                ],
                'cta_title' => 'Launch your branded academy website',
                'cta_text' => 'Look professional, capture leads, and sell courses from your own site.',
            ],
            'branded-mobile-app' => [
                'eyebrow' => 'Learn on the Go',
                'hero_image' => 'website/upload/platform/app.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e40af 50%, #93c5fd 120%)',
                'benefits' => [
                    ['icon' => 'fa-mobile', 'title' => 'Your brand on iOS & Android', 'desc' => 'Logo, colors, and splash screens that feel native to your academy.'],
                    ['icon' => 'fa-bell', 'title' => 'Push engagement', 'desc' => 'Remind learners about classes, tests, and new content.'],
                    ['icon' => 'fa-play-circle', 'title' => 'Secure playback', 'desc' => 'Deliver protected video and learning content on mobile.'],
                    ['icon' => 'fa-wifi', 'title' => 'Offline-friendly', 'desc' => 'Help students keep learning even with uneven connectivity.'],
                ],
                'use_cases' => [
                    ['title' => 'Mobile-first audiences', 'desc' => 'Reach students who prefer learning from their phones.'],
                    ['title' => 'Premium academies', 'desc' => 'Offer an app experience that matches your brand promise.'],
                ],
                'faq' => [
                    ['question' => 'Do you help with app store publishing?', 'answer' => 'StudyNest supports branded app publishing workflows for iOS and Android.'],
                    ['question' => 'Can the app match my website branding?', 'answer' => 'Yes. Colors, logo, and naming stay consistent with your academy brand.'],
                ],
                'cta_title' => 'Put your academy in every pocket',
                'cta_text' => 'Offer a branded mobile experience that keeps learners coming back.',
            ],
            'create-learning-community' => [
                'eyebrow' => 'Community',
                'hero_image' => 'website/upload/platform/community.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #0e7490 50%, #67e8f9 120%)',
                'benefits' => [
                    ['icon' => 'fa-comments', 'title' => 'Discussions that stick', 'desc' => 'Spaces for questions, peer help, and mentor replies.'],
                    ['icon' => 'fa-users', 'title' => 'Member management', 'desc' => 'Organize learners and keep conversations productive.'],
                    ['icon' => 'fa-gavel', 'title' => 'Moderation tools', 'desc' => 'Keep communities safe and on-brand.'],
                    ['icon' => 'fa-link', 'title' => 'Course-linked spaces', 'desc' => 'Attach communities to products so context stays relevant.'],
                ],
                'use_cases' => [
                    ['title' => 'Doubt solving', 'desc' => 'Let students ask questions and get faster answers.'],
                    ['title' => 'Peer motivation', 'desc' => 'Build accountability groups around cohorts and courses.'],
                ],
                'faq' => [
                    ['question' => 'Can communities be tied to a course?', 'answer' => 'Yes. Link discussion spaces to products so only enrolled learners participate.'],
                    ['question' => 'Can mentors moderate?', 'answer' => 'Assign moderators and keep conversations helpful with built-in tools.'],
                ],
                'cta_title' => 'Turn students into a thriving community',
                'cta_text' => 'Increase retention with discussion, mentorship, and peer motivation.',
            ],
            'marketing-sales-hub' => [
                'eyebrow' => 'Growth Tools',
                'hero_image' => 'website/upload/platform/website.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #b45309 50%, #fbbf24 120%)',
                'benefits' => [
                    ['icon' => 'fa-bullhorn', 'title' => 'Capture demand', 'desc' => 'Landing pages and forms designed for high-intent leads.'],
                    ['icon' => 'fa-sitemap', 'title' => 'Automate follow-up', 'desc' => 'Workflows across email, push, and WhatsApp.'],
                    ['icon' => 'fa-filter', 'title' => 'Convert faster', 'desc' => 'Funnels that move prospects from interest to enrollment.'],
                    ['icon' => 'fa-line-chart', 'title' => 'Measure what works', 'desc' => 'See which campaigns drive real enrollments.'],
                ],
                'use_cases' => [
                    ['title' => 'Lead gen campaigns', 'desc' => 'Run ads to a StudyNest landing page and nurture automatically.'],
                    ['title' => 'Launch sequences', 'desc' => 'Announce new batches and courses with multi-channel messaging.'],
                ],
                'faq' => [
                    ['question' => 'Which channels are supported?', 'answer' => 'Email, push notifications, and WhatsApp workflows help you stay in touch.'],
                    ['question' => 'Can I build funnels without code?', 'answer' => 'Yes. Combine landing pages, forms, and automations from one hub.'],
                ],
                'cta_title' => 'Grow enrollments from one hub',
                'cta_text' => 'Attract, nurture, and convert learners without juggling disconnected tools.',
            ],
            'affiliate-marketing' => [
                'eyebrow' => 'Partner Growth',
                'hero_image' => 'website/upload/platform/community.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #065f46 50%, #34d399 120%)',
                'benefits' => [
                    ['icon' => 'fa-share-alt', 'title' => 'Tracked links', 'desc' => 'Know which partners drive enrollments.'],
                    ['icon' => 'fa-percent', 'title' => 'Flexible commissions', 'desc' => 'Reward affiliates based on your program rules.'],
                    ['icon' => 'fa-tachometer', 'title' => 'Partner visibility', 'desc' => 'Give promoters a clear view of performance.'],
                    ['icon' => 'fa-money', 'title' => 'Payout workflows', 'desc' => 'Manage commissions and settlements with less manual work.'],
                ],
                'use_cases' => [
                    ['title' => 'Influencer partners', 'desc' => 'Let creators promote your courses with tracked links.'],
                    ['title' => 'Referral programs', 'desc' => 'Turn happy students and teachers into growth channels.'],
                ],
                'faq' => [
                    ['question' => 'Can I set custom commissions?', 'answer' => 'Configure commission rules that match your product margins and campaigns.'],
                    ['question' => 'Do affiliates see their performance?', 'answer' => 'Partners get dashboards so they can track clicks and conversions.'],
                ],
                'cta_title' => 'Scale sales through trusted partners',
                'cta_text' => 'Launch an affiliate program and grow enrollments with every promoter.',
            ],
            'payment-options' => [
                'eyebrow' => 'Checkout',
                'hero_image' => 'website/upload/platform/digital.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #115e59 50%, #2dd4bf 120%)',
                'benefits' => [
                    ['icon' => 'fa-credit-card', 'title' => 'Trusted gateways', 'desc' => 'Accept payments through integrated providers students already trust.'],
                    ['icon' => 'fa-ticket', 'title' => 'Coupons & offers', 'desc' => 'Run discounts and promotions without spreadsheet chaos.'],
                    ['icon' => 'fa-calendar-check-o', 'title' => 'Flexible plans', 'desc' => 'One-time purchases or installment-friendly options.'],
                    ['icon' => 'fa-file-text-o', 'title' => 'Orders & invoices', 'desc' => 'Track purchases, refunds, and order history cleanly.'],
                ],
                'use_cases' => [
                    ['title' => 'High-ticket programs', 'desc' => 'Offer installment-friendly pricing to reduce drop-off.'],
                    ['title' => 'Flash sales', 'desc' => 'Launch coupon campaigns for batch openings.'],
                ],
                'faq' => [
                    ['question' => 'Can I offer coupons?', 'answer' => 'Yes. Create discount codes and track how they perform.'],
                    ['question' => 'Are refunds supported?', 'answer' => 'Manage refund workflows alongside order records.'],
                ],
                'cta_title' => 'Make enrollment effortless',
                'cta_text' => 'Give students a smooth, secure checkout experience every time.',
            ],
            'sales-insights' => [
                'eyebrow' => 'Analytics',
                'hero_image' => 'website/upload/platform/course.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 50%, #60a5fa 120%)',
                'benefits' => [
                    ['icon' => 'fa-pie-chart', 'title' => 'Revenue clarity', 'desc' => 'See what is selling, when, and through which channels.'],
                    ['icon' => 'fa-cubes', 'title' => 'Product performance', 'desc' => 'Compare courses, tests, and digital products side by side.'],
                    ['icon' => 'fa-exchange', 'title' => 'Conversion tracking', 'desc' => 'Understand where prospects drop off and what to fix.'],
                    ['icon' => 'fa-download', 'title' => 'Exportable reports', 'desc' => 'Share insights with your team and advisors.'],
                ],
                'use_cases' => [
                    ['title' => 'Weekly growth reviews', 'desc' => 'Spot trends early and double down on winning products.'],
                    ['title' => 'Campaign ROI', 'desc' => 'Connect promotions and coupons to real revenue.'],
                ],
                'faq' => [
                    ['question' => 'What can I track?', 'answer' => 'Revenue, product sales, conversions, coupons, and campaign performance.'],
                    ['question' => 'Can I export data?', 'answer' => 'Yes. Export reports for deeper analysis or team sharing.'],
                ],
                'cta_title' => 'Decide with data, not guesswork',
                'cta_text' => 'Use sales insights to grow smarter and scale the right products.',
            ],
            'live-class' => [
                'eyebrow' => 'Live Teaching',
                'hero_image' => 'website/upload/platform/batch.png',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #7c2d12 50%, #fb923c 120%)',
                'benefits' => [
                    ['icon' => 'fa-video-camera', 'title' => 'Schedule with ease', 'desc' => 'Plan live sessions and keep learners informed.'],
                    ['icon' => 'fa-check-square-o', 'title' => 'Attendance tracking', 'desc' => 'Know who joined and follow up with absentees.'],
                    ['icon' => 'fa-film', 'title' => 'Recordings', 'desc' => 'Let students revisit sessions they missed.'],
                    ['icon' => 'fa-bell', 'title' => 'Reminders', 'desc' => 'Reduce no-shows with timely notifications.'],
                ],
                'use_cases' => [
                    ['title' => 'Doubt sessions', 'desc' => 'Host regular live Q&A around your courses.'],
                    ['title' => 'Batch teaching', 'desc' => 'Run scheduled live programs for cohorts.'],
                ],
                'faq' => [
                    ['question' => 'Can recordings be shared later?', 'answer' => 'Yes. Make recordings available to enrolled learners after class.'],
                    ['question' => 'Do reminders go out automatically?', 'answer' => 'Send reminders so students show up prepared.'],
                ],
                'cta_title' => 'Teach live without the chaos',
                'cta_text' => 'Schedule, host, record, and follow up — all inside your academy.',
            ],
        ];
    }
}
