<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyGalleryItem;
use App\Models\CompanyReview;
use App\Models\CompanyTeamMember;
use App\Models\CompanyTestimonial;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoInstitutesAndStudentsSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureDemoMedia();
        $students = $this->seedStudents();
        $this->seedInstitutes($students);
    }

    protected function ensureDemoMedia(): void
    {
        $map = [
            'avatars/priya-sharma.jpg' => 'website/upload/personnel-1-500x500.jpg',
            'avatars/arjun-mehta.jpg' => 'website/upload/personnel-2-500x500.jpg',
            'avatars/sneha-reddy.jpg' => 'website/upload/personnel-3-500x500.jpg',
            'avatars/vikram-patel.jpg' => 'website/upload/personnel-4-500x500.jpg',
            'avatars/ananya-iyer.jpg' => 'website/upload/personnel-5-500x500.jpg',
            'avatars/testimonial-1.jpg' => 'website/upload/personnel-6-500x500.jpg',
            'avatars/testimonial-2.jpg' => 'website/upload/personnel-7-500x500.jpg',
            'avatars/testimonial-3.jpg' => 'website/upload/personnel-1-500x500.jpg',
            'avatars/team-1.jpg' => 'website/upload/personnel-2-500x500.jpg',
            'avatars/team-2.jpg' => 'website/upload/personnel-3-500x500.jpg',
            'avatars/team-3.jpg' => 'website/upload/personnel-4-500x500.jpg',
            'companies/logos/luminix.png' => 'website/upload/partners/wizako.png',
            'companies/logos/nova.png' => 'website/upload/partners/edutap.png',
            'companies/logos/apex.png' => 'website/upload/partners/2iim.png',
            'companies/logos/brightpath.png' => 'website/upload/partners/sleepy-classes.png',
            'companies/logos/skillforge.png' => 'website/upload/partners/deeksha-vedantu.png',
            'companies/covers/luminix.jpg' => 'website/upload/about-title-bg.jpg',
            'companies/covers/nova.jpg' => 'website/upload/life-title-bg.jpg',
            'companies/covers/apex.jpg' => 'website/upload/col-bg-1.jpg',
            'companies/covers/brightpath.jpg' => 'website/upload/image-bg-1.jpg',
            'companies/covers/skillforge.jpg' => 'website/upload/about-bg-1.jpg',
            'companies/gallery/campus-1.jpg' => 'website/upload/slider-hp2-1.jpg',
            'companies/gallery/campus-2.jpg' => 'website/upload/slider-hp2-4.jpg',
            'companies/gallery/campus-3.jpg' => 'website/upload/hp2-col-2.jpg',
            'companies/gallery/lab-1.jpg' => 'website/upload/shutterstock_218235004-600x333.jpg',
            'companies/gallery/event-1.jpg' => 'website/upload/giammarco-boscaro-380907-unsplash.jpg',
        ];

        foreach ($map as $dest => $src) {
            if (Storage::disk('public')->exists($dest)) {
                continue;
            }

            $fullSrc = public_path($src);
            if (! is_file($fullSrc)) {
                continue;
            }

            Storage::disk('public')->put($dest, file_get_contents($fullSrc));
        }
    }

    /**
     * @return array<int, User>
     */
    protected function seedStudents(): array
    {
        $learnerRole = Role::where('slug', 'learner')->firstOrFail();

        $students = [
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@studynest.com',
                'phone' => '+919811122001',
                'address' => 'Flat 12B, Palm Heights, Andheri West, Mumbai, Maharashtra 400053',
                'bio' => 'Aspiring full-stack developer focused on Laravel and React. Preparing for campus placements.',
                'avatar' => 'avatars/priya-sharma.jpg',
                'total_spent' => 4999,
                'notes' => 'High engagement; completed 2 courses.',
            ],
            [
                'name' => 'Arjun Mehta',
                'email' => 'arjun.mehta@studynest.com',
                'phone' => '+919811122002',
                'address' => '42, Residency Road, Bengaluru, Karnataka 560025',
                'bio' => 'Data analytics learner with a background in commerce. Interested in Python and Power BI.',
                'avatar' => 'avatars/arjun-mehta.jpg',
                'total_spent' => 2999,
                'notes' => 'Prefers weekend live classes.',
            ],
            [
                'name' => 'Sneha Reddy',
                'email' => 'sneha.reddy@studynest.com',
                'phone' => '+919811122003',
                'address' => '8-2-293, Road No. 36, Jubilee Hills, Hyderabad, Telangana 500033',
                'bio' => 'UI/UX designer learning product thinking and Figma systems for edtech products.',
                'avatar' => 'avatars/sneha-reddy.jpg',
                'total_spent' => 1999,
                'notes' => 'Active in community discussions.',
            ],
            [
                'name' => 'Vikram Patel',
                'email' => 'vikram.patel@studynest.com',
                'phone' => '+919811122004',
                'address' => '15, CG Road, Navrangpura, Ahmedabad, Gujarat 380009',
                'bio' => 'Working professional upskilling in cloud and DevOps while managing a day job.',
                'avatar' => 'avatars/vikram-patel.jpg',
                'total_spent' => 7999,
                'notes' => 'Corporate learner; invoice required.',
            ],
            [
                'name' => 'Ananya Iyer',
                'email' => 'ananya.iyer@studynest.com',
                'phone' => '+919811122005',
                'address' => '27, T. Nagar Main Road, Chennai, Tamil Nadu 600017',
                'bio' => 'Competitive exam aspirant exploring digital marketing and soft-skills programs.',
                'avatar' => 'avatars/ananya-iyer.jpg',
                'total_spent' => 1499,
                'notes' => 'Referred by institute counsellor.',
            ],
        ];

        $created = [];

        foreach ($students as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'role_id' => $learnerRole->id,
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                    'bio' => $data['bio'],
                    'avatar' => $data['avatar'],
                    'notes' => $data['notes'],
                    'total_spent' => $data['total_spent'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'last_login_at' => now()->subDays(rand(1, 14)),
                ]
            );
            $created[] = $user;
        }

        return $created;
    }

    /**
     * @param  array<int, User>  $students
     */
    protected function seedInstitutes(array $students): void
    {
        $adminRole = Role::where('slug', 'admin')->firstOrFail();

        $institutes = [
            [
                'owner' => [
                    'name' => 'Luminix IT Solution',
                    'email' => 'luminix@studynest.com',
                    'phone' => '+918012345601',
                    'avatar' => 'companies/logos/luminix.png',
                    'bio' => 'Technology training institute specializing in software engineering careers.',
                    'address' => '3rd Floor, Tech Park, Whitefield, Bengaluru, Karnataka 560066',
                ],
                'company' => [
                    'name' => 'Luminix IT Solution',
                    'slug' => 'luminix-it-solution',
                    'tagline' => 'Build job-ready tech careers with mentor-led programs',
                    'about' => "Luminix IT Solution is a Bengaluru-based training institute helping students and career switchers become job-ready software professionals.\n\nOur programs combine live mentoring, hands-on projects, interview preparation, and placement support across Java, Python, web development, and digital marketing tracks.",
                    'logo' => 'companies/logos/luminix.png',
                    'cover_image' => 'companies/covers/luminix.jpg',
                    'email' => 'hello@luminix.academy',
                    'phone' => '+918012345601',
                    'website_url' => 'https://luminix.academy',
                    'address' => '3rd Floor, Tech Park, Whitefield, Bengaluru, Karnataka 560066',
                    'city' => 'Bengaluru',
                    'highlights' => ['Mentor-led cohorts', 'Placement assistance', 'Project portfolio building'],
                    'social_links' => [
                        'website' => 'https://luminix.academy',
                        'youtube' => 'https://youtube.com/@luminix',
                        'linkedin' => 'https://linkedin.com/company/luminix',
                        'instagram' => 'https://instagram.com/luminix',
                    ],
                    'profile' => [
                        'mission' => 'Make high-quality tech education accessible, practical, and outcome-driven.',
                        'vision' => 'Become India’s most trusted career institute for software and digital skills.',
                        'founded_year' => '2018',
                        'state' => 'Karnataka',
                        'country' => 'India',
                        'working_hours' => 'Mon–Sat, 9:00 AM – 7:00 PM',
                        'specialties' => ['Java', 'Python', 'Full Stack', 'Digital Marketing'],
                        'stats' => [
                            ['label' => 'Learners trained', 'value' => '12,000+'],
                            ['label' => 'Hiring partners', 'value' => '180+'],
                            ['label' => 'Avg. placement', 'value' => '92%'],
                        ],
                        'why_us' => [
                            ['title' => 'Industry mentors', 'text' => 'Learn from engineers and marketers who currently work in product companies.', 'icon' => 'fa-user-tie'],
                            ['title' => 'Career labs', 'text' => 'Mock interviews, resume clinics, and weekly hiring drills.', 'icon' => 'fa-briefcase'],
                            ['title' => 'Real projects', 'text' => 'Ship portfolio-ready apps and campaigns every cohort.', 'icon' => 'fa-laptop-code'],
                        ],
                        'faqs' => [
                            ['q' => 'Do you offer placement support?', 'a' => 'Yes. Eligible learners get interview preparation and partner referrals.'],
                            ['q' => 'Are classes online or offline?', 'a' => 'Most programs are hybrid with live online sessions and optional campus labs.'],
                        ],
                        'team' => [],
                        'gallery' => [],
                    ],
                ],
                'team' => [
                    ['name' => 'Rahul Nair', 'role' => 'Founder & Academic Head', 'bio' => 'Ex-product engineer with 12 years in SaaS and edtech.', 'photo' => 'avatars/team-1.jpg'],
                    ['name' => 'Meera Joshi', 'role' => 'Lead Instructor – Backend', 'bio' => 'Specializes in Java, Spring Boot, and system design.', 'photo' => 'avatars/team-2.jpg'],
                    ['name' => 'Imran Khan', 'role' => 'Career Coach', 'bio' => 'Helps learners crack product and service company interviews.', 'photo' => 'avatars/team-3.jpg'],
                ],
                'gallery' => [
                    ['image' => 'companies/gallery/campus-1.jpg', 'caption' => 'Whitefield learning campus'],
                    ['image' => 'companies/gallery/lab-1.jpg', 'caption' => 'Hands-on coding lab'],
                    ['image' => 'companies/gallery/event-1.jpg', 'caption' => 'Alumni hiring day'],
                ],
                'testimonials' => [
                    ['author_name' => 'Kavya Menon', 'author_title' => 'Software Engineer, Infosys', 'content' => 'The Java cohort was structured and practical. Mentors stayed with me until I cleared interviews.', 'rating' => 5, 'avatar' => 'avatars/testimonial-1.jpg'],
                    ['author_name' => 'Devansh Gupta', 'author_title' => 'Junior Developer', 'content' => 'Projects and mock interviews made the biggest difference. I landed my first tech role in 4 months.', 'rating' => 5, 'avatar' => 'avatars/testimonial-2.jpg'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Priya Sharma', 'reviewer_email' => 'priya.sharma@studynest.com', 'rating' => 5, 'content' => 'Excellent mentoring and clear learning path. Highly recommend for career switchers.'],
                    ['reviewer_name' => 'Rohit Das', 'reviewer_email' => 'rohit.das@example.com', 'rating' => 4, 'content' => 'Strong curriculum and supportive faculty. Weekend batches are well managed.'],
                ],
            ],
            [
                'owner' => [
                    'name' => 'Nova Skills Academy',
                    'email' => 'nova@studynest.com',
                    'phone' => '+918012345602',
                    'avatar' => 'companies/logos/nova.png',
                    'bio' => 'Modern skills academy for analytics, design, and business communication.',
                    'address' => 'Level 5, Cyber Hub, Gurugram, Haryana 122002',
                ],
                'company' => [
                    'name' => 'Nova Skills Academy',
                    'slug' => 'nova-skills-academy',
                    'tagline' => 'Upskill for the modern workplace',
                    'about' => "Nova Skills Academy helps professionals and graduates master analytics, UX, and workplace communication through short, outcome-focused programs.\n\nWe blend live workshops, peer learning circles, and career coaching so learners can apply skills immediately at work.",
                    'logo' => 'companies/logos/nova.png',
                    'cover_image' => 'companies/covers/nova.jpg',
                    'email' => 'admissions@novaskills.in',
                    'phone' => '+918012345602',
                    'website_url' => 'https://novaskills.in',
                    'address' => 'Level 5, Cyber Hub, Gurugram, Haryana 122002',
                    'city' => 'Gurugram',
                    'highlights' => ['Weekend intensives', 'Corporate cohorts', 'Career coaching'],
                    'social_links' => [
                        'website' => 'https://novaskills.in',
                        'linkedin' => 'https://linkedin.com/company/novaskills',
                        'instagram' => 'https://instagram.com/novaskills',
                    ],
                    'profile' => [
                        'mission' => 'Help ambitious professionals stay relevant with practical modern skills.',
                        'vision' => 'A trusted upskilling partner for India’s next-generation workforce.',
                        'founded_year' => '2020',
                        'state' => 'Haryana',
                        'country' => 'India',
                        'working_hours' => 'Mon–Fri, 10:00 AM – 6:30 PM',
                        'specialties' => ['Data Analytics', 'UI/UX', 'Business Communication'],
                        'stats' => [
                            ['label' => 'Active learners', 'value' => '6,500+'],
                            ['label' => 'Corporate clients', 'value' => '45+'],
                            ['label' => 'Course completion', 'value' => '94%'],
                        ],
                        'why_us' => [
                            ['title' => 'Short formats', 'text' => 'Finish high-impact skills in 4–8 weeks without quitting your job.', 'icon' => 'fa-clock'],
                            ['title' => 'Practice first', 'text' => 'Every module ends with a workplace-ready assignment.', 'icon' => 'fa-tasks'],
                        ],
                        'faqs' => [
                            ['q' => 'Do you run corporate batches?', 'a' => 'Yes. We run private cohorts for teams of 15 or more.'],
                        ],
                        'team' => [],
                        'gallery' => [],
                    ],
                ],
                'team' => [
                    ['name' => 'Ankit Suri', 'role' => 'Director', 'bio' => 'Former consulting manager focused on capability building.', 'photo' => 'avatars/team-1.jpg'],
                    ['name' => 'Nisha Kapoor', 'role' => 'Head of Design Programs', 'bio' => 'Product designer turned educator.', 'photo' => 'avatars/team-2.jpg'],
                ],
                'gallery' => [
                    ['image' => 'companies/gallery/campus-2.jpg', 'caption' => 'Gurugram studio classroom'],
                    ['image' => 'companies/gallery/event-1.jpg', 'caption' => 'Design critique night'],
                ],
                'testimonials' => [
                    ['author_name' => 'Sneha Reddy', 'author_title' => 'Product Designer', 'content' => 'Nova’s UX program helped me rebuild my portfolio and switch into product design confidently.', 'rating' => 5, 'avatar' => 'avatars/sneha-reddy.jpg'],
                    ['author_name' => 'Farhan Ali', 'author_title' => 'Business Analyst', 'content' => 'Clear teaching, useful templates, and mentors who actually review your work.', 'rating' => 4, 'avatar' => 'avatars/testimonial-3.jpg'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Arjun Mehta', 'reviewer_email' => 'arjun.mehta@studynest.com', 'rating' => 5, 'content' => 'Analytics modules are practical and well paced. Great for working professionals.'],
                    ['reviewer_name' => 'Ishita Bansal', 'reviewer_email' => 'ishita@example.com', 'rating' => 5, 'content' => 'Loved the peer feedback sessions. Felt like a real studio environment.'],
                ],
            ],
            [
                'owner' => [
                    'name' => 'Apex Career Institute',
                    'email' => 'apex@studynest.com',
                    'phone' => '+918012345603',
                    'avatar' => 'companies/logos/apex.png',
                    'bio' => 'Competitive exams and career foundation programs for school and college students.',
                    'address' => 'Plot 18, Baner Road, Pune, Maharashtra 411045',
                ],
                'company' => [
                    'name' => 'Apex Career Institute',
                    'slug' => 'apex-career-institute',
                    'tagline' => 'Foundation to future — guided, focused, proven',
                    'about' => "Apex Career Institute prepares school and college learners for competitive exams and early career pathways with disciplined mentoring and measurable progress tracking.\n\nFrom foundation batches to interview readiness, Apex focuses on consistency, clarity, and confidence.",
                    'logo' => 'companies/logos/apex.png',
                    'cover_image' => 'companies/covers/apex.jpg',
                    'email' => 'contact@apexcareer.in',
                    'phone' => '+918012345603',
                    'website_url' => 'https://apexcareer.in',
                    'address' => 'Plot 18, Baner Road, Pune, Maharashtra 411045',
                    'city' => 'Pune',
                    'highlights' => ['Foundation batches', 'Doubt clinics', 'Progress dashboards'],
                    'social_links' => [
                        'website' => 'https://apexcareer.in',
                        'youtube' => 'https://youtube.com/@apexcareer',
                        'facebook' => 'https://facebook.com/apexcareer',
                    ],
                    'profile' => [
                        'mission' => 'Guide every learner with structure, accountability, and care.',
                        'vision' => 'Be the preferred career institute for ambitious students across West India.',
                        'founded_year' => '2015',
                        'state' => 'Maharashtra',
                        'country' => 'India',
                        'working_hours' => 'Mon–Sun, 8:00 AM – 8:00 PM',
                        'specialties' => ['Foundation', 'Aptitude', 'Soft Skills'],
                        'stats' => [
                            ['label' => 'Students guided', 'value' => '20,000+'],
                            ['label' => 'Campus centres', 'value' => '4'],
                            ['label' => 'Faculty mentors', 'value' => '60+'],
                        ],
                        'why_us' => [
                            ['title' => 'Daily discipline', 'text' => 'Structured study plans with mentor check-ins.', 'icon' => 'fa-calendar-check'],
                            ['title' => 'Parent updates', 'text' => 'Transparent progress reports for families.', 'icon' => 'fa-users'],
                        ],
                        'faqs' => [
                            ['q' => 'Do you offer demo classes?', 'a' => 'Yes, free orientation and demo sessions are available every weekend.'],
                        ],
                        'team' => [],
                        'gallery' => [],
                    ],
                ],
                'team' => [
                    ['name' => 'Suresh Kulkarni', 'role' => 'Principal', 'bio' => '20+ years in academic mentoring and exam coaching.', 'photo' => 'avatars/team-3.jpg'],
                    ['name' => 'Pooja Deshmukh', 'role' => 'Counselling Head', 'bio' => 'Guides students on stream and career choices.', 'photo' => 'avatars/team-2.jpg'],
                ],
                'gallery' => [
                    ['image' => 'companies/gallery/campus-3.jpg', 'caption' => 'Baner campus library'],
                    ['image' => 'companies/gallery/campus-1.jpg', 'caption' => 'Morning foundation batch'],
                ],
                'testimonials' => [
                    ['author_name' => 'Ananya Iyer', 'author_title' => 'College Freshman', 'content' => 'Apex gave me clarity and a study routine I could stick to. Mentors are approachable and strict in a good way.', 'rating' => 5, 'avatar' => 'avatars/ananya-iyer.jpg'],
                    ['author_name' => 'Ritika Shah', 'author_title' => 'Parent', 'content' => 'Weekly progress updates helped us support our daughter better. Very professional institute.', 'rating' => 5, 'avatar' => 'avatars/testimonial-1.jpg'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Vikram Patel', 'reviewer_email' => 'vikram.patel@studynest.com', 'rating' => 4, 'content' => 'Solid foundation teaching and helpful doubt clinics. Campus is well maintained.'],
                    ['reviewer_name' => 'Neel Joshi', 'reviewer_email' => 'neel@example.com', 'rating' => 5, 'content' => 'Faculty are patient and explain concepts clearly. Great environment for focused study.'],
                ],
            ],
            [
                'owner' => [
                    'name' => 'BrightPath Learning',
                    'email' => 'brightpath@studynest.com',
                    'phone' => '+918012345604',
                    'avatar' => 'companies/logos/brightpath.png',
                    'bio' => 'English, communication, and professional readiness programs for young adults.',
                    'address' => '22, Anna Salai, Chennai, Tamil Nadu 600002',
                ],
                'company' => [
                    'name' => 'BrightPath Learning',
                    'slug' => 'brightpath-learning',
                    'tagline' => 'Communicate clearly. Grow confidently.',
                    'about' => "BrightPath Learning focuses on spoken English, professional communication, and interview readiness for college students and early-career professionals.\n\nOur small-batch model ensures every learner gets speaking practice, feedback, and confidence coaching.",
                    'logo' => 'companies/logos/brightpath.png',
                    'cover_image' => 'companies/covers/brightpath.jpg',
                    'email' => 'hello@brightpath.learn',
                    'phone' => '+918012345604',
                    'website_url' => 'https://brightpath.learn',
                    'address' => '22, Anna Salai, Chennai, Tamil Nadu 600002',
                    'city' => 'Chennai',
                    'highlights' => ['Small batches', 'Speaking labs', 'Interview readiness'],
                    'social_links' => [
                        'website' => 'https://brightpath.learn',
                        'instagram' => 'https://instagram.com/brightpath',
                        'youtube' => 'https://youtube.com/@brightpath',
                    ],
                    'profile' => [
                        'mission' => 'Help learners find their voice and communicate with confidence.',
                        'vision' => 'Create confident communicators across South India.',
                        'founded_year' => '2017',
                        'state' => 'Tamil Nadu',
                        'country' => 'India',
                        'working_hours' => 'Mon–Sat, 9:30 AM – 6:30 PM',
                        'specialties' => ['Spoken English', 'Public Speaking', 'Interview Prep'],
                        'stats' => [
                            ['label' => 'Batches completed', 'value' => '850+'],
                            ['label' => 'Avg. batch size', 'value' => '12'],
                            ['label' => 'Learner rating', 'value' => '4.8/5'],
                        ],
                        'why_us' => [
                            ['title' => 'Speak every session', 'text' => 'No passive lectures — every class includes speaking practice.', 'icon' => 'fa-comments'],
                            ['title' => 'Personal feedback', 'text' => 'Recorded reviews and coach notes after key sessions.', 'icon' => 'fa-microphone'],
                        ],
                        'faqs' => [
                            ['q' => 'Is this suitable for beginners?', 'a' => 'Yes. We have beginner, intermediate, and advanced speaking tracks.'],
                        ],
                        'team' => [],
                        'gallery' => [],
                    ],
                ],
                'team' => [
                    ['name' => 'Lakshmi Narayan', 'role' => 'Lead Communication Coach', 'bio' => 'TEFL-certified trainer with corporate facilitation experience.', 'photo' => 'avatars/team-1.jpg'],
                    ['name' => 'Joseph Antony', 'role' => 'Interview Coach', 'bio' => 'Helps learners prepare for campus and lateral interviews.', 'photo' => 'avatars/team-3.jpg'],
                ],
                'gallery' => [
                    ['image' => 'companies/gallery/event-1.jpg', 'caption' => 'Toast night showcase'],
                    ['image' => 'companies/gallery/lab-1.jpg', 'caption' => 'Speaking practice lab'],
                ],
                'testimonials' => [
                    ['author_name' => 'Harini Krishnan', 'author_title' => 'Campus Placement Aspirant', 'content' => 'I used to freeze in interviews. BrightPath’s drills completely changed my confidence.', 'rating' => 5, 'avatar' => 'avatars/testimonial-2.jpg'],
                    ['author_name' => 'Mohammed Faiz', 'author_title' => 'Sales Associate', 'content' => 'Practical English for work situations — emails, calls, and client meetings.', 'rating' => 4, 'avatar' => 'avatars/testimonial-3.jpg'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Sneha Reddy', 'reviewer_email' => 'sneha.reddy@studynest.com', 'rating' => 5, 'content' => 'Friendly coaches and lots of speaking time. Best communication course I have taken.'],
                    ['reviewer_name' => 'Anita Rao', 'reviewer_email' => 'anita@example.com', 'rating' => 5, 'content' => 'Small batches mean real attention. My presentation skills improved within weeks.'],
                ],
            ],
            [
                'owner' => [
                    'name' => 'SkillForge Academy',
                    'email' => 'skillforge@studynest.com',
                    'phone' => '+918012345605',
                    'avatar' => 'companies/logos/skillforge.png',
                    'bio' => 'Hands-on digital skills academy for freelancers and creators.',
                    'address' => 'B-41, Sector 63, Noida, Uttar Pradesh 201301',
                ],
                'company' => [
                    'name' => 'SkillForge Academy',
                    'slug' => 'skillforge-academy',
                    'tagline' => 'Forge digital skills that pay',
                    'about' => "SkillForge Academy trains freelancers, creators, and early entrepreneurs in digital marketing, no-code tools, and content systems that generate real client work.\n\nWe emphasize shipping, client acquisition, and portfolio proof over theory.",
                    'logo' => 'companies/logos/skillforge.png',
                    'cover_image' => 'companies/covers/skillforge.jpg',
                    'email' => 'team@skillforge.academy',
                    'phone' => '+918012345605',
                    'website_url' => 'https://skillforge.academy',
                    'address' => 'B-41, Sector 63, Noida, Uttar Pradesh 201301',
                    'city' => 'Noida',
                    'highlights' => ['Freelance readiness', 'Client projects', 'Creator tools'],
                    'social_links' => [
                        'website' => 'https://skillforge.academy',
                        'instagram' => 'https://instagram.com/skillforge',
                        'youtube' => 'https://youtube.com/@skillforge',
                        'linkedin' => 'https://linkedin.com/company/skillforge',
                    ],
                    'profile' => [
                        'mission' => 'Help independent learners build skills that create income.',
                        'vision' => 'A national hub for creator and freelancer upskilling.',
                        'founded_year' => '2021',
                        'state' => 'Uttar Pradesh',
                        'country' => 'India',
                        'working_hours' => 'Tue–Sun, 11:00 AM – 8:00 PM',
                        'specialties' => ['Digital Marketing', 'No-Code', 'Content Systems'],
                        'stats' => [
                            ['label' => 'Freelance alumni', 'value' => '3,200+'],
                            ['label' => 'Client projects shipped', 'value' => '1,100+'],
                            ['label' => 'Avg. first gig', 'value' => '45 days'],
                        ],
                        'why_us' => [
                            ['title' => 'Ship weekly', 'text' => 'Publish campaigns, sites, or content packs every week.', 'icon' => 'fa-rocket'],
                            ['title' => 'Client simulation', 'text' => 'Practice briefs, revisions, and delivery like real freelancing.', 'icon' => 'fa-handshake'],
                        ],
                        'faqs' => [
                            ['q' => 'Do I need prior experience?', 'a' => 'No. Beginner tracks start from fundamentals and move into paid-project workflows.'],
                        ],
                        'team' => [],
                        'gallery' => [],
                    ],
                ],
                'team' => [
                    ['name' => 'Tara Malhotra', 'role' => 'Founder', 'bio' => 'Growth marketer and freelancer community builder.', 'photo' => 'avatars/team-2.jpg'],
                    ['name' => 'Kabir Sethi', 'role' => 'No-Code Lead', 'bio' => 'Builds client sites and automations with modern tools.', 'photo' => 'avatars/team-1.jpg'],
                ],
                'gallery' => [
                    ['image' => 'companies/gallery/campus-2.jpg', 'caption' => 'Creator studio floor'],
                    ['image' => 'companies/gallery/campus-3.jpg', 'caption' => 'Campaign review board'],
                ],
                'testimonials' => [
                    ['author_name' => 'Vikram Patel', 'author_title' => 'Freelance Marketer', 'content' => 'Within two months I had a portfolio and my first three clients. The brief simulations were gold.', 'rating' => 5, 'avatar' => 'avatars/vikram-patel.jpg'],
                    ['author_name' => 'Shreya Banerjee', 'author_title' => 'Content Creator', 'content' => 'Practical, no fluff. SkillForge taught me systems I still use for client work.', 'rating' => 5, 'avatar' => 'avatars/testimonial-1.jpg'],
                ],
                'reviews' => [
                    ['reviewer_name' => 'Ananya Iyer', 'reviewer_email' => 'ananya.iyer@studynest.com', 'rating' => 5, 'content' => 'Great for digital marketing beginners who want freelance-ready skills fast.'],
                    ['reviewer_name' => 'Kunal Oberoi', 'reviewer_email' => 'kunal@example.com', 'rating' => 4, 'content' => 'Energetic instructors and useful templates. Would love more advanced SEO modules.'],
                ],
            ],
        ];

        foreach ($institutes as $item) {
            $owner = User::updateOrCreate(
                ['email' => $item['owner']['email']],
                [
                    'role_id' => $adminRole->id,
                    'name' => $item['owner']['name'],
                    'phone' => $item['owner']['phone'],
                    'avatar' => $item['owner']['avatar'],
                    'bio' => $item['owner']['bio'],
                    'address' => $item['owner']['address'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $companyData = $item['company'];
            $company = Company::updateOrCreate(
                ['slug' => $companyData['slug']],
                array_merge($companyData, [
                    'owner_user_id' => $owner->id,
                    'is_public' => true,
                ])
            );

            foreach ($item['team'] as $index => $member) {
                CompanyTeamMember::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $member['name'],
                    ],
                    [
                        'role' => $member['role'],
                        'bio' => $member['bio'],
                        'photo' => $member['photo'],
                        'is_published' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }

            foreach ($item['gallery'] as $index => $gallery) {
                CompanyGalleryItem::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'caption' => $gallery['caption'],
                    ],
                    [
                        'image' => $gallery['image'],
                        'is_published' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }

            foreach ($item['testimonials'] as $index => $testimonial) {
                CompanyTestimonial::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'author_name' => $testimonial['author_name'],
                    ],
                    [
                        'author_title' => $testimonial['author_title'],
                        'content' => $testimonial['content'],
                        'rating' => $testimonial['rating'],
                        'avatar' => $testimonial['avatar'],
                        'is_published' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }

            foreach ($item['reviews'] as $review) {
                $student = collect($students)->first(
                    fn (User $user) => strcasecmp($user->email, $review['reviewer_email']) === 0
                );

                CompanyReview::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'reviewer_email' => $review['reviewer_email'],
                    ],
                    [
                        'user_id' => $student?->id,
                        'reviewer_name' => $review['reviewer_name'],
                        'rating' => $review['rating'],
                        'content' => $review['content'],
                        'is_approved' => true,
                    ]
                );
            }
        }
    }
}
