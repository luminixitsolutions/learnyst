<?php

/**
 * Seed monthly paid orders for Luminix dashboard chart.
 * Run: php database/seeders/seed_luminix_dashboard_sales.php
 */

use App\Models\Company;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$company = Company::query()->where('slug', 'luminix-it-solution')->first();
if (! $company || ! $company->owner_user_id) {
    fwrite(STDERR, "Luminix company not found.\n");
    exit(1);
}

$ownerId = (int) $company->owner_user_id;
$courses = Course::where('created_by', $ownerId)->where('status', 'published')->get();
$learners = User::where('created_by', $ownerId)
    ->whereHas('role', fn ($q) => $q->where('slug', 'learner'))
    ->get();

if ($courses->isEmpty()) {
    fwrite(STDERR, "No Luminix courses found.\n");
    exit(1);
}

if ($learners->isEmpty()) {
    $learners = User::whereHas('role', fn ($q) => $q->where('slug', 'learner'))->limit(5)->get();
}

$year = (int) date('Y');
$monthlyTotals = [
    1 => 1200,
    2 => 850,
    3 => 1450,
    4 => 980,
    5 => 1675,
    6 => 1320,
    7 => 1890,
    8 => 0,
    9 => 0,
    10 => 0,
    11 => 0,
    12 => 0,
];

echo "Seeding Luminix monthly sales for {$year} (owner #{$ownerId})...\n";

$created = 0;

foreach ($monthlyTotals as $month => $targetTotal) {
    if ($targetTotal <= 0) {
        continue;
    }

    $existing = Order::where('payment_status', 'paid')
        ->whereYear('created_at', $year)
        ->whereMonth('created_at', $month)
        ->whereHas('items.course', fn ($q) => $q->where('created_by', $ownerId))
        ->sum('total');

    if ($existing >= $targetTotal * 0.8) {
        echo "  Month {$month}: already has ₹{$existing}, skipping.\n";
        continue;
    }

    $remaining = $targetTotal - $existing;
    $learner = $learners[$created % max($learners->count(), 1)];
    $course = $courses[$created % $courses->count()];
    $orderDate = now()->setDate($year, $month, min(15 + ($created % 10), 28))->setTime(10, 30, 0);

    $order = new Order([
        'order_number' => 'LMX-'.$year.str_pad((string) $month, 2, '0', STR_PAD_LEFT).'-'.Str::upper(Str::random(5)),
        'user_id' => $learner->id,
        'subtotal' => $remaining,
        'discount' => 0,
        'tax' => 0,
        'total' => $remaining,
        'payment_status' => 'paid',
        'payment_method' => 'manual',
        'paid_at' => $orderDate,
    ]);
    $order->created_at = $orderDate;
    $order->updated_at = $orderDate;
    $order->save();

    $item = new OrderItem([
        'order_id' => $order->id,
        'course_id' => $course->id,
        'price' => $remaining,
        'discount' => 0,
        'total' => $remaining,
    ]);
    $item->created_at = $orderDate;
    $item->updated_at = $orderDate;
    $item->save();

    $payment = new Payment([
        'order_id' => $order->id,
        'user_id' => $learner->id,
        'transaction_id' => 'TXN-'.Str::upper(Str::random(10)),
        'gateway' => 'manual',
        'amount' => $remaining,
        'status' => 'success',
        'paid_at' => $orderDate,
    ]);
    $payment->created_at = $orderDate;
    $payment->updated_at = $orderDate;
    $payment->save();

    echo "  Month {$month}: added order ₹{$remaining}\n";
    $created++;
}

echo "Done. Created {$created} orders.\n";
