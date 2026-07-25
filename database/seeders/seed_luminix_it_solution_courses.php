<?php

/**
 * Add published courses with lessons for luminix-it-solution.
 * Run: php database/seeders/seed_luminix_it_solution_courses.php
 */

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use Illuminate\Support\Str;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$company = Company::query()->where('slug', 'luminix-it-solution')->first();
if (! $company || ! $company->owner_user_id) {
    fwrite(STDERR, "Company luminix-it-solution not found.\n");
    exit(1);
}

$ownerId = (int) $company->owner_user_id;

$catalog = [
    [
        'slug' => 'intro-to-web-development',
        'title' => 'Introduction to Web Development',
        'subtitle' => 'Build your first website with HTML, CSS & JavaScript',
        'category_id' => 1,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 1,
        'sale_price' => null,
        'enrollment_count' => 86,
        'thumbnail' => 'website/images/courses/web-design-starter-luminix.jpg',
        'description' => "Start your web development journey with a practical, beginner-friendly course.\n\nYou will learn HTML structure, CSS styling, responsive layouts, and JavaScript basics — then publish a small multi-page website.\n\nIdeal for students and career starters who want clear, hands-on lessons.",
        'sections' => [
            'HTML Foundations' => [
                'Welcome & Course Overview',
                'Document Structure & Semantic Tags',
                'Links, Images, Lists & Forms',
            ],
            'CSS Styling' => [
                'Selectors, Colors & Typography',
                'Box Model & Flexbox Layout',
            ],
            'JavaScript Basics' => [
                'Variables, Functions & DOM Events',
                'Mini Project: Personal Portfolio Page',
            ],
        ],
    ],
    [
        'slug' => 'react-frontend-essentials-luminix',
        'title' => 'React.js Frontend Essentials',
        'subtitle' => 'Build modern UI with components, hooks, and routing',
        'category_id' => 1,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 4999,
        'sale_price' => 2999,
        'enrollment_count' => 154,
        'thumbnail' => 'website/images/courses/fullstack-web-bootcamp-luminix.jpg',
        'description' => "Learn React from scratch and build interactive single-page applications.\n\nThis course covers components, props, state, hooks, forms, and React Router with practical mini projects used in real product teams.\n\nBest for developers who already know basic HTML, CSS, and JavaScript.",
        'sections' => [
            'React Fundamentals' => [
                'Why React & Project Setup',
                'JSX, Components & Props',
                'State & Event Handling',
            ],
            'Hooks & UI Patterns' => [
                'useEffect & Data Fetching',
                'Forms & Controlled Inputs',
            ],
            'Routing & Project' => [
                'React Router Basics',
                'Capstone: Product Listing App',
            ],
        ],
    ],
    [
        'slug' => 'mysql-database-fundamentals-luminix',
        'title' => 'MySQL & Database Fundamentals',
        'subtitle' => 'Design tables, write SQL queries, and model real data',
        'category_id' => 1,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 2499,
        'sale_price' => 1999,
        'enrollment_count' => 121,
        'thumbnail' => 'website/images/courses/data-analytics-trial-luminix.jpg',
        'description' => "Master the database skills every backend and full-stack developer needs.\n\nYou will design schemas, write SELECT/JOIN queries, use indexes wisely, and model relationships for real applications like e-commerce and LMS systems.\n\nNo prior SQL experience required.",
        'sections' => [
            'Database Basics' => [
                'What Databases Solve',
                'Tables, Rows & Data Types',
                'Primary Keys & Constraints',
            ],
            'Querying Data' => [
                'SELECT, WHERE & ORDER BY',
                'JOINs Explained Simply',
                'Aggregations & GROUP BY',
            ],
            'Design Practice' => [
                'Mini Project: Course Catalog Schema',
            ],
        ],
    ],
    [
        'slug' => 'seo-content-marketing-luminix',
        'title' => 'SEO & Content Marketing',
        'subtitle' => 'Grow organic traffic with search-friendly content strategy',
        'category_id' => 4,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 3499,
        'sale_price' => 2499,
        'enrollment_count' => 203,
        'thumbnail' => 'website/images/courses/digital-marketing-pro-luminix.jpg',
        'description' => "Learn how to plan, write, and optimize content that ranks and converts.\n\nThis course covers keyword research, on-page SEO, blog frameworks, internal linking, and a simple monthly content calendar for institutes and startups.\n\nPractical worksheets included in each module.",
        'sections' => [
            'SEO Foundations' => [
                'How Search Engines Work',
                'Keyword Research Workflow',
                'Competitor Content Analysis',
            ],
            'On-Page Optimization' => [
                'Title Tags, Meta & Headings',
                'Internal Linking Strategy',
            ],
            'Content Systems' => [
                'Blog Outline Frameworks',
                '30-Day Content Calendar Project',
            ],
        ],
    ],
    [
        'slug' => 'excel-data-analysis-luminix',
        'title' => 'Excel for Data Analysis',
        'subtitle' => 'Clean data, build dashboards, and present insights with Excel',
        'category_id' => 2,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 1999,
        'sale_price' => 1499,
        'enrollment_count' => 278,
        'thumbnail' => 'website/images/courses/business-communication-trial-luminix.jpg',
        'description' => "Become confident with Excel for business reporting and analysis.\n\nYou will clean messy sheets, use formulas, PivotTables, charts, and build a dashboard that stakeholders can actually understand.\n\nGreat for analysts, operations, marketing, and students preparing for internships.",
        'sections' => [
            'Excel Essentials' => [
                'Workbook Navigation & Best Practices',
                'Cleanup: Text, Dates & Duplicates',
            ],
            'Analysis Tools' => [
                'Must-Know Formulas',
                'PivotTables for Insights',
                'Charts that Tell a Story',
            ],
            'Dashboard Project' => [
                'Build a Sales Performance Dashboard',
            ],
        ],
    ],
    [
        'slug' => 'uiux-design-fundamentals-luminix',
        'title' => 'UI/UX Design Fundamentals',
        'subtitle' => 'Design clean interfaces with user research, wireframes & Figma',
        'category_id' => 3,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 3999,
        'sale_price' => 2799,
        'enrollment_count' => 167,
        'thumbnail' => 'website/images/courses/uiux-product-design-luminix.jpg',
        'description' => "Learn how to design products people enjoy using.\n\nThis course covers UX research basics, user flows, wireframing, visual hierarchy, and Figma essentials — ending with a portfolio-ready mobile app concept.\n\nIdeal for beginners exploring design careers and developers who want stronger UI skills.",
        'sections' => [
            'UX Foundations' => [
                'What Great UX Looks Like',
                'User Research & Personas',
                'User Flows & Journey Maps',
            ],
            'Wireframes & UI' => [
                'Low-Fidelity Wireframes',
                'Visual Hierarchy & Components',
            ],
            'Figma Project' => [
                'Figma Basics for Designers',
                'Capstone: Mobile App Concept',
            ],
        ],
    ],
];

function rebuildCourse(int $ownerId, array $data): Course
{
    $course = Course::withTrashed()->where('slug', $data['slug'])->first();

    $payload = [
        'category_id' => $data['category_id'],
        'created_by' => $ownerId,
        'title' => $data['title'],
        'subtitle' => $data['subtitle'],
        'slug' => $data['slug'],
        'description' => $data['description'],
        'thumbnail' => $data['thumbnail'],
        'product_type' => 'course',
        'price' => $data['price'],
        'sale_price' => $data['sale_price'],
        'is_free' => $data['is_free'],
        'access_type' => $data['access_type'],
        'status' => 'published',
        'drip_enabled' => false,
        'enrollment_count' => $data['enrollment_count'],
        'seo_title' => $data['title'].' | Luminix IT Solution',
        'seo_description' => Str::limit($data['subtitle'], 150),
        'deleted_at' => null,
    ];

    if ($course) {
        // Only claim existing course if it already belongs to this institute or is the intro course we created.
        if ($course->created_by && (int) $course->created_by !== $ownerId && $data['slug'] !== 'intro-to-web-development') {
            throw new RuntimeException("Slug {$data['slug']} already used by another institute.");
        }
        $course->fill($payload)->save();
    } else {
        $course = Course::create($payload);
    }

    foreach ($course->sections()->with('lessons')->get() as $section) {
        $section->lessons()->delete();
        $section->delete();
    }

    $sectionOrder = 1;
    foreach ($data['sections'] as $sectionTitle => $lessons) {
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => $sectionTitle,
            'description' => null,
            'sort_order' => $sectionOrder++,
            'status' => 'active',
        ]);

        foreach (array_values($lessons) as $i => $lessonTitle) {
            CourseLesson::create([
                'course_section_id' => $section->id,
                'title' => $lessonTitle,
                'lesson_type' => $i === 0 ? 'video' : 'text',
                'status' => 'published',
                'content' => "In this lesson you will learn: {$lessonTitle}.\n\nFollow the examples, complete the practice task, and mark the lesson complete when ready.",
                'duration_minutes' => 12 + ($i * 4),
                'is_preview' => $sectionOrder === 2 && $i === 0,
                'is_locked' => false,
                'sort_order' => $i + 1,
                'drip_enabled' => false,
                'completion_required' => true,
                'allow_download' => false,
            ]);
        }
    }

    return $course->fresh(['sections.lessons']);
}

echo "Seeding courses for {$company->name} (owner #{$ownerId})\n\n";

foreach ($catalog as $item) {
    $thumb = public_path($item['thumbnail']);
    if (! is_file($thumb)) {
        fwrite(STDERR, "Missing thumbnail: {$item['thumbnail']}\n");
        exit(1);
    }

    $course = rebuildCourse($ownerId, $item);
    $lessonCount = $course->sections->sum(fn ($s) => $s->lessons->count());
    echo sprintf(
        "OK  %s | lessons=%d | %s | /courses/%s\n",
        $course->title,
        $lessonCount,
        $course->displayPrice(),
        $course->slug
    );
}

$total = Course::query()->where('created_by', $ownerId)->where('status', 'published')->count();
echo "\nPublished courses for institute: {$total}\n";
