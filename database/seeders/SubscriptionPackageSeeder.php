<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'tagline' => 'Launch your first courses with confidence.',
                'description' => 'Perfect for tutors and small institutes getting started online.',
                'price_monthly' => 1499,
                'price_yearly' => 14990,
                'currency' => 'INR',
                'is_free' => false,
                'is_custom' => false,
                'trial_days' => 14,
                'features' => [
                    'Unlimited courses',
                    'Up to 500 students',
                    'Branded website',
                    'Quizzes & certificates',
                    'Email support',
                ],
                'cta_label' => 'Start Free Trial',
                'cta_url' => null,
                'badge' => null,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'tagline' => 'Scale enrollments with marketing & DRM.',
                'description' => 'Built for growing academies that sell courses every day.',
                'price_monthly' => 3999,
                'price_yearly' => 39990,
                'currency' => 'INR',
                'is_free' => false,
                'is_custom' => false,
                'trial_days' => 14,
                'features' => [
                    'Everything in Starter',
                    'Unlimited students',
                    'DRM content protection',
                    'Live classes & mock tests',
                    'Coupons & marketing tools',
                    'Priority support',
                ],
                'cta_label' => 'Start Free Trial',
                'cta_url' => null,
                'badge' => 'Most Popular',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'tagline' => 'Custom security, apps, and onboarding.',
                'description' => 'For large institutes that need white-glove setup and advanced controls.',
                'price_monthly' => null,
                'price_yearly' => null,
                'currency' => 'INR',
                'is_free' => false,
                'is_custom' => true,
                'trial_days' => 0,
                'features' => [
                    'Everything in Growth',
                    'Android & iOS branded apps',
                    'Advanced DRM & device limits',
                    'Dedicated success manager',
                    'Custom SLAs & onboarding',
                    'Migration assistance',
                ],
                'cta_label' => 'Talk to Sales',
                'cta_url' => null,
                'badge' => null,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::updateOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}
