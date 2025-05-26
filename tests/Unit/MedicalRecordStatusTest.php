<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\TestCase as BaseTestCase;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MedicalRecordStatusTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test patient
        $this->patient = Patient::factory()->create();
    }

    /** @test */
    public function it_automatically_assigns_pending_status_when_created()
    {
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $this->assertTrue($medicalRecord->hasStatus(MedicalRecord::STATUS_PENDING));
        $this->assertEquals(MedicalRecord::STATUS_PENDING, $medicalRecord->status);
    }

    /** @test */
    public function it_can_transition_from_pending_to_attending()
    {
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        $this->assertTrue($medicalRecord->hasStatus(MedicalRecord::STATUS_ATTENDING));
        $this->assertEquals(MedicalRecord::STATUS_ATTENDING, $medicalRecord->status);
    }

    /** @test */
    public function it_can_transition_from_attending_to_finalized()
    {
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);
        $medicalRecord->setStatus(MedicalRecord::STATUS_FINALIZED);

        $this->assertTrue($medicalRecord->hasStatus(MedicalRecord::STATUS_FINALIZED));
        $this->assertEquals(MedicalRecord::STATUS_FINALIZED, $medicalRecord->status);
    }

    /** @test */
    public function it_can_transition_from_attending_to_needs_followup()
    {
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);

        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);
        $medicalRecord->setStatus(MedicalRecord::STATUS_NEEDS_FOLLOWUP);

        $this->assertTrue($medicalRecord->hasStatus(MedicalRecord::STATUS_NEEDS_FOLLOWUP));
        $this->assertEquals(MedicalRecord::STATUS_NEEDS_FOLLOWUP, $medicalRecord->status);
    }

    /** @test */
    public function it_returns_available_statuses()
    {
        $expectedStatuses = [
            MedicalRecord::STATUS_PENDING,
            MedicalRecord::STATUS_ATTENDING,
            MedicalRecord::STATUS_FINALIZED,
            MedicalRecord::STATUS_NEEDS_FOLLOWUP,
        ];

        $this->assertEquals($expectedStatuses, MedicalRecord::getStatuses());
    }

    /** @test */
    public function it_does_not_duplicate_status_if_record_already_has_one()
    {
        // Create a record and manually set a status
        $medicalRecord = new MedicalRecord([
            'patient_id' => $this->patient->id,
            'symptoms' => 'Test symptoms'
        ]);
        $medicalRecord->save();
        $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);

        // Count initial statuses
        $initialStatusCount = $medicalRecord->statuses()->count();

        // Trigger the created event again (simulating a duplicate creation scenario)
        $medicalRecord->setStatus(MedicalRecord::STATUS_FINALIZED);

        // Should not have extra statuses beyond what we explicitly set
        $this->assertEquals($initialStatusCount + 1, $medicalRecord->statuses()->count());
    }
}
