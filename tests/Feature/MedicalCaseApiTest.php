<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MedicalCaseApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $viewer;
    private string $adminToken;
    private string $viewerToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@testapi.com',
            'password' => Hash::make('AdminSecret123!'),
        ]);

        $this->viewer = User::factory()->viewer()->create([
            'email' => 'viewer@testapi.com',
            'password' => Hash::make('ViewerSecret123!'),
        ]);

        $this->adminToken = $this->admin->createToken('admin_test_token')->plainTextToken;
        $this->viewerToken = $this->viewer->createToken('viewer_test_token')->plainTextToken;
    }

    /**
     * Requirement 1: Valid login succeeds and returns an API token.
     */
    public function test_valid_login_succeeds_and_issues_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@testapi.com',
            'password' => 'AdminSecret123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Authenticated successfully.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * Requirement 2: Invalid login is rejected with HTTP 401.
     */
    public function test_invalid_login_is_rejected(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@testapi.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
                'data' => null,
            ]);
    }

    /**
     * Requirement 3: A guest cannot access protected case APIs and receives HTTP 401.
     */
    public function test_guest_cannot_access_protected_case_apis(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        $this->getJson('/api/cases')->assertStatus(401);
        $this->postJson('/api/cases', [])->assertStatus(401);
        $this->getJson("/api/cases/{$case->id}")->assertStatus(401);
        $this->putJson("/api/cases/{$case->id}", [])->assertStatus(401);
        $this->deleteJson("/api/cases/{$case->id}")->assertStatus(401);
    }

    /**
     * Requirement 4: An Admin can create a case through the API.
     */
    public function test_admin_can_create_a_case_through_api(): void
    {
        $payload = [
            'case_number' => 'CAS-API-001',
            'patient_reference' => 'PT-API-101',
            'surgeon_name' => 'Dr. Meredith Grey',
            'implant_type' => 'Custom 3D Mesh',
            'status' => 'Planning',
            'priority' => 'High',
            'surgery_date' => '2026-11-30',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/cases', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Case created successfully.',
                'data' => [
                    'case_number' => 'CAS-API-001',
                    'patient_reference' => 'PT-API-101',
                    'surgeon_name' => 'Dr. Meredith Grey',
                    'created_by' => $this->admin->id,
                ],
            ]);

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CAS-API-001',
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Requirement 5: Creating a case with a duplicate Case Number fails with HTTP 422 through the API.
     */
    public function test_creating_case_with_duplicate_case_number_fails_with_422_through_api(): void
    {
        MedicalCase::factory()->create([
            'case_number' => 'CAS-DUPLICATE-999',
            'created_by' => $this->admin->id,
        ]);

        $duplicatePayload = [
            'case_number' => 'CAS-DUPLICATE-999',
            'patient_reference' => 'PT-DUPL-02',
            'surgeon_name' => 'Dr. Derek Shepherd',
            'implant_type' => 'Spinal Implant',
            'status' => 'Planning',
            'priority' => 'Medium',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/cases', $duplicatePayload);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['case_number']);
    }

    /**
     * Requirement 6: A Viewer cannot delete a case and receives HTTP 403.
     */
    public function test_viewer_cannot_delete_a_case_and_receives_403(): void
    {
        $case = MedicalCase::factory()->create([
            'case_number' => 'CAS-NODELETE-55',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->viewerToken)
            ->deleteJson("/api/cases/{$case->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Requirement: Viewer cannot create or update cases through API (receives 403).
     */
    public function test_viewer_cannot_create_or_update_cases_through_api(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        // Attempt create
        $createResponse = $this->withHeader('Authorization', 'Bearer ' . $this->viewerToken)
            ->postJson('/api/cases', [
                'case_number' => 'CAS-VIEWER-DENIED',
                'patient_reference' => 'PT-001',
                'surgeon_name' => 'Dr. Denied',
                'implant_type' => 'Implant',
                'status' => 'Draft',
                'priority' => 'Low',
            ]);
        $createResponse->assertStatus(403);

        // Attempt update
        $updateResponse = $this->withHeader('Authorization', 'Bearer ' . $this->viewerToken)
            ->putJson("/api/cases/{$case->id}", [
                'case_number' => $case->case_number,
                'patient_reference' => 'PT-MOD',
                'surgeon_name' => 'Dr. Denied',
                'implant_type' => 'Implant',
                'status' => 'Completed',
                'priority' => 'High',
            ]);
        $updateResponse->assertStatus(403);
    }

    /**
     * Requirement: Admin can update a case through API.
     */
    public function test_admin_can_update_a_case_through_api(): void
    {
        $case = MedicalCase::factory()->create([
            'case_number' => 'CAS-ORIG-100',
            'status' => 'Planning',
            'created_by' => $this->admin->id,
        ]);

        $updatePayload = [
            'case_number' => 'CAS-ORIG-100',
            'patient_reference' => 'PT-UPDATED-REF',
            'surgeon_name' => 'Dr. Cristina Yang',
            'implant_type' => 'Aortic Stent v3',
            'status' => 'Approved',
            'priority' => 'High',
            'surgery_date' => '2026-12-10',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->putJson("/api/cases/{$case->id}", $updatePayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $case->id,
                    'status' => 'Approved',
                    'surgeon_name' => 'Dr. Cristina Yang',
                ],
            ]);

        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'status' => 'Approved',
            'patient_reference' => 'PT-UPDATED-REF',
        ]);
    }

    /**
     * Requirement: Admin can delete a case through API.
     */
    public function test_admin_can_delete_a_case_through_api(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->deleteJson("/api/cases/{$case->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => null,
            ]);

        $this->assertDatabaseMissing('cases', ['id' => $case->id]);
    }

    /**
     * Requirement: GET /api/cases returns paginated list and filters by search and status.
     */
    public function test_api_get_cases_returns_paginated_list_with_search_and_filters(): void
    {
        MedicalCase::factory()->create([
            'case_number' => 'CAS-SPECIFIC-1',
            'surgeon_name' => 'Dr. UniqueSurgeon',
            'status' => 'Planning',
            'created_by' => $this->admin->id,
        ]);

        MedicalCase::factory()->create([
            'case_number' => 'CAS-OTHER-2',
            'surgeon_name' => 'Dr. OtherSurgeon',
            'status' => 'Completed',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->viewerToken)
            ->getJson('/api/cases?search=UniqueSurgeon&status=Planning');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'items',
                    'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
                ],
            ]);

        $items = $response->json('data.items');
        $this->assertCount(1, $items);
        $this->assertEquals('CAS-SPECIFIC-1', $items[0]['case_number']);
    }

    /**
     * Requirement: GET /api/cases/{id} returns 200 for existing case, 404 for missing case.
     */
    public function test_api_get_single_case_returns_200_and_missing_case_returns_404(): void
    {
        $case = MedicalCase::factory()->create(['created_by' => $this->admin->id]);

        // Found
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->viewerToken)
            ->getJson("/api/cases/{$case->id}");
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['id' => $case->id],
            ]);

        // Missing ID (e.g. 999999)
        $missingResponse = $this->withHeader('Authorization', 'Bearer ' . $this->viewerToken)
            ->getJson('/api/cases/999999');
        $missingResponse->assertStatus(404)
            ->assertJson([
                'success' => false,
                'data' => null,
            ]);
    }

    /**
     * Requirement: Invalid status and priority values are rejected with HTTP 422.
     */
    public function test_invalid_status_and_priority_rejected_with_422(): void
    {
        $payload = [
            'case_number' => 'CAS-INVALID-01',
            'patient_reference' => 'PT-01',
            'surgeon_name' => 'Dr. Test',
            'implant_type' => 'Implant',
            'status' => 'InvalidStatusValue',
            'priority' => 'ExtremeSuperHigh',
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/cases', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status', 'priority']);
    }

    /**
     * Requirement: API logout revokes the token.
     */
    public function test_api_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('logout_test_token')->plainTextToken;

        // Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully. Token revoked.',
            ]);

        // Verify token was deleted from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'name' => 'logout_test_token',
        ]);

        // Reset auth guard state to verify token cannot be reused
        auth()->forgetGuards();

        $subsequentResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/cases');

        $subsequentResponse->assertStatus(401);
    }
}
