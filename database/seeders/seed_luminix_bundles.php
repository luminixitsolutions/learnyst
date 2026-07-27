<?php

/**
 * Seed demo bundles for Luminix (luminix@learnyst.com).
 * Run: php database/seeders/seed_luminix_bundles.php
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Bundle;
use App\Models\Company;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Str;

$owner = User::where('email', 'luminix@learnyst.com')->first();
if (! $owner) {
    fwrite(STDERR, "User luminix@learnyst.com not found. Run DemoInstitutesAndStudentsSeeder first.\n");
    exit(1);
}

$ownerId = (int) $owner->id;
$company = Company::where('slug', 'luminix-it-solution')->first();
if ($company && (int) $company->owner_user_id !== $ownerId) {
    $company->update(['owner_user_id' => $ownerId]);
    echo "Linked company owner to luminix@learnyst.com (user #{$ownerId}).\n";
}

$courses = Course::where('created_by', $ownerId)
    ->where('status', 'published')
    ->orderBy('title')
    ->get()
    ->keyBy('title');

if ($courses->count() < 2) {
    fwrite(STDERR, "Need at least 2 published courses owned by luminix@learnyst.com.\n");
    exit(1);
}

echo "Seeding bundles for {$owner->email} (user #{$ownerId})...\n";

$bundles = [
    [
        'title' => 'Web Developer Starter Pack',
        'slug' => 'web-developer-starter-pack',
        'description' => 'Kickstart your web development career with HTML, CSS, JavaScript foundations and React essentials — bundled at a special price.',
        'price' => 6999,
        'sale_price' => 5499,
        'validity_days' => 365,
        'status' => 'published',
        'courses' => [
            'Introduction to Web Development',
            'React.js Frontend Essentials',
        ],
    ],
    [
        'title' => 'Data & Analytics Combo',
        'slug' => 'data-analytics-combo',
        'description' => 'Master spreadsheets and databases together. Excel for analysis plus MySQL fundamentals in one bundle.',
        'price' => 3999,
        'sale_price' => 3299,
        'validity_days' => 180,
        'status' => 'published',
        'courses' => [
            'Excel for Data Analysis',
            'MySQL & Database Fundamentals',
        ],
    ],
    [
        'title' => 'Digital Skills Pro Bundle',
        'slug' => 'digital-skills-pro-bundle',
        'description' => 'Complete digital skillset: web development, SEO marketing, and UI/UX design for modern careers.',
        'price' => 11999,
        'sale_price' => 8999,
        'validity_days' => 365,
        'status' => 'published',
        'courses' => [
            'Introduction to Web Development',
            'SEO & Content Marketing',
            'UI/UX Design Fundamentals',
        ],
    ],
];

$created = 0;
$reassigned = 0;
$skipped = 0;

foreach ($bundles as $data) {
    $courseTitles = $data['courses'];
    unset($data['courses']);

    $existing = Bundle::where('title', $data['title'])->first();

    if ($existing) {
        if ((int) $existing->created_by !== $ownerId) {
            $existing->update(['created_by' => $ownerId]);
            echo "  Reassigned: {$data['title']} → luminix@learnyst.com\n";
            $reassigned++;
        } else {
            echo "  Skip (exists): {$data['title']}\n";
            $skipped++;
        }
        continue;
    }

    $courseIds = collect($courseTitles)->map(function ($title) use ($courses) {
        $course = $courses->get($title);
        if (! $course) {
            throw new RuntimeException("Course not found: {$title}");
        }

        return $course->id;
    });

    $bundle = Bundle::create(array_merge($data, [
        'created_by' => $ownerId,
        'slug' => $data['slug'].'-'.Str::lower(Str::random(4)),
    ]));

    $bundle->courses()->sync(
        $courseIds->values()->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i + 1]])->all()
    );

    echo "  Created: {$bundle->title} ({$bundle->courses()->count()} courses) — ₹".number_format((float) $bundle->price, 0)."\n";
    $created++;
}

$total = Bundle::where('created_by', $ownerId)->count();
echo "\nDone. Created: {$created}, Reassigned: {$reassigned}, Skipped: {$skipped}, Total for luminix@learnyst.com: {$total}\n";
