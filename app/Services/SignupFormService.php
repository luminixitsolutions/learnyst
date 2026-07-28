<?php

namespace App\Services;

use App\Models\WebsiteContent;

class SignupFormService
{
    public static function questions(): array
    {
        return [
            'business_type' => [
                'label' => 'Business type',
                'description' => '“What best describes what you do?” options.',
                'sort' => 10,
            ],
            'teach' => [
                'label' => 'What do you teach?',
                'description' => 'Shown when career / upskilling is selected.',
                'sort' => 20,
            ],
            'goal' => [
                'label' => 'Business goal',
                'description' => '“What is your primary goal right now?” options.',
                'sort' => 30,
            ],
            'content_ready' => [
                'label' => 'Content readiness',
                'description' => 'Whether the educator has content ready.',
                'sort' => 40,
            ],
            'audience' => [
                'label' => 'Audience',
                'description' => 'Do they already have an audience?',
                'sort' => 50,
            ],
            'source' => [
                'label' => 'How did you find us?',
                'description' => 'Acquisition source options.',
                'sort' => 60,
            ],
        ];
    }

    public static function contentKey(string $question): string
    {
        return 'signup_'.$question;
    }

    public static function defaults(string $question): array
    {
        return match ($question) {
            'business_type' => [
                'title' => 'Help us to personalise your experience',
                'subtitle' => "Tell us a bit about your business and goals. We'll use this to set up StudyNest the way that works best for you.",
                'label' => 'What best describes what you do?',
                'options' => [
                    ['value' => 'exam_prep', 'label' => 'Competitive Exam Prep (UPSC, CAT, NEET, etc.)', 'is_active' => true, 'opens_teach' => false],
                    ['value' => 'career_upskilling', 'label' => 'Career Upskilling or Income Growth (Coding, Stock Market, Digital Marketing, etc.)', 'is_active' => true, 'opens_teach' => true],
                    ['value' => 'corporate', 'label' => 'Corporate Training (Skill Development, Customer Training, etc.)', 'is_active' => true, 'opens_teach' => false],
                    ['value' => 'lifestyle', 'label' => 'Lifestyle and Creator Content (Fitness, Fashion, Cooking, etc.)', 'is_active' => true, 'opens_teach' => false],
                ],
            ],
            'teach' => [
                'title' => 'What do you teach?',
                'subtitle' => null,
                'label' => null,
                'options' => [
                    ['value' => 'coding', 'label' => 'Coding', 'is_active' => true],
                    ['value' => 'stock_market', 'label' => 'Stock Market / Trading', 'is_active' => true],
                    ['value' => 'merchant_navy', 'label' => 'Merchant Navy', 'is_active' => true],
                    ['value' => 'aviation', 'label' => 'Aviation Studies', 'is_active' => true],
                    ['value' => 'business_coaching', 'label' => 'Business Coaching', 'is_active' => true],
                    ['value' => 'digital_marketing', 'label' => 'Digital Marketing', 'is_active' => true],
                    ['value' => 'other', 'label' => 'Other', 'is_active' => true],
                ],
            ],
            'goal' => [
                'title' => 'What is your primary goal right now?',
                'subtitle' => null,
                'label' => null,
                'options' => [
                    ['value' => 'from_scratch', 'label' => 'Starting my online business from scratch', 'is_active' => true],
                    ['value' => 'offline_to_online', 'label' => 'Taking my offline business online', 'is_active' => true],
                    ['value' => 'scaling', 'label' => 'Scaling my existing online business', 'is_active' => true],
                    ['value' => 'migrating', 'label' => 'Migrating from another platform', 'is_active' => true],
                    ['value' => 'exploring', 'label' => 'Just exploring', 'is_active' => true],
                ],
            ],
            'content_ready' => [
                'title' => 'Do you have content ready?',
                'subtitle' => null,
                'label' => null,
                'options' => [
                    ['value' => 'yes', 'label' => 'Yes, I have content ready', 'is_active' => true],
                    ['value' => 'partial', 'label' => 'Partially ready, still working on it', 'is_active' => true],
                    ['value' => 'no', 'label' => 'Not ready, starting from scratch', 'is_active' => true],
                ],
            ],
            'audience' => [
                'title' => 'Do you already have an audience?',
                'subtitle' => null,
                'label' => null,
                'options' => [
                    ['value' => 'social', 'label' => 'Yes, on social media (YouTube, Instagram, etc.)', 'is_active' => true],
                    ['value' => 'offline', 'label' => 'Yes, offline (existing students or clients)', 'is_active' => true],
                    ['value' => 'both', 'label' => 'Yes, both online and offline', 'is_active' => true],
                    ['value' => 'none', 'label' => 'No audience yet, starting from zero', 'is_active' => true],
                ],
            ],
            'source' => [
                'title' => 'How did you find out about StudyNest?',
                'subtitle' => null,
                'label' => null,
                'options' => [
                    ['value' => 'google', 'label' => 'Google Search', 'is_active' => true],
                    ['value' => 'social', 'label' => 'Social Media (Instagram, YouTube)', 'is_active' => true],
                    ['value' => 'ads', 'label' => 'Paid Ads (Google, Meta)', 'is_active' => true],
                    ['value' => 'referral', 'label' => 'Referred by someone', 'is_active' => true],
                    ['value' => 'ai', 'label' => 'AI Tools (ChatGPT, Claude, etc.)', 'is_active' => true],
                    ['value' => 'other', 'label' => 'Other', 'is_active' => true],
                ],
            ],
            default => ['title' => '', 'subtitle' => null, 'label' => null, 'options' => []],
        };
    }

    public static function get(string $question): array
    {
        $defaults = self::defaults($question);

        try {
            $stored = WebsiteContent::getContent(self::contentKey($question), null);
        } catch (\Throwable $e) {
            return $defaults;
        }

        if ($stored === null || $stored === []) {
            return $defaults;
        }

        return array_merge($defaults, $stored);
    }

    /** @return array<string, string> value => label for active options */
    public static function choices(string $question): array
    {
        $options = self::get($question)['options'] ?? [];
        $choices = [];

        foreach ($options as $option) {
            if (! ($option['is_active'] ?? true)) {
                continue;
            }
            $value = trim((string) ($option['value'] ?? ''));
            $label = trim((string) ($option['label'] ?? ''));
            if ($value === '' || $label === '') {
                continue;
            }
            $choices[$value] = $label;
        }

        return $choices;
    }

    public static function allChoices(): array
    {
        $all = [];
        foreach (array_keys(self::questions()) as $question) {
            $all[$question] = self::choices($question);
        }

        return $all;
    }

    public static function opensTeach(?string $businessType): bool
    {
        if (! $businessType) {
            return false;
        }

        foreach (self::get('business_type')['options'] ?? [] as $option) {
            if (($option['value'] ?? null) === $businessType) {
                return (bool) ($option['opens_teach'] ?? false);
            }
        }

        return $businessType === 'career_upskilling';
    }

    public static function slugify(string $label): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '_', $label) ?? ''));

        return trim($slug, '_') ?: 'option_'.substr(md5($label), 0, 6);
    }
}
