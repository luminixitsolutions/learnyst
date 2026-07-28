<?php

/**
 * Set homepage hero slider images to education / student photos.
 * Run: php database/seeders/seed_hero_student_slides.php
 */

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WebsiteContent;
use App\Services\WebsiteContentService;

$slides = [
    [
        'image' => 'website/upload/shutterstock_218235004-800x533.jpg',
        'title' => 'Discover Why Top Institutes Choose StudyNest For Unbeatable Growth',
        'text' => 'Protect your content, grow your student base, and lead with confidence on the most secure LMS built for educators.',
        'is_active' => true,
    ],
    [
        'image' => 'website/upload/shutterstock_481869205-800x405.jpg',
        'title' => 'Launch and Scale Your Academy From One Platform',
        'text' => 'Create courses, run mock tests, host live classes, and market your brand — all from a single secure learning platform.',
        'is_active' => true,
    ],
    [
        'image' => 'website/upload/shutterstock_361397258-800x533.jpg',
        'title' => 'Best-in-Class Content Security for Educators',
        'text' => 'Stop piracy with screenshot blocking, device limits, parallel login restriction, OTP checks, and watch-time controls.',
        'is_active' => true,
    ],
    [
        'image' => 'website/upload/annie-spratt-294450-unsplash.jpg',
        'title' => 'Your Own Branded Apps, Built for Learning',
        'text' => 'Give learners a polished mobile experience with your logo, colors, and secure content delivery.',
        'is_active' => true,
    ],
    [
        'image' => 'website/upload/shutterstock_734589535-800x534.jpg',
        'title' => 'Empower Every Student to Learn Without Limits',
        'text' => 'Deliver engaging lessons, track progress, and support learners across web and mobile — wherever they study.',
        'is_active' => true,
    ],
];

$meta = WebsiteContentService::sections()['slides'];

WebsiteContent::putContent(
    'slides',
    $meta['label'],
    ['items' => $slides],
    $meta['group'],
    $meta['sort']
);

echo "Updated hero slider with ".count($slides)." education student images:\n";
foreach ($slides as $i => $slide) {
    echo '  '.($i + 1).'. '.$slide['image']."\n";
}
