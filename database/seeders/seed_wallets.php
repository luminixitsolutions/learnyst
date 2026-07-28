<?php

/**
 * Seed wallet settings + demo ledger.
 * Run: php database/seeders/seed_wallets.php
 * Or included from DatabaseSeeder.
 */

use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;

if (! function_exists('app') || ! app()->bound('db')) {
    require __DIR__.'/../../vendor/autoload.php';
    $app = require __DIR__.'/../../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
}

$admin = User::where('email', 'admin@studynest.com')->first()
    ?? User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->first();

if (! $admin) {
    echo "  Skipped wallet seed: no admin user found.\n";

    return;
}

Setting::set('enabled', '1', 'wallet');
Setting::set('allow_checkout_redeem', '1', 'wallet');
Setting::set('refund_to_wallet', '1', 'wallet');
Setting::set('signup_bonus', '50', 'wallet');
Setting::set('min_topup', '100', 'wallet');

$wallets = app(WalletService::class);
$learnerRoleId = Role::where('slug', 'learner')->value('id');

$learners = User::query()
    ->where('role_id', $learnerRoleId)
    ->where(function ($q) use ($admin) {
        $q->where('created_by', $admin->id)
            ->orWhereNull('created_by');
    })
    ->orderBy('id')
    ->take(5)
    ->get();

if ($learners->isEmpty()) {
    echo "  Skipped wallet demo txns: no learners found.\n";

    return;
}

foreach ($learners as $i => $learner) {
    if (! $learner->created_by) {
        $learner->update(['created_by' => $admin->id]);
    }

    $wallet = $wallets->getOrCreateForLearner($learner, $admin->id);

    if ($wallet->transactions()->exists()) {
        continue;
    }

    $credit = [250, 150, 200, 100, 75][$i] ?? 100;
    $wallets->credit(
        $wallet,
        $credit,
        $i === 4 ? WalletTransaction::SOURCE_SIGNUP_REWARD : WalletTransaction::SOURCE_REFERRAL_BONUS,
        $i === 4 ? 'Demo signup reward' : 'Demo referral bonus',
        $admin,
        null,
        strtoupper(substr(preg_replace('/\s+/', '', $learner->name), 0, 8)).($i + 1),
        ['referred_user' => $learners[($i + 1) % $learners->count()]->name ?? '—']
    );

    if ($i === 2 && (float) $wallet->fresh()->balance >= 50) {
        $wallets->debit(
            $wallet->fresh(),
            50,
            WalletTransaction::SOURCE_ORDER_PAYMENT,
            'Demo course purchase debit',
            $admin
        );
    }
}

echo "  Wallet settings + demo ledger seeded for ".$learners->count()." learners.\n";
