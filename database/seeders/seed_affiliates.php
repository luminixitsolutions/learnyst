<?php

/**
 * Seed affiliate settings + demo affiliates.
 * Run: php database/seeders/seed_affiliates.php
 */

use App\Models\Affiliate;
use App\Models\Setting;
use App\Models\User;
use App\Services\AffiliateService;

if (! function_exists('app') || ! app()->bound('db')) {
    require __DIR__.'/../../vendor/autoload.php';
    $app = require __DIR__.'/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
}

$admin = User::where('email', 'admin@studynest.com')->first()
    ?? User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

if (! $admin) {
    echo "  Skipped affiliate seed: no admin user found.\n";

    return;
}

Setting::set('enabled', '1', 'affiliate');
Setting::set('default_commission_percent', '15', 'affiliate');
Setting::set('cookie_days', '30', 'affiliate');
Setting::set('auto_approve', '0', 'affiliate');

$service = app(AffiliateService::class);

$demos = [
    [
        'name' => 'Priya Sharma',
        'email' => 'priya.affiliate@example.com',
        'phone' => '9876500001',
        'code' => 'PRIYA15',
        'commission_type' => 'percent',
        'commission_value' => 15,
        'status' => 'approved',
        'payment_details' => "UPI: priya@upi\nBank: HDFC ****4521",
        'notes' => 'Demo affiliate — education influencers',
    ],
    [
        'name' => 'Rahul Verma',
        'email' => 'rahul.affiliate@example.com',
        'phone' => '9876500002',
        'code' => 'RAHUL10',
        'commission_type' => 'percent',
        'commission_value' => 10,
        'status' => 'approved',
        'payment_details' => 'Bank transfer — ICICI ****8890',
        'notes' => 'Demo affiliate — YouTube channel',
    ],
    [
        'name' => 'Ananya Patel',
        'email' => 'ananya.affiliate@example.com',
        'phone' => '9876500003',
        'code' => 'ANANYA20',
        'commission_type' => 'fixed',
        'commission_value' => 200,
        'status' => 'pending',
        'payment_details' => null,
        'notes' => 'Demo affiliate awaiting approval',
    ],
];

$created = 0;
foreach ($demos as $demo) {
    $existing = Affiliate::where('code', $demo['code'])
        ->orWhere(function ($q) use ($demo, $admin) {
            $q->where('email', $demo['email'])->where('created_by', $admin->id);
        })
        ->first();

    if ($existing) {
        continue;
    }

    $status = $demo['status'];
    unset($demo['status']);

    $affiliate = $service->createAffiliate($demo, $admin->id);

    if ($status === 'approved' && $affiliate->status !== 'approved') {
        $service->approveAffiliate($affiliate);
    } elseif ($status === 'pending' && $affiliate->status === 'approved') {
        $affiliate->update(['status' => 'pending', 'approved_at' => null]);
    }

    $service->getOrCreateLink($affiliate->fresh(), 'custom', null, $admin->id);
    $created++;
}

echo "  Affiliate settings + {$created} demo affiliate(s) seeded.\n";
