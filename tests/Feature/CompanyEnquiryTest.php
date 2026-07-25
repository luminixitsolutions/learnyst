<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\CompanyEnquiry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_enquiry_is_saved_and_visible_in_institute_panel(): void
    {
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'is_system' => true]
        );

        $owner = User::factory()->create([
            'role_id' => $adminRole->id,
            'email' => 'luminix-owner@example.com',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $company = Company::create([
            'owner_user_id' => $owner->id,
            'name' => 'Luminix IT Solution',
            'slug' => 'luminix-it-solution',
            'email' => 'hello@luminix.academy',
            'is_public' => true,
        ]);

        $response = $this->from(route('website.companies.show', $company->slug))
            ->post(route('website.companies.enquiries.store', $company->slug), [
                'name' => 'Rajat Student',
                'email' => 'rajat@example.com',
                'phone' => '9876543210',
                'subject' => 'Course enquiry',
                'message' => 'I want to join the fullstack course.',
            ]);

        $response->assertRedirect(route('website.companies.show', $company->slug).'#contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('company_enquiries', [
            'company_id' => $company->id,
            'name' => 'Rajat Student',
            'email' => 'rajat@example.com',
            'subject' => 'Course enquiry',
            'status' => 'new',
        ]);

        $this->actingAs($owner)
            ->get(route('admin.company-page.enquiries'))
            ->assertOk()
            ->assertSee('Rajat Student')
            ->assertSee('rajat@example.com')
            ->assertSee('I want to join the fullstack course.');
    }
}
