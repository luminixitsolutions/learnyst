<?php

/**
 * Seed demo subscription plans.
 * Run: php database/seeders/seed_subscriptions.php
 * Or included from DatabaseSeeder.
 */

use App\Models\SubscriptionPlan;
use App\Models\User;

if (! function_exists('app') || ! app()->bound('db')) {
    require __DIR__.'/../../vendor/autoload.php';
    $app = require __DIR__.'/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
}

$admin = User::where('email', 'admin@studynest.com')->first()
    ?? User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

if (! $admin) {
    echo "  Skipped subscription seed: no admin user found.\n";

    return;
}

$plans = [
    [
        'title' => 'Monthly Course Access',
        'slug' => 'monthly-course-access',
        'description' => 'Recurring monthly access to a single course.',
        'plan_type' => 'course',
        'billing_cycle' => 'monthly',
        'billing_days' => 30,
        'price' => 999,
        'setup_fee' => 0,
        'trial_days' => 7,
        'auto_renew' => true,
    ],
    [
        'title' => 'Yearly Bundle Pro',
        'slug' => 'yearly-bundle-pro',
        'description' => 'Annual subscription for a course bundle.',
        'plan_type' => 'bundle',
        'billing_cycle' => 'yearly',
        'billing_days' => 365,
        'price' => 9999,
        'setup_fee' => 499,
        'trial_days' => 0,
        'auto_renew' => true,
    ],
];

foreach ($plans as $data) {
    SubscriptionPlan::updateOrCreate(
        [
            'created_by' => $admin->id,
            'slug' => $data['slug'],
        ],
        array_merge($data, [
            'currency' => 'INR',
            'is_active' => true,
            'product_type' => $data['plan_type'],
        ])
    );
}

echo '  Subscription plans seeded ('.count($plans).").\n";
