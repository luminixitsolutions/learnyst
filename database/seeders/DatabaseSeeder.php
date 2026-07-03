<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CertificateTemplate;
use App\Models\Community;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSection;
use App\Models\Bundle;
use App\Models\CheckoutConsent;
use App\Models\Group;
use App\Models\Permission;
use App\Services\PermissionService;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRolesAndPermissions();
        $this->seedUsers();
        $this->seedCategories();
        $this->seedCourses();
        $this->seedCoupons();
        $this->seedCommunities();
        $this->seedSettings();
        $this->seedCertificateTemplate();
        $this->seedBundlesAndGroups();
        $this->seedCheckoutConsents();
    }

    protected function seedBundlesAndGroups(): void
    {
        $admin = User::where('email', 'admin@learnyst.com')->first();
        $courses = Course::take(2)->pluck('id');

        if ($courses->count() >= 2) {
            $bundle = Bundle::firstOrCreate(['title' => 'Full Stack Developer Pack'], [
                'description' => 'Bundle of essential development courses.',
                'price' => 7999,
                'status' => 'published',
                'created_by' => $admin?->id,
            ]);
            $bundle->courses()->syncWithoutDetaching($courses->mapWithKeys(fn ($id, $i) => [$id => ['sort_order' => $i + 1]])->all());
        }

        Group::firstOrCreate(['name' => 'Premium Learners'], [
            'description' => 'High-value learner group for targeted communication.',
            'is_active' => true,
            'created_by' => $admin?->id,
        ]);
    }

    protected function seedCheckoutConsents(): void
    {
        CheckoutConsent::firstOrCreate(['title' => 'Terms & Conditions'], [
            'description' => 'Standard purchase terms',
            'body' => 'I agree to the terms and conditions and refund policy of Learnyst platform.',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    protected function seedRolesAndPermissions(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full platform access', 'is_system' => true],
            ['name' => 'Sub Administrator', 'slug' => 'sub-admin', 'description' => 'Limited admin with assigned permissions', 'is_system' => true],
            ['name' => 'Instructor', 'slug' => 'instructor', 'description' => 'Manage assigned courses and batches', 'is_system' => true],
            ['name' => 'Learner', 'slug' => 'learner', 'description' => 'Access enrolled courses', 'is_system' => true],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }

        $modules = PermissionService::modules();
        $actions = PermissionService::actions();
        $adminRole = Role::where('slug', 'admin')->first();
        $subAdminRole = Role::where('slug', 'sub-admin')->first();
        $permissionIds = [];

        foreach ($modules as $module => $label) {
            foreach ($actions as $action) {
                $slug = "{$module}.{$action}";
                $permission = Permission::firstOrCreate(['slug' => $slug], [
                    'name' => ucfirst($action) . ' ' . $label,
                    'group' => $module,
                    'module' => $module,
                    'action' => $action,
                ]);
                $permissionIds[] = $permission->id;
            }
        }

        $adminRole->permissions()->sync($permissionIds);

        $subAdminPermissions = Permission::whereIn('module', ['dashboard', 'learners', 'products', 'sales'])
            ->where('action', 'view')
            ->pluck('id');
        $subAdminRole->permissions()->sync($subAdminPermissions);
    }

    protected function seedUsers(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $instructorRole = Role::where('slug', 'instructor')->first();
        $learnerRole = Role::where('slug', 'learner')->first();

        User::firstOrCreate(['email' => 'admin@learnyst.com'], [
            'role_id' => $adminRole->id,
            'name' => 'Platform Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'instructor@learnyst.com'], [
            'role_id' => $instructorRole->id,
            'name' => 'Sarah Mitchell',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'bio' => 'Senior instructor with 10+ years experience.',
        ]);

        User::firstOrCreate(['email' => 'learner@learnyst.com'], [
            'role_id' => $learnerRole->id,
            'name' => 'Alex Johnson',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(['email' => "learner{$i}@learnyst.com"], [
                'role_id' => $learnerRole->id,
                'name' => "Learner User {$i}",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);
        }

        $subAdminRole = Role::where('slug', 'sub-admin')->first();
        User::firstOrCreate(['email' => 'subadmin@learnyst.com'], [
            'role_id' => $subAdminRole?->id,
            'name' => 'Sub Admin User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'phone' => '+919876543210',
        ]);
    }

    protected function seedCategories(): void
    {
        $categories = [
            ['name' => 'Web Development', 'icon' => 'code'],
            ['name' => 'Business', 'icon' => 'briefcase'],
            ['name' => 'Design', 'icon' => 'palette'],
            ['name' => 'Marketing', 'icon' => 'megaphone'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], array_merge($cat, ['is_active' => true]));
        }
    }

    protected function seedCourses(): void
    {
        $admin = User::where('email', 'admin@learnyst.com')->first();
        $category = Category::first();

        $course = Course::firstOrCreate(['title' => 'Complete Laravel Mastery'], [
            'category_id' => $category?->id,
            'created_by' => $admin?->id,
            'description' => 'Master Laravel from basics to advanced concepts including APIs, queues, and testing.',
            'price' => 4999,
            'is_free' => false,
            'access_type' => 'paid',
            'status' => 'published',
            'product_type' => 'course',
            'start_date' => now(),
        ]);

        if ($course->sections()->count() === 0) {
            $section = CourseSection::create([
                'course_id' => $course->id,
                'title' => 'Getting Started',
                'sort_order' => 1,
            ]);

            CourseLesson::create([
                'course_section_id' => $section->id,
                'title' => 'Introduction to Laravel',
                'lesson_type' => 'video',
                'content' => 'Welcome to the course!',
                'duration_minutes' => 15,
                'is_preview' => true,
                'sort_order' => 1,
            ]);

            CourseLesson::create([
                'course_section_id' => $section->id,
                'title' => 'Setting Up Your Environment',
                'lesson_type' => 'text',
                'content' => 'Install PHP, Composer, and Laravel.',
                'sort_order' => 2,
            ]);
        }

        Course::firstOrCreate(['title' => 'Free UI/UX Fundamentals'], [
            'category_id' => Category::skip(2)->first()?->id,
            'created_by' => $admin?->id,
            'description' => 'Learn design principles for modern web applications.',
            'price' => 0,
            'is_free' => true,
            'access_type' => 'free',
            'status' => 'published',
            'product_type' => 'course',
        ]);
    }

    protected function seedCoupons(): void
    {
        Coupon::firstOrCreate(['code' => 'WELCOME20'], [
            'title' => 'Welcome Discount',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'is_active' => true,
        ]);
    }

    protected function seedCommunities(): void
    {
        $admin = User::where('email', 'admin@learnyst.com')->first();

        Community::firstOrCreate(['name' => 'Developers Hub'], [
            'description' => 'A community for developers to share knowledge and collaborate.',
            'created_by' => $admin?->id,
            'is_active' => true,
        ]);
    }

    protected function seedSettings(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Learnyst', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Premium Learning Platform', 'type' => 'text'],
            ['group' => 'general', 'key' => 'theme_color', 'value' => '#10b981', 'type' => 'color'],
            ['group' => 'payment', 'key' => 'razorpay_key', 'value' => '', 'type' => 'text'],
            ['group' => 'payment', 'key' => 'razorpay_secret', 'value' => '', 'type' => 'password'],
            ['group' => 'payment', 'key' => 'currency', 'value' => 'INR', 'type' => 'text'],
            ['group' => 'tax', 'key' => 'gst_rate', 'value' => '18', 'type' => 'number'],
            ['group' => 'email', 'key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'type' => 'text'],
            ['group' => 'social', 'key' => 'facebook', 'value' => 'https://facebook.com/learnyst', 'type' => 'url'],
            ['group' => 'social', 'key' => 'youtube', 'value' => 'https://youtube.com/@learnyst', 'type' => 'url'],
            ['group' => 'social', 'key' => 'linkedin', 'value' => 'https://linkedin.com/company/learnyst', 'type' => 'url'],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://instagram.com/learnyst', 'type' => 'url'],
            ['group' => 'social', 'key' => 'website', 'value' => 'https://learnyst.com', 'type' => 'url'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }

    protected function seedCertificateTemplate(): void
    {
        CertificateTemplate::firstOrCreate(['name' => 'Default Certificate'], [
            'html_content' => '<div style="text-align:center;padding:40px;"><h1>Certificate of Completion</h1><p>This certifies that</p><h2>{{student_name}}</h2><p>has completed</p><h3>{{course_name}}</h3><p>Certificate No: {{certificate_number}}</p><p>Date: {{issue_date}}</p></div>',
            'is_default' => true,
        ]);
    }
}
