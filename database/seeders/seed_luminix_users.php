<?php

/**
 * Seed Luminix institute user-management demo data.
 * Run: php database/seeders/seed_luminix_users.php
 */

use App\Models\Company;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Group;
use App\Models\LegalDocument;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$company = Company::query()->where('slug', 'luminix-it-solution')->first();
if (! $company || ! $company->owner_user_id) {
    fwrite(STDERR, "Luminix company not found.\n");
    exit(1);
}

$ownerId = (int) $company->owner_user_id;
$learnerRole = Role::where('slug', 'learner')->firstOrFail();
$instructorRole = Role::where('slug', 'instructor')->firstOrFail();
$subAdminRole = Role::where('slug', 'sub-admin')->firstOrFail();
$courseIds = Course::where('created_by', $ownerId)->pluck('id');

echo "Seeding Luminix user data for owner #{$ownerId}...\n";

$learners = [];
$learnerRows = [
    ['name' => 'Rahul Verma', 'email' => 'rahul.verma@luminix.demo', 'phone' => '+919900110001', 'notes' => 'Web development cohort learner.'],
    ['name' => 'Kavya Nair', 'email' => 'kavya.nair@luminix.demo', 'phone' => '+919900110002', 'notes' => 'Interested in UI/UX and React tracks.'],
    ['name' => 'Dev Patel', 'email' => 'dev.patel@luminix.demo', 'phone' => '+919900110003', 'notes' => 'Corporate upskilling learner.'],
    ['name' => 'Isha Gupta', 'email' => 'isha.gupta@luminix.demo', 'phone' => '+919900110004', 'notes' => 'Excel and data analysis focus.'],
    ['name' => 'Nikhil Rao', 'email' => 'nikhil.rao@luminix.demo', 'phone' => '+919900110005', 'notes' => 'SEO and digital marketing learner.'],
];

foreach ($learnerRows as $row) {
    $learners[] = User::firstOrCreate(
        ['email' => $row['email']],
        [
            'role_id' => $learnerRole->id,
            'created_by' => $ownerId,
            'name' => $row['name'],
            'phone' => $row['phone'],
            'notes' => $row['notes'],
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'total_spent' => rand(999, 4999),
        ]
    );
}

foreach ($learners as $index => $learner) {
    $courseId = $courseIds[$index % max($courseIds->count(), 1)] ?? null;
    if (! $courseId) {
        continue;
    }

    CourseEnrollment::firstOrCreate(
        ['user_id' => $learner->id, 'course_id' => $courseId, 'enrollment_type' => 'course'],
        [
            'status' => 'active',
            'access_type' => 'paid',
            'enrolled_at' => now()->subDays(rand(5, 60)),
            'progress' => rand(10, 85),
        ]
    );
}

echo '  Learners: '.count($learners)."\n";

$instructors = [];
$instructorRows = [
    ['name' => 'Meera Joshi', 'email' => 'meera.joshi@luminix.demo', 'bio' => 'Lead instructor for backend and Java programs.'],
    ['name' => 'Arun Krishnan', 'email' => 'arun.krishnan@luminix.demo', 'bio' => 'Frontend specialist teaching React and UI systems.'],
    ['name' => 'Pooja Desai', 'email' => 'pooja.desai@luminix.demo', 'bio' => 'Digital marketing and SEO mentor.'],
    ['name' => 'Sanjay Menon', 'email' => 'sanjay.menon@luminix.demo', 'bio' => 'Database and analytics instructor.'],
    ['name' => 'Neha Kapoor', 'email' => 'neha.kapoor@luminix.demo', 'bio' => 'UI/UX design and product thinking coach.'],
];

foreach ($instructorRows as $index => $row) {
    $instructor = User::firstOrCreate(
        ['email' => $row['email']],
        [
            'role_id' => $instructorRole->id,
            'created_by' => $ownerId,
            'name' => $row['name'],
            'bio' => $row['bio'],
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]
    );
    $instructors[] = $instructor;

    $courseId = $courseIds[$index % max($courseIds->count(), 1)] ?? null;
    if ($courseId) {
        $instructor->courses()->syncWithoutDetaching([$courseId => ['is_primary' => $index === 0]]);
    }
}

echo '  Instructors: '.count($instructors)."\n";

$subAdmins = [];
$subAdminRows = [
    ['name' => 'Amit Shah', 'email' => 'amit.shah@luminix.demo'],
    ['name' => 'Ritu Singh', 'email' => 'ritu.singh@luminix.demo'],
    ['name' => 'Karan Malhotra', 'email' => 'karan.malhotra@luminix.demo'],
    ['name' => 'Divya Iyer', 'email' => 'divya.iyer@luminix.demo'],
    ['name' => 'Mohit Agarwal', 'email' => 'mohit.agarwal@luminix.demo'],
];

foreach ($subAdminRows as $row) {
    $subAdmins[] = User::firstOrCreate(
        ['email' => $row['email']],
        [
            'role_id' => $subAdminRole->id,
            'created_by' => $ownerId,
            'name' => $row['name'],
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]
    );
}

echo '  Sub-admins: '.count($subAdmins)."\n";

$groups = [];
$groupRows = [
    ['name' => 'Web Dev Cohort 2026', 'description' => 'Learners enrolled in full-stack and frontend programs.'],
    ['name' => 'Weekend Batch Learners', 'description' => 'Working professionals attending weekend sessions.'],
    ['name' => 'Placement Track', 'description' => 'Learners preparing for interviews and portfolio reviews.'],
    ['name' => 'Digital Marketing Group', 'description' => 'SEO, content, and analytics learners.'],
    ['name' => 'Design Studio Group', 'description' => 'UI/UX and product design learners.'],
];

foreach ($groupRows as $index => $row) {
    $group = Group::firstOrCreate(
        ['slug' => \Illuminate\Support\Str::slug($row['name'])],
        [
            'name' => $row['name'],
            'description' => $row['description'],
            'is_active' => true,
            'created_by' => $ownerId,
        ]
    );
    $groups[] = $group;

    if (isset($learners[$index])) {
        $group->learners()->syncWithoutDetaching([$learners[$index]->id]);
    }
    if (isset($learners[$index + 1])) {
        $group->learners()->syncWithoutDetaching([$learners[$index + 1]->id]);
    }

    $courseId = $courseIds[$index % max($courseIds->count(), 1)] ?? null;
    if ($courseId) {
        $group->courses()->syncWithoutDetaching([$courseId]);
    }
}

echo '  Groups: '.count($groups)."\n";

$contacts = [];
$contactRows = [
    ['name' => 'Aditi Sharma', 'email' => 'aditi@techcorp.in', 'phone' => '+919811100001', 'organization' => 'TechCorp India', 'contact_type' => 'lead', 'notes' => 'Interested in corporate Java training.'],
    ['name' => 'Rohit Bansal', 'email' => 'rohit@campusconnect.edu', 'phone' => '+919811100002', 'organization' => 'Campus Connect', 'contact_type' => 'partner', 'notes' => 'College partnership enquiry.'],
    ['name' => 'Sunita Rao', 'email' => 'sunita@parentsforum.org', 'phone' => '+919811100003', 'organization' => 'Parents Forum', 'contact_type' => 'customer', 'notes' => 'Enrolled two learners last quarter.'],
    ['name' => 'Harish Mehta', 'email' => 'harish@skillbridge.co', 'phone' => '+919811100004', 'organization' => 'SkillBridge Co.', 'contact_type' => 'vendor', 'notes' => 'Provides lab infrastructure support.'],
    ['name' => 'Lakshmi Pillai', 'email' => 'lakshmi@futurelearn.in', 'phone' => '+919811100005', 'organization' => 'FutureLearn India', 'contact_type' => 'lead', 'notes' => 'Requested demo for React bootcamp.'],
];

foreach ($contactRows as $row) {
    $contacts[] = Contact::firstOrCreate(
        ['email' => $row['email'], 'created_by' => $ownerId],
        array_merge($row, ['is_active' => true, 'created_by' => $ownerId])
    );
}

echo '  Contacts: '.count($contacts)."\n";

$documents = [];
$documentRows = [
    ['title' => 'Privacy Policy', 'document_type' => 'privacy_policy', 'version' => '2.1', 'status' => 'published', 'content' => 'This privacy policy explains how Luminix IT Solution collects and uses learner data.'],
    ['title' => 'Terms of Service', 'document_type' => 'terms_of_service', 'version' => '1.4', 'status' => 'published', 'content' => 'By using Luminix academy services, learners agree to these terms of service.'],
    ['title' => 'Refund Policy', 'document_type' => 'refund_policy', 'version' => '1.2', 'status' => 'published', 'content' => 'Refunds are processed within 7 business days for eligible paid programs.'],
    ['title' => 'Learner Agreement', 'document_type' => 'user_agreement', 'version' => '1.0', 'status' => 'published', 'content' => 'Learners must maintain academic integrity and follow platform guidelines.'],
    ['title' => 'Instructor Code of Conduct', 'document_type' => 'other', 'version' => '1.1', 'status' => 'draft', 'content' => 'Guidelines for instructors delivering live and recorded sessions.'],
];

foreach ($documentRows as $row) {
    $documents[] = LegalDocument::firstOrCreate(
        ['title' => $row['title'], 'created_by' => $ownerId],
        array_merge($row, [
            'created_by' => $ownerId,
            'effective_date' => now()->subMonths(rand(1, 6))->toDateString(),
        ])
    );
}

echo '  Legal documents: '.count($documents)."\n";
echo "Done.\n";
