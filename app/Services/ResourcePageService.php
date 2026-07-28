<?php

namespace App\Services;

use App\Models\WebsiteContent;

class ResourcePageService
{
    public static function pages(): array
    {
        return [
            'product-demo' => [
                'label' => 'Product Demo',
                'description' => 'Book a demo page content and highlights.',
                'sort' => 10,
                'layout' => 'demo',
            ],
            'help-center' => [
                'label' => 'Help Center',
                'description' => 'Help categories and support topics.',
                'sort' => 20,
                'layout' => 'help',
            ],
            'support-migration' => [
                'label' => 'Support & Migration',
                'description' => 'Migration steps and support channels.',
                'sort' => 30,
                'layout' => 'migration',
            ],
            'guides' => [
                'label' => 'Guides',
                'description' => 'Guide library cards and resources.',
                'sort' => 40,
                'layout' => 'guides',
            ],
            'whats-new' => [
                'label' => "What's New",
                'description' => 'Changelog and feature release notes.',
                'sort' => 50,
                'layout' => 'changelog',
            ],
        ];
    }

    public static function contentKey(string $slug): string
    {
        return 'resource_'.str_replace('-', '_', $slug);
    }

    public static function defaults(string $slug): array
    {
        return self::richDefaults()[$slug] ?? [
            'title' => '',
            'caption' => 'Resources',
            'eyebrow' => 'Resources',
            'summary' => '',
            'body' => '',
            'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #163663 55%, #1d4ed8 100%)',
            'items' => [],
            'stats' => [],
            'faq' => [],
            'cta_title' => 'Need a hand getting started?',
            'cta_text' => 'Start a free trial or book a demo with our team.',
            'cta_primary_label' => 'Start Free Trial',
            'cta_secondary_label' => 'Book a Demo',
        ];
    }

    public static function get(string $slug): ?array
    {
        if (! array_key_exists($slug, self::pages())) {
            return null;
        }

        $defaults = self::defaults($slug);

        try {
            $stored = WebsiteContent::getContent(self::contentKey($slug), null);
        } catch (\Throwable $e) {
            $stored = null;
        }

        $content = is_array($stored) ? array_merge($defaults, $stored) : $defaults;
        $content['layout'] = self::pages()[$slug]['layout'];
        $content['features'] = ProductPageService::normalizeList($content['features'] ?? []);
        $content['items'] = array_values(array_filter($content['items'] ?? [], function ($item) use ($slug) {
            if ($slug === 'whats-new') {
                return trim((string) ($item['title'] ?? '')) !== '';
            }
            if ($slug === 'help-center' || $slug === 'guides') {
                return trim((string) ($item['title'] ?? '')) !== '';
            }
            if ($slug === 'product-demo' || $slug === 'support-migration') {
                return trim((string) ($item['title'] ?? '')) !== '';
            }

            return true;
        }));
        $content['stats'] = array_values(array_filter($content['stats'] ?? [], fn ($s) => trim((string) ($s['value'] ?? '')) !== ''));
        $content['faq'] = array_values(array_filter($content['faq'] ?? [], fn ($f) => trim((string) ($f['question'] ?? '')) !== ''));

        return $content;
    }

    protected static function richDefaults(): array
    {
        return [
            'product-demo' => [
                'title' => 'Product Demo',
                'caption' => 'Book a Tour',
                'eyebrow' => 'Live Walkthrough',
                'summary' => 'See how StudyNest can power your academy growth in a guided demo.',
                'body' => 'Book a personalized tour with our team and explore course builder, mock tests, live classes, marketing tools, DRM security, and branded apps tailored to your institute.',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%)',
                'features' => [
                    'Course builder and lesson types',
                    'Mock tests and analytics',
                    'Live class workflows',
                    'Marketing and sales hub',
                    'DRM and branded apps',
                ],
                'items' => [
                    ['title' => 'Share your goals', 'desc' => 'Tell us what you sell today — courses, mocks, live classes, or digital products.'],
                    ['title' => 'Live product walkthrough', 'desc' => 'See the educator dashboard, learner experience, and key growth tools in action.'],
                    ['title' => 'Get a launch plan', 'desc' => 'Leave with a clear next-step plan for setup, migration, and go-live.'],
                ],
                'stats' => [
                    ['value' => '30 min', 'label' => 'Typical demo length'],
                    ['value' => '1:1', 'label' => 'Personalized session'],
                    ['value' => 'Same day', 'label' => 'Follow-up support'],
                ],
                'faq' => [
                    ['question' => 'Who should join the demo?', 'answer' => 'Founders, academic heads, and ops leads who care about content, sales, and learner experience.'],
                    ['question' => 'Do I need to prepare anything?', 'answer' => 'Just a short note on what you teach and where your students currently learn.'],
                ],
                'cta_title' => 'Book your free StudyNest demo',
                'cta_text' => 'Get a guided tour and a practical plan to launch or migrate your academy.',
            ],
            'help-center' => [
                'title' => 'Help Center',
                'caption' => 'Support',
                'eyebrow' => 'Self-Serve Support',
                'summary' => 'Hello, how can we help you today?',
                'body' => 'Browse help topics for courses, assessments, website, apps, marketing, reports, and school settings — or start your academy in a few steps.',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 50%, #38bdf8 120%)',
                'items' => [
                    ['icon' => 'fa-rocket', 'title' => 'Get Started', 'desc' => 'You are just a few steps away from creating your own branded school.'],
                    ['icon' => 'fa-book', 'title' => 'Courses', 'desc' => 'Add lessons such as videos, slides, quizzes, articles, and YouTube videos.'],
                    ['icon' => 'fa-clipboard', 'title' => 'Mock Test', 'desc' => 'Create mock exams using authentic competitive exam templates.'],
                    ['icon' => 'fa-files-o', 'title' => 'Test Series', 'desc' => 'Build test series to measure understanding and knowledge level.'],
                    ['icon' => 'fa-cubes', 'title' => 'Bundles', 'desc' => 'Combine courses and tests to sell as combo products.'],
                    ['icon' => 'fa-database', 'title' => 'Question Pool', 'desc' => 'Build and manage your own question bank.'],
                    ['icon' => 'fa-desktop', 'title' => 'Website', 'desc' => 'Build and manage your website, course sales pages, and landing pages.'],
                    ['icon' => 'fa-mobile', 'title' => 'Mobile Apps', 'desc' => 'Android and iOS apps with the app builder.'],
                    ['icon' => 'fa-bullhorn', 'title' => 'Marketing', 'desc' => 'Market products using emails, coupons, and CTAs.'],
                    ['icon' => 'fa-plug', 'title' => 'Integrations', 'desc' => 'Connect Pabbly, Zapier, Google Ads, and Meta Ads.'],
                    ['icon' => 'fa-shield', 'title' => 'Content Security', 'desc' => 'Protect content with DRM encryption and piracy monitoring.'],
                    ['icon' => 'fa-life-ring', 'title' => 'Learner Support', 'desc' => 'View and respond to learner support tickets.'],
                ],
                'stats' => [
                    ['value' => '24/7', 'label' => 'Help articles available'],
                    ['value' => '4+', 'label' => 'App store ratings'],
                    ['value' => 'Fast', 'label' => 'Ticket response loops'],
                ],
                'cta_title' => 'Still need help?',
                'cta_text' => 'Start a trial or book a demo and our team will guide your setup.',
                'external_note' => 'Inspired by the StudyNest Support Center topics.',
            ],
            'support-migration' => [
                'title' => 'Support & Migration',
                'caption' => 'Support',
                'eyebrow' => 'Move with Confidence',
                'summary' => 'Navigate your transition with expert support and practical resources.',
                'body' => 'Moving from another platform? Our team helps you migrate courses, learners, and content smoothly so you can launch on StudyNest with confidence.',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%)',
                'features' => [
                    'Migration planning call',
                    'Course and learner import guidance',
                    'Content checklist and QA support',
                    'Go-live review',
                    'Post-launch support channels',
                ],
                'items' => [
                    ['title' => 'Audit your current setup', 'desc' => 'Map courses, learners, pricing, and must-have workflows before you move.'],
                    ['title' => 'Migrate content safely', 'desc' => 'Bring over lessons, assessments, and brand assets with a clear checklist.'],
                    ['title' => 'Launch and optimize', 'desc' => 'Go live with support for payments, apps, and learner onboarding.'],
                ],
                'faq' => [
                    ['question' => 'How long does migration take?', 'answer' => 'Most academies can plan and launch in days to a few weeks depending on catalog size.'],
                    ['question' => 'Will learners keep access?', 'answer' => 'We help you plan access continuity so students experience a smooth switch.'],
                ],
                'cta_title' => 'Plan your StudyNest migration',
                'cta_text' => 'Book a demo and get a practical transition plan for your academy.',
            ],
            'guides' => [
                'title' => 'Guides',
                'caption' => 'Resources',
                'eyebrow' => 'Playbooks & Templates',
                'summary' => 'A library of practical templates, eBooks, and resources for growing your academy.',
                'body' => 'Explore guides that help you create courses, grow enrollments, and run your online school more effectively with StudyNest.',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 50%, #38bdf8 120%)',
                'items' => [
                    ['title' => 'Course launch checklist', 'tag' => 'Launch', 'read' => '6 min', 'desc' => 'A practical list to go from draft course to first enrollment.'],
                    ['title' => 'Mock test setup guide', 'tag' => 'Assessments', 'read' => '8 min', 'desc' => 'Configure timers, marking, and reports for exam-ready mocks.'],
                    ['title' => 'Migration playbook', 'tag' => 'Support', 'read' => '10 min', 'desc' => 'Move content and learners without disrupting your students.'],
                    ['title' => 'Marketing funnel basics', 'tag' => 'Growth', 'read' => '7 min', 'desc' => 'Use landing pages, offers, and follow-ups to convert leads.'],
                    ['title' => 'Branding your academy', 'tag' => 'Website', 'read' => '5 min', 'desc' => 'Set logo, colors, domain, and trust signals that convert.'],
                    ['title' => 'Content security essentials', 'tag' => 'DRM', 'read' => '5 min', 'desc' => 'Protect videos and reduce piracy risk from day one.'],
                ],
                'cta_title' => 'Put these guides into action',
                'cta_text' => 'Start your free trial and apply the playbooks inside your own academy.',
            ],
            'whats-new' => [
                'title' => "What's New",
                'caption' => 'Changelog',
                'eyebrow' => 'Product Updates',
                'summary' => 'Stay informed about the latest StudyNest feature releases and improvements.',
                'body' => 'Follow new capabilities across course backup, AI tools, certificates, live classes, website builder, and more.',
                'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #14532d 45%, #22c55e 120%)',
                'items' => [
                    [
                        'title' => 'StudyNest Course Backup',
                        'type' => 'New',
                        'date' => '2026',
                        'summary' => 'Keep your course content safe and recoverable with backups from your StudyNest account.',
                        'highlights' => "Reliable course backups\nRecover deleted content\nProtect against accidental changes\nManage updates with confidence",
                    ],
                    [
                        'title' => 'StudyNest AI Tools',
                        'type' => 'New',
                        'date' => '2026',
                        'summary' => 'Refine course material, announcements, and lessons with AI rephrase tools.',
                        'highlights' => "Improve clarity and readability\nSimplify or expand content\nFix grammar instantly\nTranslate into multiple languages",
                    ],
                    [
                        'title' => 'StudyNest Trashcan',
                        'type' => 'New',
                        'date' => '2026',
                        'summary' => 'Move products to trash instead of deleting permanently, then restore within 7 days.',
                        'highlights' => "Safer product deletion\nEasy restore window\nBetter catalog control\nLess risk of data loss",
                    ],
                    [
                        'title' => 'Certificate Builder',
                        'type' => 'New',
                        'date' => '2026',
                        'summary' => 'Create branded certificates with templates, drag-and-drop editing, and automation.',
                        'highlights' => "Educator-ready templates\nNo-code customization\nAuto-fill learner details\nIssue on course completion",
                    ],
                    [
                        'title' => 'Website Builder',
                        'type' => 'New',
                        'date' => '2026',
                        'summary' => 'Launch a branded online school with educator templates and conversion widgets.',
                        'highlights' => "50+ templates\nDrag-and-drop editing\nCustom domain support\nSEO and performance focused",
                    ],
                    [
                        'title' => 'SuperLive',
                        'type' => 'New',
                        'date' => '2026',
                        'summary' => 'Run interactive live classes with polls, Q&A, hand-raise, and instant recordings.',
                        'highlights' => "Interactive classroom tools\nRole-based control\nAuto recordings\nSmoother live operations",
                    ],
                ],
                'cta_title' => 'Try the latest StudyNest features',
                'cta_text' => 'Start your free trial and explore new tools as they ship.',
            ],
        ];
    }
}
