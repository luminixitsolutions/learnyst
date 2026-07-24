<?php

namespace Tests\Feature\Admin;

use App\Models\Course;
use App\Models\CourseSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseSettingsPublishTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(): User
    {
        $role = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'is_system' => true]
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_publish_is_blocked_when_requirements_missing(): void
    {
        $admin = $this->makeAdmin();
        $course = Course::create([
            'title' => 'Incomplete Course',
            'slug' => 'incomplete-course',
            'status' => 'draft',
            'created_by' => $admin->id,
            'product_type' => 'course',
            'access_type' => 'paid',
            'is_free' => false,
        ]);
        CourseSetting::create(['course_id' => $course->id]);

        $response = $this->actingAs($admin)->post(route('admin.courses.settings.publish', $course), [
            'status' => 'published',
            'confirm' => '1',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertSame('draft', $course->fresh()->status);
    }

    public function test_settings_hub_is_reachable_for_owner(): void
    {
        $admin = $this->makeAdmin();
        $course = Course::create([
            'title' => 'AI Course',
            'slug' => 'ai-course-test',
            'status' => 'draft',
            'created_by' => $admin->id,
            'product_type' => 'course',
            'access_type' => 'paid',
            'description' => 'Desc',
            'is_free' => false,
        ]);
        CourseSetting::create(['course_id' => $course->id]);

        $this->actingAs($admin)
            ->get(route('admin.courses.settings.hub', $course))
            ->assertOk()
            ->assertSee('Course Settings')
            ->assertSee('Branding')
            ->assertSee('Pricing Plans');
    }
}
