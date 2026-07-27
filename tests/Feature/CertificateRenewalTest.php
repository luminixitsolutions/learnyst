<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use App\Services\CertificateLifecycleService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CertificateRenewalTest extends TestCase
{
    use RefreshDatabase;

    protected function seedRoles(): void
    {
        foreach (['admin', 'learner'] as $slug) {
            Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => ucfirst($slug), 'is_system' => true]
            );
        }
    }

    protected function makeLearner(): User
    {
        $role = Role::where('slug', 'learner')->first();

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function makeAdmin(): User
    {
        $role = Role::where('slug', 'admin')->first();

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_certificate_issue_sets_expiry_from_template(): void
    {
        $this->seedRoles();
        $learner = $this->makeLearner();
        $course = Course::create([
            'title' => 'Safety Course',
            'slug' => 'safety-course',
            'status' => 'published',
            'product_type' => 'course',
            'access_type' => 'free',
            'is_free' => true,
        ]);

        $template = CertificateTemplate::create([
            'name' => 'Safety Cert',
            'html_content' => '<p>{{student_name}}</p>',
            'validity_years' => 1,
            'course_id' => $course->id,
        ]);

        $issuedAt = now();
        $cert = Certificate::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'certificate_template_id' => $template->id,
            'issued_at' => $issuedAt,
        ]);

        app(CertificateLifecycleService::class)->applyLifecycle($cert, $template);

        $cert->refresh();
        $this->assertNotNull($cert->expires_at);
        $this->assertSame('valid', $cert->status);
        $this->assertTrue($cert->expires_at->greaterThan(now()->addMonths(11)));
    }

    public function test_expired_certificate_moves_to_renewal_due(): void
    {
        $this->seedRoles();
        $learner = $this->makeLearner();
        $template = CertificateTemplate::create([
            'name' => 'Annual Cert',
            'html_content' => '<p>Test</p>',
            'validity_days' => 30,
        ]);

        $cert = Certificate::create([
            'user_id' => $learner->id,
            'certificate_template_id' => $template->id,
            'issued_at' => now()->subDays(60),
            'expires_at' => now()->subDay(),
            'status' => 'valid',
        ]);

        $lifecycle = app(CertificateLifecycleService::class);
        $cert->update(['status' => $lifecycle->resolveStatus($cert)]);

        $this->assertSame('renewal_due', $cert->fresh()->status);
    }

    public function test_free_renewal_issues_new_certificate(): void
    {
        $this->seedRoles();
        $learner = $this->makeLearner();
        $template = CertificateTemplate::create([
            'name' => 'Renewable',
            'html_content' => '<p>Test</p>',
            'validity_days' => 1,
            'renewal_price' => 0,
        ]);

        $cert = Certificate::create([
            'user_id' => $learner->id,
            'certificate_template_id' => $template->id,
            'issued_at' => now()->subDays(5),
            'expires_at' => now()->subDay(),
            'status' => 'renewal_due',
        ]);

        $renewed = app(CertificateLifecycleService::class)->renew($cert);

        $this->assertNotEquals($cert->id, $renewed->id);
        $this->assertSame($cert->id, $renewed->renewed_from_id);
        $this->assertSame(1, $renewed->renewal_count);
        $this->assertSame('valid', $renewed->status);
        $this->assertSame('expired', $cert->fresh()->status);
    }

    public function test_public_verify_shows_renewal_due_status(): void
    {
        $this->seedRoles();
        $learner = $this->makeLearner();
        $cert = Certificate::create([
            'user_id' => $learner->id,
            'certificate_number' => 'CERT-TESTVERIFY1',
            'issued_at' => now()->subYear(),
            'expires_at' => now()->subWeek(),
            'status' => 'renewal_due',
        ]);

        $response = $this->get(route('certificates.verify', ['number' => $cert->certificate_number]));

        $response->assertOk();
        $response->assertSee('Renewal Due');
        $response->assertSee($cert->certificate_number);
    }

    public function test_scheduled_command_updates_statuses(): void
    {
        $this->seedRoles();
        $learner = $this->makeLearner();
        Certificate::create([
            'user_id' => $learner->id,
            'issued_at' => now()->subYear(),
            'expires_at' => now()->addDays(45),
            'status' => 'valid',
        ]);

        Artisan::call('certificates:update-statuses');

        $this->assertDatabaseHas('certificates', ['status' => 'expiring_soon']);
    }

    public function test_admin_renewal_dashboard_requires_auth(): void
    {
        $this->seedRoles();

        $this->get(route('admin.certificates.renewals.index'))
            ->assertRedirect();
    }

    public function test_admin_can_view_renewal_dashboard(): void
    {
        $this->seedRoles();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get(route('admin.certificates.renewals.index'))
            ->assertOk()
            ->assertSee('Certificate Renewal Dashboard');
    }
}
