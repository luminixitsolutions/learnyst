<?php

/**
 * Seed Luminix institute courses for demo presentation.
 * Run: php database/seeders/seed_luminix_courses.php
 */

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseFaq;
use App\Models\CourseLesson;
use App\Models\CourseReview;
use App\Models\CourseSection;
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

$catalog = [
    [
        'slug' => 'java-Q8mSk',
        'title' => 'Java Programming Masterclass',
        'subtitle' => 'Learn core Java, OOP, and build real console apps from scratch.',
        'category_id' => 1,
        'access_type' => 'free',
        'is_free' => true,
        'price' => 0,
        'sale_price' => null,
        'validity_days' => null,
        'enrollment_count' => 248,
        'thumbnail' => 'website/images/courses/java-Q8mSk.jpg',
        'description' => "This free Java masterclass takes you from absolute basics to confident OOP coding.\n\nYou will learn syntax, control flow, classes, inheritance, collections, and exception handling with practical examples used in real academy projects.\n\nIdeal for beginners who want a clear, structured path into software development.",
        'sections' => [
            'Getting Started with Java' => ['Install JDK & IDE', 'First Hello World Program', 'Variables & Data Types', 'Operators & Expressions'],
            'Control Flow & Methods' => ['If / Else & Switch', 'Loops in Practice', 'Writing Reusable Methods', 'Debugging Basics'],
            'Object Oriented Java' => ['Classes & Objects', 'Constructors', 'Inheritance & Polymorphism', 'Mini Project: Student Manager'],
        ],
        'faqs' => [
            ['Do I need prior coding experience?', 'No. This course starts from absolute basics and builds step by step.'],
            ['Is this course really free?', 'Yes. You get lifetime free access to all published lessons.'],
        ],
        'reviews' => [
            ['Asha Verma', 5, 'Clear explanations and practical Java examples. Highly recommended for beginners.'],
            ['Rohit Singh', 4, 'Good curriculum structure. The projects helped me understand OOP concepts quickly.'],
            ['Neha Patel', 5, 'Best free Java starter I have taken. Lessons are short and useful.'],
        ],
    ],
    [
        'slug' => 'python-fundamentals-luminix',
        'title' => 'Python Fundamentals for Beginners',
        'subtitle' => 'Start coding with Python â€” syntax, logic, and mini automation scripts.',
        'category_id' => 1,
        'access_type' => 'free',
        'is_free' => true,
        'price' => 0,
        'sale_price' => null,
        'validity_days' => null,
        'enrollment_count' => 412,
        'thumbnail' => 'website/images/courses/python-fundamentals-luminix.jpg',
        'description' => "Python is the friendliest way to begin programming. This free course covers the essentials with hands-on exercises.\n\nYou will write scripts, work with lists and dictionaries, handle files, and finish with a mini automation project.\n\nPerfect for students, career switchers, and non-tech learners.",
        'sections' => [
            'Python Basics' => ['Setup & First Script', 'Numbers & Strings', 'Lists & Dictionaries', 'Input / Output'],
            'Logic & Functions' => ['Conditions', 'Loops', 'Functions & Modules', 'Error Handling'],
            'Mini Projects' => ['File Organizer Script', 'Simple Quiz App', 'CSV Report Generator'],
        ],
        'faqs' => [
            ['Which Python version is used?', 'Python 3.11+ examples are used throughout the course.'],
            ['Will I get a certificate?', 'Yes, after completing all required lessons.'],
        ],
        'reviews' => [
            ['Karan Mehta', 5, 'Simple language and great exercises. I built my first script in week one.'],
            ['Sneha Iyer', 5, 'Loved the mini projects. Very practical for beginners.'],
        ],
    ],
    [
        'slug' => 'web-design-starter-luminix',
        'title' => 'Modern Web Design Starter',
        'subtitle' => 'HTML, CSS, and responsive layouts for beautiful academy landing pages.',
        'category_id' => 3,
        'access_type' => 'free',
        'is_free' => true,
        'price' => 0,
        'sale_price' => null,
        'validity_days' => null,
        'enrollment_count' => 189,
        'thumbnail' => 'website/images/courses/web-design-starter-luminix.jpg',
        'description' => "Design clean, responsive web pages without overwhelm.\n\nLearn semantic HTML, modern CSS layout techniques, typography, spacing, and mobile-first design patterns used by professional academies.\n\nBy the end you will ship a polished multi-section landing page.",
        'sections' => [
            'HTML Foundations' => ['Page Structure', 'Text & Media', 'Forms & Accessibility'],
            'CSS Layouts' => ['Flexbox Essentials', 'CSS Grid Studio', 'Responsive Breakpoints'],
            'Build a Landing Page' => ['Hero & Navigation', 'Feature Sections', 'Final Polish & Publish'],
        ],
        'faqs' => [
            ['Do I need design software?', 'No. A code editor and browser are enough.'],
        ],
        'reviews' => [],
    ],
    [
        'slug' => 'fullstack-web-bootcamp-luminix',
        'title' => 'Full-Stack Web Development Bootcamp',
        'subtitle' => 'Frontend + backend + database â€” build and deploy production-ready apps.',
        'category_id' => 1,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 14999,
        'sale_price' => 9999,
        'validity_days' => 365,
        'enrollment_count' => 156,
        'thumbnail' => 'website/images/courses/fullstack-web-bootcamp-luminix.jpg',
        'description' => "A complete paid bootcamp for serious learners who want job-ready full-stack skills.\n\nYou will master HTML/CSS/JS, React fundamentals, REST APIs, databases, authentication, and deployment.\n\nIncludes mentor-style walkthroughs, assignments, and a final capstone project you can showcase.",
        'sections' => [
            'Frontend Foundations' => ['Modern JavaScript', 'DOM & Fetch API', 'React Basics', 'Component Patterns'],
            'Backend & APIs' => ['Node/Laravel API Intro', 'Auth & Sessions', 'CRUD APIs', 'Validation & Security'],
            'Database & Deploy' => ['SQL Essentials', 'Eloquent / ORM Patterns', 'Hosting & CI Basics', 'Capstone: LMS Lite'],
        ],
        'faqs' => [
            ['Is there a refund policy?', 'Yes. Request within 7 days if less than 20% of content is completed.'],
            ['How long do I get access?', '12 months from enrollment, with optional renewal.'],
        ],
        'reviews' => [
            ['Vikram Shah', 5, 'Worth every rupee. Capstone project helped me crack interviews.'],
            ['Ananya Rao', 4, 'Dense but excellent. Mentorship-style videos are clear and practical.'],
            ['Imran Ali', 5, 'Best paid web course from Luminix. Curriculum matches industry needs.'],
        ],
    ],
    [
        'slug' => 'digital-marketing-pro-luminix',
        'title' => 'Digital Marketing Pro Certificate',
        'subtitle' => 'SEO, ads, funnels, and analytics to grow institute enrollments.',
        'category_id' => 4,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 7999,
        'sale_price' => 5999,
        'validity_days' => 180,
        'enrollment_count' => 97,
        'thumbnail' => 'website/images/courses/digital-marketing-pro-luminix.jpg',
        'description' => "Grow your academy with a practical digital marketing system.\n\nLearn SEO for course pages, Google/Meta ads basics, landing page conversion, email nurturing, and analytics dashboards.\n\nBuilt for educators, founders, and institute marketers.",
        'sections' => [
            'Growth Foundations' => ['Marketing Funnel Map', 'Offer Positioning', 'Audience Research'],
            'Acquisition Channels' => ['SEO for Courses', 'Paid Ads Starter', 'Content & Social'],
            'Conversion & Analytics' => ['Landing Page CRO', 'Email Sequences', 'Tracking & ROI'],
        ],
        'faqs' => [
            ['Is ad spend included?', 'No. Ad budgets are separate; we teach setup and optimization.'],
        ],
        'reviews' => [
            ['Priya Nair', 5, 'Applied the funnel framework and our demo bookings doubled in a month.'],
        ],
    ],
    [
        'slug' => 'uiux-product-design-luminix',
        'title' => 'UI/UX Product Design Studio',
        'subtitle' => 'Wireframes to high-fidelity UI with Figma workflows for product teams.',
        'category_id' => 3,
        'access_type' => 'paid',
        'is_free' => false,
        'price' => 8999,
        'sale_price' => null,
        'validity_days' => 365,
        'enrollment_count' => 64,
        'thumbnail' => 'website/images/courses/uiux-product-design-luminix.jpg',
        'description' => "Design beautiful, usable digital products with a professional studio process.\n\nCover research, user flows, wireframing, visual design systems, prototyping, and handoff.\n\nIncludes Figma practice files and a portfolio-ready case study.",
        'sections' => [
            'Discover & Define' => ['UX Research Basics', 'Personas & Jobs', 'User Flows'],
            'Design in Figma' => ['Wireframes', 'UI Kits & Tokens', 'Responsive Frames'],
            'Prototype & Handoff' => ['Interactive Prototypes', 'Usability Testing', 'Dev Handoff Pack'],
        ],
        'faqs' => [
            ['Do I need a paid Figma plan?', 'Free Figma plan is enough for all course exercises.'],
        ],
        'reviews' => [],
    ],
    [
        'slug' => 'data-analytics-trial-luminix',
        'title' => 'Data Analytics Essentials (7-Day Trial)',
        'subtitle' => 'Try Excel + SQL + dashboards free for 7 days, then upgrade to full access.',
        'category_id' => 2,
        'access_type' => 'trial',
        'is_free' => false,
        'price' => 4999,
        'sale_price' => 3499,
        'validity_days' => 7,
        'enrollment_count' => 221,
        'thumbnail' => 'website/images/courses/data-analytics-trial-luminix.jpg',
        'description' => "Start with a 7-day trial and explore core data analytics skills risk-free.\n\nLearn Excel analysis, introductory SQL, chart storytelling, and a starter dashboard workflow.\n\nAfter trial, unlock the complete paid curriculum and projects.",
        'sections' => [
            'Trial Week Kickoff' => ['Analytics Mindset', 'Excel Cleanup Toolkit', 'First Dashboard'],
            'SQL Foundations' => ['SELECT & Filters', 'Joins Explained', 'Aggregations'],
            'Storytelling with Data' => ['Chart Selection', 'Insight Writing', 'Stakeholder Demo'],
        ],
        'faqs' => [
            ['What happens after 7 days?', 'You can upgrade to the full paid plan to keep learning.'],
            ['Can I cancel during trial?', 'Yes. Trial access simply expires if you do not upgrade.'],
        ],
        'reviews' => [
            ['Deepak Joshi', 4, 'Trial was enough to judge quality. Upgraded immediately for SQL modules.'],
            ['Meera Kapoor', 5, 'Great trial experience. Dashboards module is excellent.'],
        ],
    ],
    [
        'slug' => 'cloud-devops-trial-luminix',
        'title' => 'Cloud & DevOps Starter (Trial)',
        'subtitle' => '14-day trial of Linux, Docker, CI/CD, and cloud deploy basics.',
        'category_id' => 1,
        'access_type' => 'trial',
        'is_free' => false,
        'price' => 12999,
        'sale_price' => 8999,
        'validity_days' => 14,
        'enrollment_count' => 83,
        'thumbnail' => 'website/images/courses/cloud-devops-trial-luminix.jpg',
        'description' => "Explore Cloud & DevOps with a 14-day trial before committing.\n\nCover Linux essentials, containers, basic CI pipelines, and a simple cloud deploy path.\n\nDesigned for developers and IT learners moving toward DevOps roles.",
        'sections' => [
            'Ops Foundations' => ['Linux Crash Course', 'Git for Deployments', 'Servers & Networking Basics'],
            'Containers' => ['Docker Images', 'Compose for Local Apps', 'Registry Workflow'],
            'CI/CD Lite' => ['Pipeline Anatomy', 'Automated Tests', 'Ship a Sample App'],
        ],
        'faqs' => [
            ['Do I need a cloud account?', 'A free-tier cloud account is recommended for deploy lessons.'],
        ],
        'reviews' => [],
    ],
    [
        'slug' => 'business-communication-trial-luminix',
        'title' => 'Business Communication for Professionals',
        'subtitle' => 'Trial access to emails, presentations, and stakeholder communication skills.',
        'category_id' => 2,
        'access_type' => 'trial',
        'is_free' => false,
        'price' => 2999,
        'sale_price' => 1999,
        'validity_days' => 10,
        'enrollment_count' => 134,
        'thumbnail' => 'website/images/courses/business-communication-trial-luminix.jpg',
        'description' => "Communicate with clarity in workplace and client settings.\n\nPractice professional emails, meeting agendas, presentation storytelling, and difficult conversations.\n\nStart with trial access, then unlock the complete coaching-style curriculum.",
        'sections' => [
            'Written Clarity' => ['Email Frameworks', 'Status Updates', 'Proposal Writing'],
            'Spoken Impact' => ['Meeting Presence', 'Presentation Flow', 'Handling Questions'],
            'Stakeholder Skills' => ['Feedback Conversations', 'Conflict Basics', 'Personal Brand Voice'],
        ],
        'faqs' => [
            ['Is this useful for students too?', 'Yes. Templates work for internships, placements, and client work.'],
        ],
        'reviews' => [],
    ],
];

function upsertCourse(int $ownerId, array $data): Course
{
    $course = Course::withTrashed()->where('slug', $data['slug'])->first();

    $payload = [
        'category_id' => $data['category_id'],
        'created_by' => $ownerId,
        'title' => $data['title'],
        'subtitle' => $data['subtitle'],
        'slug' => $data['slug'],
        'description' => $data['description'],
        'product_type' => 'course',
        'price' => $data['price'],
        'sale_price' => $data['sale_price'],
        'is_free' => $data['is_free'],
        'access_type' => $data['access_type'],
        'validity_days' => $data['validity_days'],
        'status' => 'published',
        'drip_enabled' => false,
        'enrollment_count' => $data['enrollment_count'],
        'seo_title' => $data['title'].' | Luminix IT Solution',
        'seo_description' => Str::limit($data['subtitle'], 150),
        'deleted_at' => null,
    ];

    if (! empty($data['thumbnail'])) {
        $payload['thumbnail'] = $data['thumbnail'];
    }

    if ($course) {
        $course->fill($payload)->save();
    } else {
        $course = Course::create($payload);
    }

    // Rebuild curriculum for a clean demo presentation.
    foreach ($course->sections()->with('lessons')->get() as $section) {
        $section->lessons()->delete();
        $section->delete();
    }
    $course->faqs()->delete();

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
                'content' => 'Lesson overview and practice notes for: '.$lessonTitle,
                'duration_minutes' => 12 + ($i * 3),
                'is_preview' => $i === 0,
                'is_locked' => false,
                'sort_order' => $i + 1,
                'drip_enabled' => false,
                'completion_required' => true,
                'allow_download' => false,
            ]);
        }
    }

    foreach (array_values($data['faqs']) as $i => $faq) {
        CourseFaq::create([
            'course_id' => $course->id,
            'question' => $faq[0],
            'answer' => $faq[1],
            'sort_order' => $i + 1,
            'is_active' => true,
        ]);
    }

    // Replace demo reviews for selected courses.
    if (! empty($data['reviews'])) {
        $course->reviews()->forceDelete();
        foreach ($data['reviews'] as $review) {
            CourseReview::create([
                'course_id' => $course->id,
                'user_id' => null,
                'reviewer_name' => $review[0],
                'reviewer_email' => null,
                'rating' => $review[1],
                'review' => $review[2],
                'is_anonymous' => false,
                'status' => 'approved',
            ]);
        }
    }

    return $course->fresh(['sections.lessons', 'reviews', 'faqs']);
}

$created = [];
foreach ($catalog as $item) {
    $course = upsertCourse($ownerId, $item);
    $created[] = sprintf(
        '%s [%s] lessons=%d reviews=%d faq=%d price=%s',
        $course->slug,
        $course->access_type,
        $course->sections->sum(fn ($s) => $s->lessons->count()),
        $course->reviews->where('status', 'approved')->count(),
        $course->faqs->count(),
        $course->displayPrice()
    );
}

echo "Seeded ".count($created)." Luminix courses:\n";
foreach ($created as $line) {
    echo " - {$line}\n";
}
echo "Institute page: /companies/luminix-it-solution\n";

