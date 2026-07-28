<?php

namespace App\Services;

use App\Models\WebsiteContent;

class CustomerPageService
{
    public static function pages(): array
    {
        $items = [];
        foreach (config('website.customers', []) as $slug => $page) {
            $items[$slug] = [
                'label' => $page['menu'] ?? $page['title'] ?? $slug,
                'description' => $page['menu_desc'] ?? ($page['summary'] ?? ''),
                'sort' => count($items) + 1,
            ];
        }

        return $items;
    }

    public static function contentKey(string $slug): string
    {
        return 'customer_'.str_replace('-', '_', $slug);
    }

    public static function defaults(string $slug): array
    {
        $base = config("website.customers.{$slug}", []);
        $rich = self::richDefaults()[$slug] ?? [];

        return array_merge([
            'title' => $base['title'] ?? '',
            'caption' => $base['caption'] ?? 'Customers',
            'eyebrow' => $base['menu'] ?? 'Customers',
            'summary' => $base['summary'] ?? '',
            'body' => $base['body'] ?? '',
            'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #163663 55%, #1d4ed8 100%)',
            'stats' => self::defaultStats(),
            'items' => [],
            'cta_title' => 'Ready to write your success story?',
            'cta_text' => 'Join thousands of educators growing securely with StudyNest.',
            'cta_primary_label' => 'Start Free Trial',
            'cta_secondary_label' => 'Book a Demo',
        ], $rich);
    }

    public static function get(string $slug): ?array
    {
        if (! config("website.customers.{$slug}")) {
            return null;
        }

        $defaults = self::defaults($slug);
        $config = config("website.customers.{$slug}");

        try {
            $stored = WebsiteContent::getContent(self::contentKey($slug), null);
        } catch (\Throwable $e) {
            $stored = null;
        }

        $content = is_array($stored) ? array_merge($defaults, $stored) : $defaults;

        // Fallback to homepage CMS lists when page items are empty
        if (empty($content['items'])) {
            $content['items'] = match ($slug) {
                'testimonials' => WebsiteContentService::get('testimonials')['items'] ?? config('website.testimonials', []),
                'success-stories' => WebsiteContentService::get('success_stories')['items'] ?? config('website.success_stories', []),
                default => $defaults['items'] ?? [],
            };
        }

        $content['menu'] = $config['menu'] ?? $content['title'];
        $content['menu_desc'] = $config['menu_desc'] ?? '';
        $content['icon'] = $config['icon'] ?? 'fa-heart-o';
        $content['stats'] = array_values(array_filter($content['stats'] ?? [], fn ($s) => trim((string) ($s['value'] ?? '')) !== ''));
        $content['items'] = array_values(array_filter($content['items'] ?? [], function ($item) use ($slug) {
            if ($slug === 'testimonials' || $slug === 'wall-of-love') {
                return trim((string) ($item['quote'] ?? '')) !== '';
            }

            return trim((string) ($item['title'] ?? '')) !== '';
        }));

        return $content;
    }

    public static function related(string $slug): array
    {
        $related = [];
        foreach (config('website.customers', []) as $key => $page) {
            if ($key === $slug) {
                continue;
            }
            $related[$key] = $page;
        }

        return $related;
    }

    protected static function defaultStats(): array
    {
        return [
            ['value' => '12,000+', 'label' => 'Institutes trust StudyNest'],
            ['value' => '3,000+', 'label' => 'Happy client reviews'],
            ['value' => '10M+', 'label' => 'Learners reached'],
        ];
    }

    protected static function richDefaults(): array
    {
        $testimonials = collect(config('website.testimonials', []))->map(function ($item, $index) {
            $extras = [
                ['rating' => 5, 'result' => 'Trusted by CAT aspirants nationwide', 'featured' => true],
                ['rating' => 5, 'result' => '50x revenue growth', 'featured' => true],
                ['rating' => 5, 'result' => 'Rapid product improvements', 'featured' => false],
                ['rating' => 5, 'result' => 'Simplified academy operations', 'featured' => false],
                ['rating' => 5, 'result' => 'Ideal for early-stage institutes', 'featured' => false],
            ][$index] ?? ['rating' => 5, 'result' => '', 'featured' => false];

            return array_merge($item, $extras);
        })->all();

        $stories = collect(config('website.success_stories', []))->map(function ($item, $index) {
            $extras = [
                ['summary' => 'EduTap built a scalable banking prep brand with secure courses, mocks, and rapid enrollment growth.', 'metric' => 'Pan-India', 'metric_label' => 'Reach'],
                ['summary' => 'Xylem combined live teaching and digital delivery to accelerate revenue in record time.', 'metric' => '₹255 Cr', 'metric_label' => 'In 38 months'],
                ['summary' => 'LCO scaled coding education with branded delivery, community, and strong monetization.', 'metric' => '₹120 Cr', 'metric_label' => 'Valuation'],
                ['summary' => '2IIM doubled online business by pairing trusted CAT content with a stronger digital funnel.', 'metric' => '2x', 'metric_label' => 'Online growth'],
                ['summary' => 'Scalper Academy unlocked explosive profit growth with secure content and better student ops.', 'metric' => '50x', 'metric_label' => 'Profit growth'],
                ['summary' => 'Sleepy Classes improved conversions with a smoother learner experience and stronger acquisition.', 'metric' => '2x', 'metric_label' => 'Signups'],
            ][$index] ?? ['summary' => '', 'metric' => '', 'metric_label' => ''];

            return array_merge($item, $extras);
        })->all();

        return [
            'testimonials' => [
                'eyebrow' => 'Social Proof',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%)',
                'items' => $testimonials,
                'cta_title' => 'Join educators who trust StudyNest',
                'cta_text' => 'Start your free trial and see why institutes recommend StudyNest for growth and content security.',
            ],
            'success-stories' => [
                'eyebrow' => 'Case Studies',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 50%, #60a5fa 120%)',
                'items' => $stories,
                'cta_title' => 'Your growth story can be next',
                'cta_text' => 'Launch on StudyNest and build an academy story worth sharing.',
            ],
            'wall-of-love' => [
                'eyebrow' => 'Community Love',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #9a3412 45%, #fb923c 120%)',
                'items' => [
                    ['quote' => 'Support that actually replies — and ships fixes fast. That alone changed how we run our academy.', 'name' => 'Ananya R.', 'role' => 'Founder, Exam Prep Brand', 'source' => 'Email'],
                    ['quote' => 'We finally feel like we own our brand online. Website, apps, payments — all ours.', 'name' => 'Karthik M.', 'role' => 'CEO, Coding Bootcamp', 'source' => 'Call'],
                    ['quote' => 'DRM was the deal-breaker. We switched and stopped worrying about content leaks.', 'name' => 'Neha S.', 'role' => 'Director, UPSC Institute', 'source' => 'LinkedIn'],
                    ['quote' => 'Mock tests + live classes in one place made operations so much cleaner for our team.', 'name' => 'Imran Q.', 'role' => 'Operations Head', 'source' => 'WhatsApp'],
                    ['quote' => 'From first demo to launch, the team felt like a partner — not just a vendor.', 'name' => 'Priya D.', 'role' => 'Creator & Coach', 'source' => 'Twitter'],
                    ['quote' => 'Our enrollments jumped after we moved checkout and courses under one branded funnel.', 'name' => 'Suresh K.', 'role' => 'Founder, Trading Academy', 'source' => 'Review'],
                    ['quote' => 'The UI is simple enough for teachers, powerful enough for growth teams.', 'name' => 'Meera J.', 'role' => 'Product Lead', 'source' => 'Email'],
                    ['quote' => 'Migration was smoother than we expected. Students barely noticed — in a good way.', 'name' => 'Arjun P.', 'role' => 'Admin, CA Coaching', 'source' => 'Support'],
                ],
                'cta_title' => 'Become part of the wall',
                'cta_text' => 'Build with StudyNest and give your students an experience worth talking about.',
            ],
        ];
    }
}
