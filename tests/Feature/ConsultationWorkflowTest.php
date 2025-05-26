<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Anamnesis;

class ConsultationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->patient = Patient::factory()->create();
        $this->anamnesis = Anamnesis::factory()->create();
    }

    /** @test */
    public function it_can_start_consultation_for_pending_record()
    {
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
            ->where('record.id', $medicalRecord->id)
            ->where('patient.id', $this->patient->id)
            ->where('record.symptoms', 'Test symptoms for consultation')
        );
    }

    /** @test */
    public function it_can_complete_consultation_with_finalized_status()
    {
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
        $response = $this->post("/medical-records/99999/start-consultation");

        $response->assertRedirect('/medical-records');
        $response->assertSessionHas('error', 'Medical record not found');
    }

    /** @test */
    public function it_cannot_access_consultation_for_nonexistent_record()
    {
        $response = $this->get("/medical-records/99999/consultation");

        $response->assertRedirect('/medical-records');
        $response->assertSessionHas('error', 'Medical record not found');
    }
} 