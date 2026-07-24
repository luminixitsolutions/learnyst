<?php

namespace App\Services;

use App\Models\WebsiteContent;

class BlogService
{
    public static function pageMeta(): array
    {
        return [
            'label' => 'Blogs',
            'description' => 'Blog listing page and all blog posts.',
            'sort' => 60,
        ];
    }

    public static function contentKey(): string
    {
        return 'resource_blogs';
    }

    public static function defaults(): array
    {
        return [
            'title' => 'Blogs',
            'caption' => 'Resources',
            'eyebrow' => 'Academy Growth Tips',
            'summary' => 'Tips and advice for growing your course business with Learnyst.',
            'body' => 'Practical articles on launching courses, protecting content, improving conversions, and running a branded online academy.',
            'hero_gradient' => 'linear-gradient(135deg, #0b1220 0%, #1e3a8a 50%, #38bdf8 120%)',
            'cta_title' => 'Ready to put these ideas into practice?',
            'cta_text' => 'Start your free trial and build your academy with Learnyst.',
            'cta_primary_label' => 'Start Free Trial',
            'cta_secondary_label' => 'Book a Demo',
            'posts' => [
                [
                    'slug' => 'how-to-launch-your-first-online-course',
                    'title' => 'How to Launch Your First Online Course',
                    'excerpt' => 'A practical checklist for packaging your expertise, pricing it well, and getting your first enrollments.',
                    'body' => "Launching your first course is less about perfection and more about clarity.\n\nStart with one outcome your student wants, break it into lessons, and add a simple quiz or worksheet so progress feels tangible. Then set a clear price, write a landing page that explains the transformation, and invite your warmest audience first.\n\nLearnyst helps you do this under your own brand — with course builder, checkout, and learner accounts ready from day one.",
                    'tag' => 'Launch',
                    'date' => 'July 12, 2026',
                    'read' => '6 min read',
                    'author' => 'Learnyst Team',
                    'featured' => true,
                    'is_active' => true,
                ],
                [
                    'slug' => 'why-drm-matters-for-online-educators',
                    'title' => 'Why DRM Matters for Online Educators',
                    'excerpt' => 'If your content is your business, protection is not optional. Here’s how DRM helps you teach with confidence.',
                    'body' => "Premium video and notes take months to create. Without protection, a single leak can undercut your pricing and trust.\n\nDRM and related controls — like device limits and secure playback — help you deliver a great learner experience while reducing piracy risk.\n\nFor exam prep and high-ticket academies, content security is often the deciding factor when choosing an LMS.",
                    'tag' => 'Security',
                    'date' => 'June 28, 2026',
                    'read' => '5 min read',
                    'author' => 'Learnyst Team',
                    'featured' => false,
                    'is_active' => true,
                ],
                [
                    'slug' => 'mock-tests-that-improve-student-outcomes',
                    'title' => 'Mock Tests That Improve Student Outcomes',
                    'excerpt' => 'Use timing, analytics, and solution reviews to turn practice into measurable improvement.',
                    'body' => "Great mock tests do more than score students. They teach.\n\nUse sectional timing, clear marking rules, and detailed solutions so learners understand mistakes. Then review analytics to spot weak topics across your batch.\n\nWith Learnyst, you can build question pools, publish test series, and give students the exam-like practice they need.",
                    'tag' => 'Assessments',
                    'date' => 'June 10, 2026',
                    'read' => '7 min read',
                    'author' => 'Learnyst Team',
                    'featured' => false,
                    'is_active' => true,
                ],
                [
                    'slug' => 'from-social-following-to-paid-academy',
                    'title' => 'From Social Following to Paid Academy',
                    'excerpt' => 'How creators convert attention into owned enrollments with branded courses and community.',
                    'body' => "A large following is distribution — not a business model by itself.\n\nThe creators who sustain growth move fans onto owned channels: a branded website, email list, course catalog, and community. That shift improves retention and revenue predictability.\n\nLearnyst is built for that transition, with course tools, payments, marketing, and apps in one place.",
                    'tag' => 'Creators',
                    'date' => 'May 22, 2026',
                    'read' => '6 min read',
                    'author' => 'Learnyst Team',
                    'featured' => false,
                    'is_active' => true,
                ],
                [
                    'slug' => 'migration-checklist-for-switching-lms',
                    'title' => 'Migration Checklist for Switching LMS Platforms',
                    'excerpt' => 'A calm, practical checklist for moving courses and learners without chaos.',
                    'body' => "Switching platforms feels risky when students are mid-course. The fix is a clear checklist.\n\nAudit your catalog, export learner lists, map pricing and access rules, then migrate content in priority order. Keep communication ready so students know what changes and what stays the same.\n\nLearnyst support can help you plan the transition so go-live feels controlled.",
                    'tag' => 'Support',
                    'date' => 'May 4, 2026',
                    'read' => '8 min read',
                    'author' => 'Learnyst Team',
                    'featured' => false,
                    'is_active' => true,
                ],
                [
                    'slug' => 'how-affiliates-can-boost-course-sales',
                    'title' => 'How Affiliates Can Boost Course Sales',
                    'excerpt' => 'Use trusted partners and tracked links to grow enrollments beyond your own channels.',
                    'body' => "Affiliate partners extend your reach into communities that already trust them.\n\nGive promoters clear offers, tracked links, and fair commissions. Then review which partners drive real enrollments — not just traffic.\n\nWith Learnyst affiliate tools, you can grow sales while keeping reporting and payouts organized.",
                    'tag' => 'Growth',
                    'date' => 'April 18, 2026',
                    'read' => '5 min read',
                    'author' => 'Learnyst Team',
                    'featured' => false,
                    'is_active' => true,
                ],
            ],
        ];
    }

    public static function getPage(): array
    {
        $defaults = self::defaults();

        try {
            $stored = WebsiteContent::getContent(self::contentKey(), null);
        } catch (\Throwable $e) {
            $stored = null;
        }

        $content = is_array($stored) ? array_merge($defaults, $stored) : $defaults;
        $content['posts'] = collect($content['posts'] ?? [])
            ->filter(fn ($post) => ($post['is_active'] ?? true) && trim((string) ($post['slug'] ?? '')) !== '' && trim((string) ($post['title'] ?? '')) !== '')
            ->values()
            ->all();

        return $content;
    }

    public static function getPost(string $slug): ?array
    {
        $page = self::getPage();
        foreach ($page['posts'] as $post) {
            if (($post['slug'] ?? '') === $slug) {
                $post['paragraphs'] = preg_split('/\r\n|\r|\n/', (string) ($post['body'] ?? '')) ?: [];
                $post['paragraphs'] = array_values(array_filter(array_map('trim', $post['paragraphs'])));

                return $post;
            }
        }

        return null;
    }

    public static function relatedPosts(string $slug, int $limit = 3): array
    {
        return collect(self::getPage()['posts'] ?? [])
            ->reject(fn ($post) => ($post['slug'] ?? '') === $slug)
            ->take($limit)
            ->values()
            ->all();
    }
}
