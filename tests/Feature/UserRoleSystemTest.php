<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Services\CurrentUserService;

class UserRoleSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $doctorUser;
    protected $staffUser;
    protected $patient;
    protected $medicalRecord;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create users with different roles
        $this->doctorUser = User::factory()->create([
            'role' => User::ROLE_DOCTOR,
            'name' => 'Dr. Smith',
            'email' => 'doctor@example.com'
        ]);
        
        $this->staffUser = User::factory()->create([
            'role' => User::ROLE_STAFF,
            'name' => 'Staff User',
            'email' => 'staff@example.com'
        ]);
        
        $this->patient = Patient::factory()->create();
        
        $this->medicalRecord = MedicalRecord::factory()->create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms',
            'diagnosis' => 'Test diagnosis',
            'treatment' => 'Test treatment',
        ]);
    }

    /** @test */
    public function current_user_api_returns_user_and_available_users()
    {
        // Set current user
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $response = $this->get('/current-user');
        
        $response->assertOk();
        $response->assertJson([
            'current_user' => [
                'id' => $this->staffUser->id,
                'name' => 'Staff User',
                'role' => 'staff',
                'email' => 'staff@example.com'
            ]
        ]);
        
        $response->assertJsonStructure([
            'current_user' => ['id', 'name', 'role', 'email'],
            'available_users' => [
                '*' => ['id', 'name', 'role', 'email']
            ]
        ]);
    }

    /** @test */
    public function user_switching_works_correctly()
    {
        // Start with staff user
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        // Switch to doctor
        $response = $this->post('/switch-user', [
            'user_id' => $this->doctorUser->id
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verify current user changed
        $currentUser = app(CurrentUserService::class)->getCurrentUser();
        $this->assertEquals($this->doctorUser->id, $currentUser->id);
        $this->assertEquals('doctor', $currentUser->role);
    }

    /** @test */
    public function user_switching_validates_user_exists()
    {
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $response = $this->post('/switch-user', [
            'user_id' => 99999 // Non-existent user
        ]);
        
        $response->assertSessionHasErrors(['user_id']);
    }

    /** @test */
    public function doctor_can_start_consultation()
    {
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $response = $this->post("/medical-records/{$this->medicalRecord->id}/start-consultation");
        
        $response->assertRedirect("/medical-records/{$this->medicalRecord->id}/consultation");
        $response->assertSessionHas('success', 'Consultation started successfully');
        
        $this->medicalRecord->refresh();
        $this->assertEquals(MedicalRecord::STATUS_ATTENDING, $this->medicalRecord->status);
    }

    /** @test */
    public function staff_cannot_start_consultation()
    {
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $response = $this->post("/medical-records/{$this->medicalRecord->id}/start-consultation");
        
        $response->assertRedirect('/medical-records');
        $response->assertSessionHas('error', 'Only doctors can start consultations.');
        
        $this->medicalRecord->refresh();
        $this->assertEquals(MedicalRecord::STATUS_PENDING, $this->medicalRecord->status);
    }

    /** @test */
    public function doctor_can_update_diagnosis_and_treatment()
    {
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $data = [
            'patient_id' => $this->patient->id,
            'symptoms' => 'Updated symptoms',
            'diagnosis' => 'Updated diagnosis by doctor',
            'treatment' => 'Updated treatment by doctor',
            'notes' => 'Updated notes',
        ];
        
        $response = $this->put("/medical-records/{$this->medicalRecord->id}", $data);
        
        $response->assertRedirect("/medical-records/{$this->medicalRecord->id}");
        $response->assertSessionHas('success', 'Medical record updated successfully');
        
        $this->assertDatabaseHas('medical_records', [
            'id' => $this->medicalRecord->id,
            'diagnosis' => 'Updated diagnosis by doctor',
            'treatment' => 'Updated treatment by doctor',
        ]);
    }

    /** @test */
    public function staff_cannot_update_diagnosis_and_treatment()
    {
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $data = [
            'patient_id' => $this->patient->id,
            'symptoms' => 'Updated symptoms',
            'diagnosis' => 'Unauthorized diagnosis update',
            'treatment' => 'Unauthorized treatment update',
            'notes' => 'Updated notes',
        ];
        
        $response = $this->put("/medical-records/{$this->medicalRecord->id}", $data);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
        
        // Verify diagnosis and treatment were NOT updated
        $this->assertDatabaseHas('medical_records', [
            'id' => $this->medicalRecord->id,
            'diagnosis' => 'Test diagnosis', // Original value
            'treatment' => 'Test treatment', // Original value
        ]);
    }

    /** @test */
    public function staff_can_update_other_fields()
    {
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $data = [
            'patient_id' => $this->patient->id,
            'symptoms' => 'Updated symptoms by staff',
            'notes' => 'Updated notes by staff',
        ];
        
        $response = $this->put("/medical-records/{$this->medicalRecord->id}", $data);
        
        $response->assertRedirect("/medical-records/{$this->medicalRecord->id}");
        $response->assertSessionHas('success', 'Medical record updated successfully');
        
        $this->assertDatabaseHas('medical_records', [
            'id' => $this->medicalRecord->id,
            'symptoms' => 'Updated symptoms by staff',
            'notes' => 'Updated notes by staff',
            // These should remain unchanged
            'diagnosis' => 'Test diagnosis',
            'treatment' => 'Test treatment',
        ]);
    }

    /** @test */
    public function medical_records_index_shows_correct_permissions()
    {
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $response = $this->get('/medical-records');
        
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MedicalRecords/Index')
            ->has('permissions')
            ->where('permissions.canStartConsultation', false) // Staff cannot start consultations
        );
    }

    /** @test */
    public function medical_records_index_shows_doctor_permissions()
    {
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $response = $this->get('/medical-records');
        
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MedicalRecords/Index')
            ->has('permissions')
            ->where('permissions.canStartConsultation', true) // Doctor can start consultations
        );
    }

    /** @test */
    public function medical_record_show_page_includes_permissions()
    {
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $response = $this->get("/medical-records/{$this->medicalRecord->id}");
        
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MedicalRecords/Show')
            ->has('permissions')
            ->where('permissions.canStartConsultation', true)
            ->where('permissions.canFinishConsultation', true)
            ->where('permissions.canUpdateDiagnosisAndTreatment', true)
        );
    }

    /** @test */
    public function medical_record_edit_page_includes_permissions()
    {
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $response = $this->get("/medical-records/{$this->medicalRecord->id}/edit");
        
        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MedicalRecords/Edit')
            ->has('permissions')
            ->where('permissions.canUpdateDiagnosisAndTreatment', false) // Staff cannot update diagnosis/treatment
        );
    }

    /** @test */
    public function user_model_helper_methods_work_correctly()
    {
        $this->assertTrue($this->doctorUser->isDoctor());
        $this->assertFalse($this->doctorUser->isStaff());
        
        $this->assertTrue($this->staffUser->isStaff());
        $this->assertFalse($this->staffUser->isDoctor());
    }

    /** @test */
    public function user_model_returns_available_roles()
    {
        $roles = User::getRoles();
        
        $this->assertArrayHasKey(User::ROLE_STAFF, $roles);
        $this->assertArrayHasKey(User::ROLE_DOCTOR, $roles);
        $this->assertEquals('Staff', $roles[User::ROLE_STAFF]);
        $this->assertEquals('Doctor', $roles[User::ROLE_DOCTOR]);
    }

    /** @test */
    public function current_user_service_defaults_to_staff_user()
    {
        // Don't set any user, should default to staff
        $currentUser = app(CurrentUserService::class)->getCurrentUser();
        
        $this->assertNotNull($currentUser);
        $this->assertEquals(User::ROLE_STAFF, $currentUser->role);
    }

    /** @test */
    public function current_user_service_can_switch_roles()
    {
        $service = app(CurrentUserService::class);
        
        // Switch to doctor role
        $doctorUser = $service->switchToRole(User::ROLE_DOCTOR);
        $this->assertNotNull($doctorUser);
        $this->assertEquals(User::ROLE_DOCTOR, $doctorUser->role);
        
        // Verify current user changed
        $currentUser = $service->getCurrentUser();
        $this->assertEquals($doctorUser->id, $currentUser->id);
    }

    /** @test */
    public function current_user_service_returns_available_users()
    {
        $service = app(CurrentUserService::class);
        $availableUsers = $service->getAvailableUsers();
        
        $this->assertIsArray($availableUsers);
        $this->assertCount(2, $availableUsers); // Should have both doctor and staff
        
        // Check structure
        foreach ($availableUsers as $user) {
            $this->assertArrayHasKey('id', $user);
            $this->assertArrayHasKey('name', $user);
            $this->assertArrayHasKey('role', $user);
            $this->assertArrayHasKey('email', $user);
        }
    }
} 