<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Anamnesis;
use App\Models\User;
use App\Services\CurrentUserService;

class ConsultationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $doctorUser;
    protected $staffUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->patient = Patient::factory()->create();
        $this->anamnesis = Anamnesis::factory()->create();
        
        // Create users with different roles
        $this->doctorUser = User::factory()->create([
            'role' => User::ROLE_DOCTOR,
            'name' => 'Dr. Test',
            'email' => 'doctor@test.com'
        ]);
        
        $this->staffUser = User::factory()->create([
            'role' => User::ROLE_STAFF,
            'name' => 'Staff Test',
            'email' => 'staff@test.com'
        ]);
    }

    /** @test */
    public function it_can_start_consultation_for_pending_record()
    {
        // Set current user to doctor (only doctors can start consultations)
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'anamnesis_id' => $this->anamnesis->id,
            'symptoms' => 'Test symptoms'
        ]);

        $this->assertEquals(MedicalRecord::STATUS_PENDING, $medicalRecord->status);

        $response = $this->post("/medical-records/{$medicalRecord->id}/start-consultation");

        $response->assertRedirect("/medical-records/{$medicalRecord->id}/consultation");
        $response->assertSessionHas('success', 'Consultation started successfully');

        $medicalRecord->refresh();
        $this->assertEquals(MedicalRecord::STATUS_ATTENDING, $medicalRecord->status);
    }

    /** @test */
    public function it_shows_consultation_page_with_patient_and_record_data()
    {
        // Set current user to doctor
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'anamnesis_id' => $this->anamnesis->id,
            'symptoms' => 'Test symptoms for consultation'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        $response = $this->get("/medical-records/{$medicalRecord->id}/consultation");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MedicalRecords/Consultation')
            ->has('record')
            ->has('patient')
            ->has('anamnesis')
            ->has('statusHistory')
            ->has('availableStatuses')
            ->has('permissions')
            ->where('record.id', $medicalRecord->id)
            ->where('patient.id', $this->patient->id)
            ->where('record.symptoms', 'Test symptoms for consultation')
        );
    }

    /** @test */
    public function it_can_complete_consultation_with_finalized_status()
    {
        // Set current user to doctor (only doctors can complete consultations)
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        $consultationData = [
            'diagnosis' => 'Test diagnosis',
            'treatment' => 'Test treatment plan',
            'notes' => 'Additional consultation notes',
            'status' => MedicalRecord::STATUS_FINALIZED
        ];

        $response = $this->put("/medical-records/{$medicalRecord->id}/consultation", $consultationData);

        $response->assertRedirect("/medical-records/{$medicalRecord->id}");
        $response->assertSessionHas('success', 'Consultation completed successfully');

        $medicalRecord->refresh();
        $this->assertEquals('Test diagnosis', $medicalRecord->diagnosis);
        $this->assertEquals('Test treatment plan', $medicalRecord->treatment);
        $this->assertEquals('Additional consultation notes', $medicalRecord->notes);
        $this->assertEquals(MedicalRecord::STATUS_FINALIZED, $medicalRecord->status);
    }

    /** @test */
    public function it_can_complete_consultation_with_needs_followup_status()
    {
        // Set current user to doctor
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Complex symptoms requiring follow-up'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        $consultationData = [
            'diagnosis' => 'Preliminary diagnosis',
            'treatment' => 'Initial treatment with follow-up required',
            'notes' => 'Patient needs follow-up in 2 weeks',
            'status' => MedicalRecord::STATUS_NEEDS_FOLLOWUP
        ];

        $response = $this->put("/medical-records/{$medicalRecord->id}/consultation", $consultationData);

        $response->assertRedirect("/medical-records/{$medicalRecord->id}");
        $response->assertSessionHas('success', 'Consultation completed successfully');

        $medicalRecord->refresh();
        $this->assertEquals(MedicalRecord::STATUS_NEEDS_FOLLOWUP, $medicalRecord->status);
    }

    /** @test */
    public function it_validates_required_fields_in_consultation()
    {
        // Set current user to doctor
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        // Test with missing required fields
        $response = $this->put("/medical-records/{$medicalRecord->id}/consultation", [
            'notes' => 'Some notes'
            // Missing diagnosis, treatment, and status
        ]);

        $response->assertSessionHasErrors(['diagnosis', 'treatment', 'status']);
    }

    /** @test */
    public function it_validates_status_values_in_consultation()
    {
        // Set current user to doctor
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        // Test with invalid status
        $response = $this->put("/medical-records/{$medicalRecord->id}/consultation", [
            'diagnosis' => 'Test diagnosis',
            'treatment' => 'Test treatment',
            'status' => 'Invalid Status'
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function it_cannot_start_consultation_for_nonexistent_record()
    {
        // Set current user to doctor
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $response = $this->post("/medical-records/99999/start-consultation");

        $response->assertRedirect('/medical-records');
        $response->assertSessionHas('error', 'Medical record not found');
    }

    /** @test */
    public function it_cannot_access_consultation_for_nonexistent_record()
    {
        // Set current user to doctor
        app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
        
        $response = $this->get("/medical-records/99999/consultation");

        $response->assertRedirect('/medical-records');
        $response->assertSessionHas('error', 'Medical record not found');
    }

    /** @test */
    public function staff_cannot_start_consultation()
    {
        // Set current user to staff (staff cannot start consultations)
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'anamnesis_id' => $this->anamnesis->id,
            'symptoms' => 'Test symptoms'
        ]);

        $response = $this->post("/medical-records/{$medicalRecord->id}/start-consultation");

        // Should redirect back with error due to authorization failure
        $response->assertRedirect('/medical-records');
        $response->assertSessionHas('error', 'Only doctors can start consultations.');
    }

    /** @test */
    public function staff_cannot_complete_consultation()
    {
        // Set current user to staff (staff cannot complete consultations)
        app(CurrentUserService::class)->setCurrentUser($this->staffUser);
        
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        $consultationData = [
            'diagnosis' => 'Test diagnosis',
            'treatment' => 'Test treatment plan',
            'notes' => 'Additional consultation notes',
            'status' => MedicalRecord::STATUS_FINALIZED
        ];

        $response = $this->put("/medical-records/{$medicalRecord->id}/consultation", $consultationData);

        // Should redirect back with error due to authorization failure
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Only doctors can complete consultations.');
    }
} 