<?php

/**
 * Seed Luminix institute report demo data.
 * Run: php database/seeders/seed_luminix_reports.php
 */

use App\Models\Batch;
use App\Models\Bundle;
use App\Models\Campaign;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Lead;
use App\Models\Resource;
use App\Models\ResourceDownload;
use App\Models\ScheduledEvent;
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
$instructors = User::where('created_by', $ownerId)
    ->whereHas('role', fn ($q) => $q->where('slug', 'instructor'))
    ->get();

if ($courses->isEmpty() || $learners->isEmpty()) {
    fwrite(STDERR, "Run seed_luminix_users.php and course seeders first.\n");
    exit(1);
}

echo "Seeding Luminix report data for owner #{$ownerId}...\n";

$progressMetaSets = [
    ['completed_lessons' => 8, 'total_lessons' => 12, 'course_quiz_score' => 78, 'mock_test_score' => 62, 'test_series_score' => 84, 'test_series_total' => 100, 'bundle_quiz_score' => 71, 'completed_content' => '4/6 modules', 'live_attendance' => 'Present', 'watch_duration' => '52 min', 'join_time' => '10:02 AM', 'leave_time' => '10:54 AM'],
    ['completed_lessons' => 5, 'total_lessons' => 10, 'course_quiz_score' => 65, 'mock_test_score' => 55, 'test_series_score' => 72, 'test_series_total' => 100, 'bundle_quiz_score' => 68, 'completed_content' => '3/6 modules', 'live_attendance' => 'Present', 'watch_duration' => '45 min', 'join_time' => '2:01 PM', 'leave_time' => '2:46 PM'],
    ['completed_lessons' => 11, 'total_lessons' => 14, 'course_quiz_score' => 88, 'mock_test_score' => 74, 'test_series_score' => 91, 'test_series_total' => 100, 'bundle_quiz_score' => 82, 'completed_content' => '5/6 modules', 'live_attendance' => 'Late', 'watch_duration' => '38 min', 'join_time' => '11:15 AM', 'leave_time' => '11:53 AM'],
    ['completed_lessons' => 3, 'total_lessons' => 8, 'course_quiz_score' => 58, 'mock_test_score' => 48, 'test_series_score' => 66, 'test_series_total' => 100, 'bundle_quiz_score' => 60, 'completed_content' => '2/6 modules', 'live_attendance' => 'Absent', 'watch_duration' => '—', 'join_time' => '—', 'leave_time' => '—'],
    ['completed_lessons' => 9, 'total_lessons' => 12, 'course_quiz_score' => 82, 'mock_test_score' => 69, 'test_series_score' => 79, 'test_series_total' => 100, 'bundle_quiz_score' => 75, 'completed_content' => '6/6 modules', 'live_attendance' => 'Present', 'watch_duration' => '61 min', 'join_time' => '4:00 PM', 'leave_time' => '5:01 PM'],
];

$enrollmentCount = 0;
foreach ($learners as $index => $learner) {
    $course = $courses[$index % $courses->count()];
    $meta = $progressMetaSets[$index % count($progressMetaSets)];

    CourseEnrollment::updateOrCreate(
        ['user_id' => $learner->id, 'course_id' => $course->id],
        [
            'enrollment_type' => 'course',
            'status' => 'active',
            'access_type' => 'paid',
            'enrolled_at' => now()->subDays(20 + $index * 5),
            'access_starts_at' => now()->subDays(20 + $index * 5),
            'expires_at' => now()->addMonths(6),
            'progress' => rand(35, 92),
            'meta' => $meta,
            'updated_at' => now()->subDays(rand(1, 10)),
        ]
    );
    $enrollmentCount++;
}

echo "  Enrollments with progress meta: {$enrollmentCount}\n";

$bundle = Bundle::firstOrCreate(
    ['slug' => 'luminix-full-stack-bundle', 'created_by' => $ownerId],
    [
        'title' => 'Luminix Full Stack Bundle',
        'description' => 'Complete web development bundle for Luminix learners.',
        'price' => 9999,
        'sale_price' => 7999,
        'validity_days' => 365,
        'status' => 'published',
    ]
);

if ($bundle->courses()->count() === 0) {
    $bundle->courses()->sync($courses->take(3)->pluck('id')->values()->all());
}

foreach ($learners->take(4) as $index => $learner) {
    $bundleCourseIds = $bundle->courses()->pluck('courses.id');
    $enrollment = CourseEnrollment::where('user_id', $learner->id)
        ->whereIn('course_id', $bundleCourseIds)
        ->first();

    if (! $enrollment) {
        continue;
    }

    $enrollment->update([
        'enrollment_type' => 'bundle',
        'bundle_id' => $bundle->id,
        'progress' => rand(25, 80),
        'meta' => array_merge($enrollment->meta ?? [], [
            'courses_completed' => rand(1, 2),
            'total_courses' => max($bundleCourseIds->count(), 1),
        ]),
        'updated_at' => now()->subDays(rand(1, 7)),
    ]);
}

echo "  Bundle enrollments seeded.\n";

$instructor = $instructors->first();
foreach ($courses->take(5) as $index => $course) {
    Batch::firstOrCreate(
        ['slug' => 'luminix-batch-'.$course->id, 'course_id' => $course->id],
        [
            'title' => $course->title.' — Batch '.($index + 1),
            'instructor_id' => $instructor?->id,
            'start_date' => now()->subDays(30)->addDays($index * 7),
            'end_date' => now()->addDays(60 + $index * 7),
            'status' => 'active',
            'is_online' => true,
            'price' => 4999,
        ]
    );
}

echo "  Batches seeded.\n";

$couponRows = [
    ['code' => 'LUMINIX10', 'title' => '10% Off All Courses', 'discount_type' => 'percentage', 'discount_value' => 10],
    ['code' => 'WELCOME500', 'title' => '₹500 Welcome Discount', 'discount_type' => 'fixed', 'discount_value' => 500],
    ['code' => 'SUMMER25', 'title' => 'Summer Sale 25%', 'discount_type' => 'percentage', 'discount_value' => 25],
    ['code' => 'BUNDLE15', 'title' => 'Bundle Discount', 'discount_type' => 'percentage', 'discount_value' => 15],
    ['code' => 'FLASH1000', 'title' => 'Flash ₹1000 Off', 'discount_type' => 'fixed', 'discount_value' => 1000],
];

foreach ($couponRows as $index => $row) {
    Coupon::firstOrCreate(
        ['code' => $row['code']],
        [
            'title' => $row['title'],
            'discount_type' => $row['discount_type'],
            'discount_value' => $row['discount_value'],
            'used_count' => rand(2, 18),
            'starts_at' => now()->subMonths(2),
            'expires_at' => now()->addMonths(3),
            'is_active' => true,
        ]
    );
}

echo "  Coupons seeded.\n";

$campaignRows = [
    ['title' => 'New Course Launch — React Mastery', 'channel' => 'email', 'status' => 'sent'],
    ['title' => 'Weekend Workshop Reminder', 'channel' => 'whatsapp', 'status' => 'sent'],
    ['title' => 'Fee Payment Due Notice', 'channel' => 'both', 'status' => 'scheduled'],
    ['title' => 'Certificate Distribution Update', 'channel' => 'email', 'status' => 'sent'],
    ['title' => 'Holiday Schedule Announcement', 'channel' => 'email', 'status' => 'draft'],
];

foreach ($campaignRows as $index => $row) {
    Campaign::firstOrCreate(
        ['title' => $row['title'], 'created_by' => $ownerId],
        [
            'content' => 'Demo broadcast message for Luminix institute reports.',
            'channel' => $row['channel'],
            'status' => $row['status'],
            'scheduled_at' => now()->addDays($index)->setTime(10, 0),
            'sent_at' => $row['status'] === 'sent' ? now()->subDays($index + 1) : null,
        ]
    );
}

echo "  Campaigns seeded.\n";

foreach ($learners->take(5) as $index => $learner) {
    $course = $courses[$index % $courses->count()];
    Certificate::firstOrCreate(
        [
            'user_id' => $learner->id,
            'course_id' => $course->id,
        ],
        [
            'certificate_number' => 'LUM-CERT-2026-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            'issued_at' => now()->subDays(rand(5, 45)),
        ]
    );
}

echo "  Certificates seeded.\n";

$zoomTitles = [
    'HTML & CSS Live Workshop',
    'React Hooks Q&A Session',
    'Database Design Masterclass',
    'JavaScript ES6 Deep Dive',
    'UI/UX Portfolio Review',
];

foreach ($zoomTitles as $index => $title) {
    $course = $courses[$index % $courses->count()];
    ScheduledEvent::firstOrCreate(
        [
            'title' => $title,
            'course_id' => $course->id,
            'platform' => 'zoom',
        ],
        [
            'description' => 'Demo Zoom session for Luminix reports.',
            'instructor_id' => $instructor?->id,
            'created_by' => $ownerId,
            'starts_at' => now()->addDays($index + 2)->setTime(10, 30),
            'ends_at' => now()->addDays($index + 2)->setTime(12, 0),
            'meeting_url' => 'https://zoom.us/j/'.rand(100000000, 999999999),
            'type' => 'class',
            'status' => 'scheduled',
        ]
    );
}

$liveTitles = [
    'Live Coding — Build a REST API',
    'Live Class — CSS Grid Layouts',
    'Live Session — SQL Joins Explained',
    'Live Workshop — Git & GitHub',
    'Live Demo — Deployment Basics',
];

foreach ($liveTitles as $index => $title) {
    $course = $courses[$index % $courses->count()];
    ScheduledEvent::firstOrCreate(
        [
            'title' => $title,
            'course_id' => $course->id,
            'type' => 'class',
        ],
        [
            'description' => 'Demo live class for attendance report.',
            'instructor_id' => $instructor?->id,
            'created_by' => $ownerId,
            'starts_at' => now()->subDays($index)->setTime(15, 0),
            'ends_at' => now()->subDays($index)->setTime(16, 30),
            'platform' => 'google_meet',
            'status' => $index < 3 ? 'completed' : 'scheduled',
        ]
    );
}

echo "  Scheduled events seeded.\n";

foreach ($courses->take(3) as $course) {
    $section = CourseSection::firstOrCreate(
        ['course_id' => $course->id, 'title' => 'Live Sessions'],
        ['sort_order' => 99]
    );

    CourseLesson::firstOrCreate(
        ['course_section_id' => $section->id, 'title' => 'Super Live — '.$course->title],
        [
            'lesson_type' => 'live_class',
            'status' => 'published',
            'sort_order' => 1,
            'duration_minutes' => 60,
        ]
    );
}

echo "  Live class lessons seeded.\n";

$resourceTitles = [
    'JavaScript Cheat Sheet',
    'React Component Patterns PDF',
    'SQL Practice Workbook',
    'CSS Flexbox Guide',
    'API Design Checklist',
];

foreach ($resourceTitles as $index => $title) {
    $resource = Resource::firstOrCreate(
        ['slug' => Str::slug($title)],
        [
            'title' => $title,
            'description' => 'Demo resource for Luminix usage reports.',
            'resource_type' => 'pdf',
            'status' => 'published',
            'published_at' => now()->subDays(30),
        ]
    );

    $learner = $learners[$index % $learners->count()];
    ResourceDownload::firstOrCreate(
        ['resource_id' => $resource->id, 'user_id' => $learner->id],
        ['ip_address' => '127.0.0.1', 'created_at' => now()->subDays(rand(1, 20))]
    );
}

echo "  Resource downloads seeded.\n";

foreach ($learners as $index => $learner) {
    Lead::firstOrCreate(
        ['email' => $learner->email, 'course_id' => $courses[$index % $courses->count()]->id],
        [
            'name' => $learner->name,
            'phone' => $learner->phone,
            'source' => 'website',
            'status' => 'converted',
        ]
    );
    Lead::firstOrCreate(
        ['email' => $learner->email.'+visit', 'course_id' => $courses[$index % $courses->count()]->id],
        [
            'name' => $learner->name,
            'source' => 'landing_page',
            'status' => 'new',
        ]
    );
}

echo "  Leads seeded.\n";

$profile = $company->profile ?? [];
$profile['reports_demo'] = [
    'school_payouts' => [
        ['payout_id' => 'PO-LUM-001', 'transaction_id' => 'TXN-RZP-784512', 'amount' => 12500, 'gateway' => 'razorpay', 'status' => 'completed', 'date' => now()->subDays(12)->format('M d, Y')],
        ['payout_id' => 'PO-LUM-002', 'transaction_id' => 'TXN-RZP-784890', 'amount' => 8750, 'gateway' => 'razorpay', 'status' => 'completed', 'date' => now()->subDays(8)->format('M d, Y')],
        ['payout_id' => 'PO-LUM-003', 'transaction_id' => 'TXN-MNL-002341', 'amount' => 4200, 'gateway' => 'manual', 'status' => 'pending', 'date' => now()->subDays(3)->format('M d, Y')],
        ['payout_id' => 'PO-LUM-004', 'transaction_id' => 'TXN-RZP-785102', 'amount' => 15600, 'gateway' => 'razorpay', 'status' => 'completed', 'date' => now()->subDays(25)->format('M d, Y')],
        ['payout_id' => 'PO-LUM-005', 'transaction_id' => 'TXN-RZP-785445', 'amount' => 9800, 'gateway' => 'razorpay', 'status' => 'processing', 'date' => now()->subDay()->format('M d, Y')],
    ],
    'referral_wallet' => [
        ['learner' => 'Rahul Verma', 'referral_code' => 'RAHUL2026', 'referred_user' => 'Kavya Nair', 'wallet_amount' => 250, 'credit_debit' => 'Credit', 'transaction_type' => 'Referral Bonus', 'date' => now()->subDays(10)->format('M d, Y'), 'status' => 'Completed'],
        ['learner' => 'Dev Patel', 'referral_code' => 'DEVREF50', 'referred_user' => 'Isha Gupta', 'wallet_amount' => 150, 'credit_debit' => 'Credit', 'transaction_type' => 'Referral Bonus', 'date' => now()->subDays(7)->format('M d, Y'), 'status' => 'Completed'],
        ['learner' => 'Nikhil Rao', 'referral_code' => 'NIKHILX', 'referred_user' => '—', 'wallet_amount' => 500, 'credit_debit' => 'Debit', 'transaction_type' => 'Course Purchase', 'date' => now()->subDays(5)->format('M d, Y'), 'status' => 'Completed'],
        ['learner' => 'Isha Gupta', 'referral_code' => 'ISHA100', 'referred_user' => 'Rahul Verma', 'wallet_amount' => 200, 'credit_debit' => 'Credit', 'transaction_type' => 'Referral Bonus', 'date' => now()->subDays(14)->format('M d, Y'), 'status' => 'Completed'],
        ['learner' => 'Kavya Nair', 'referral_code' => 'KAVYA25', 'referred_user' => 'Dev Patel', 'wallet_amount' => 100, 'credit_debit' => 'Credit', 'transaction_type' => 'Signup Reward', 'date' => now()->subDays(2)->format('M d, Y'), 'status' => 'Pending'],
    ],
    'affiliate_products' => [
        ['affiliate' => 'Tech Influencer Hub', 'product' => $courses->first()?->title ?? 'Web Development', 'sales_count' => 12, 'commission' => 3600, 'conversion' => '4.2%', 'payout_status' => 'Paid', 'date' => now()->subDays(15)->format('M d, Y')],
        ['affiliate' => 'CodeWith Priya', 'product' => $courses->skip(1)->first()?->title ?? 'React Course', 'sales_count' => 8, 'commission' => 2400, 'conversion' => '3.1%', 'payout_status' => 'Paid', 'date' => now()->subDays(9)->format('M d, Y')],
        ['affiliate' => 'Learn Daily Blog', 'product' => 'Luminix Full Stack Bundle', 'sales_count' => 5, 'commission' => 4000, 'conversion' => '2.8%', 'payout_status' => 'Pending', 'date' => now()->subDays(4)->format('M d, Y')],
        ['affiliate' => 'Dev Community India', 'product' => $courses->skip(2)->first()?->title ?? 'Database Course', 'sales_count' => 6, 'commission' => 1800, 'conversion' => '3.5%', 'payout_status' => 'Paid', 'date' => now()->subDays(20)->format('M d, Y')],
        ['affiliate' => 'Skill Up Channel', 'product' => $courses->last()?->title ?? 'SEO Course', 'sales_count' => 3, 'commission' => 900, 'conversion' => '1.9%', 'payout_status' => 'Pending', 'date' => now()->subDay()->format('M d, Y')],
    ],
    'affiliates' => [
        ['name' => 'Tech Influencer Hub', 'email' => 'contact@techinfluencer.demo', 'total_referrals' => 28, 'total_sales' => 84000, 'commission_earned' => 8400, 'commission_paid' => 7200, 'pending' => 1200, 'status' => 'Active'],
        ['name' => 'CodeWith Priya', 'email' => 'priya@codewith.demo', 'total_referrals' => 19, 'total_sales' => 57000, 'commission_earned' => 5700, 'commission_paid' => 5700, 'pending' => 0, 'status' => 'Active'],
        ['name' => 'Learn Daily Blog', 'email' => 'editor@learndaily.demo', 'total_referrals' => 14, 'total_sales' => 42000, 'commission_earned' => 4200, 'commission_paid' => 3000, 'pending' => 1200, 'status' => 'Active'],
        ['name' => 'Dev Community India', 'email' => 'partners@devcommunity.demo', 'total_referrals' => 22, 'total_sales' => 66000, 'commission_earned' => 6600, 'commission_paid' => 6600, 'pending' => 0, 'status' => 'Active'],
        ['name' => 'Skill Up Channel', 'email' => 'affiliate@skillup.demo', 'total_referrals' => 8, 'total_sales' => 24000, 'commission_earned' => 2400, 'commission_paid' => 1200, 'pending' => 1200, 'status' => 'Inactive'],
    ],
];

$company->update(['profile' => $profile]);

echo "  Demo payout/referral/affiliate records stored in company profile.\n";
echo "Done. Luminix report data is ready.\n";
