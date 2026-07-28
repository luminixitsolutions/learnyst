<?php

/**
 * Ensure Platform Section has an education (Mock Tests) card.
 * Run: php database/seeders/seed_platform_education_card.php
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WebsiteContent;
use App\Services\WebsiteContentService;

$meta = WebsiteContentService::sections()['platform'];
$content = WebsiteContentService::get('platform');
$items = collect($content['items'] ?? []);

$educationCard = [
    'slug' => 'sell-mock-tests',
    'title' => 'Mock Tests',
    'desc' => 'Create exam-ready mock tests and test series for students.',
    'image' => 'website/upload/platform/digital.png',
    'bg' => 'linear-gradient(330deg, #fff7ed 15%, #fdba74 88%)',
];

$exists = $items->contains(function ($item) {
    return ($item['slug'] ?? '') === 'sell-mock-tests'
        || strcasecmp((string) ($item['title'] ?? ''), 'Mock Tests') === 0;
});

if ($exists) {
    echo "Mock Tests education card already exists.\n";
    exit(0);
}

// Insert after Batch/Course cards when possible.
$insertAt = $items->search(fn ($item) => ($item['slug'] ?? '') === 'manage-batches-cohorts');
if ($insertAt === false) {
    $items->push($educationCard);
} else {
    $items->splice($insertAt + 1, 0, [$educationCard]);
}

$content['items'] = $items->values()->all();

WebsiteContent::putContent(
    'platform',
    $meta['label'],
    $content,
    $meta['group'],
    $meta['sort']
);

echo "Added education card: Mock Tests\n";
echo "Total platform cards: ".count($content['items'])."\n";
foreach ($content['items'] as $i => $item) {
    echo '  '.($i + 1).'. '.($item['title'] ?? '?').' ('.($item['slug'] ?? '').")\n";
}
