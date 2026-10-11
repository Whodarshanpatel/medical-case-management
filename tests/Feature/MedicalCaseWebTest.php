<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalCaseWebTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@test.com',
        ]);

        $this->viewer = User::factory()->viewer()->create([
            'email' => 'viewer@test.com',
        ]);
    }

    /**
     * Requirement: Guest cannot access protected case pages.
     */
    public function test_guest_cannot_access_protected_case_pages(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/cases')->assertRedirect(route('login'));
        $this->get('/cases/create')->assertRedirect(route('login'));
        $this->get("/cases/{$case->id}")->assertRedirect(route('login'));
        $this->get("/cases/{$case->id}/edit")->assertRedirect(route('login'));
    }

    /**
     * Requirement: Both Admin and Viewer can view cases.
     */
    public function test_both_admin_and_viewer_can_view_cases_index_and_details(): void
    {
        $case = MedicalCase::factory()->create([
            'case_number' => 'CAS-88110',
            'surgeon_name' => 'Dr. Walter Bishop',
            'created_by' => $this->admin->id,
        ]);

        // Admin view
        $this->actingAs($this->admin)
            ->get('/cases')
            ->assertOk()
            ->assertSee('CAS-88110')
            ->assertSee('Dr. Walter Bishop');

        $this->actingAs($this->admin)
            ->get("/cases/{$case->id}")
            ->assertOk()
            ->assertSee('CAS-88110');

        // Viewer view
        $this->actingAs($this->viewer)
            ->get('/cases')
            ->assertOk()
            ->assertSee('CAS-88110');

        $this->actingAs($this->viewer)
            ->get("/cases/{$case->id}")
            ->assertOk()
            ->assertSee('CAS-88110');
    }

    /**
     * Requirement: Admin can create a case.
     */
    public function test_admin_can_create_a_case(): void
    {
        $payload = [
            'case_number' => 'CAS-99001',
            'patient_reference' => 'PT-5521',
            'surgeon_name' => 'Dr. Gregory House',
            'implant_type' => 'Titanium Hip Stem',
            'status' => 'Planning',
            'priority' => 'High',
            'surgery_date' => '2026-11-15',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/cases', $payload);

        $response->assertRedirect(route('cases.index'));
        $this->assertDatabaseHas('cases', [
            'case_number' => 'CAS-99001',
            'patient_reference' => 'PT-5521',
            'surgeon_name' => 'Dr. Gregory House',
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Requirement: Viewer cannot create a case and receives HTTP 403.
     */
    public function test_viewer_cannot_create_a_case_and_receives_403(): void
    {
        $payload = [
            'case_number' => 'CAS-99002',
            'patient_reference' => 'PT-5522',
            'surgeon_name' => 'Dr. Allison Cameron',
            'implant_type' => 'Spinal Cage',
            'status' => 'Draft',
            'priority' => 'Low',
        ];

        $response = $this->actingAs($this->viewer)
            ->post('/cases', $payload);

        $response->assertForbidden();
        $this->assertDatabaseMissing('cases', ['case_number' => 'CAS-99002']);
    }

    /**
     * Requirement: Admin can update a case.
     */
    public function test_admin_can_update_a_case(): void
    {
        $case = MedicalCase::factory()->create([
            'case_number' => 'CAS-77001',
            'status' => 'Planning',
            'created_by' => $this->admin->id,
        ]);

        $updatePayload = [
            'case_number' => 'CAS-77001', // Keeps same case_number
            'patient_reference' => 'PT-7701-UPD',
            'surgeon_name' => 'Dr. Robert Chase',
            'implant_type' => 'Ceramic Joint v2',
            'status' => 'Approved',
            'priority' => 'High',
            'surgery_date' => '2026-12-01',
        ];

        $response = $this->actingAs($this->admin)
            ->put("/cases/{$case->id}", $updatePayload);

        $response->assertRedirect(route('cases.show', $case));
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'status' => 'Approved',
            'implant_type' => 'Ceramic Joint v2',
        ]);
    }

    /**
     * Requirement: Viewer cannot update a case and receives HTTP 403.
     */
    public function test_viewer_cannot_update_a_case_and_receives_403(): void
    {
        $case = MedicalCase::factory()->create([
            'status' => 'Planning',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->viewer)
            ->put("/cases/{$case->id}", [
                'case_number' => $case->case_number,
                'patient_reference' => 'PT-HACK',
                'surgeon_name' => 'Dr. Unauthorized',
                'implant_type' => 'Implant',
                'status' => 'Completed',
                'priority' => 'High',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('cases', ['patient_reference' => 'PT-HACK']);
    }

    /**
     * Requirement: Admin can delete a case.
     */
    public function test_admin_can_delete_a_case(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)
            ->delete("/cases/{$case->id}");

        $response->assertRedirect(route('cases.index'));
        $this->assertDatabaseMissing('cases', ['id' => $case->id]);
    }

    /**
     * Requirement: Viewer cannot delete a case and receives HTTP 403.
     */
    public function test_viewer_cannot_delete_a_case_and_receives_403(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->viewer)
            ->delete("/cases/{$case->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Requirement: Dashboard displays calculated counts from database.
     */
    public function test_dashboard_displays_real_database_counts(): void
    {
        MedicalCase::factory(3)->create([
            'status' => 'Planning',
            'created_by' => $this->admin->id,
        ]);
        MedicalCase::factory(2)->create([
            'status' => 'In Review',
            'created_by' => $this->admin->id,
        ]);
        MedicalCase::factory(4)->create([
            'status' => 'Approved',
            'created_by' => $this->admin->id,
        ]);
        MedicalCase::factory(1)->create([
            'status' => 'Completed',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertOk();
        $response->assertViewHas('totalCases', 10);
        $response->assertViewHas('planningCases', 3);
        $response->assertViewHas('inReviewCases', 2);
        $response->assertViewHas('approvedCases', 4);
        $response->assertViewHas('completedCases', 1);
    }
}
