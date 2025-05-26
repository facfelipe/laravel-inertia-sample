<?php

use App\Console\Commands\GenerateMedicalData;
use App\Models\Anamnesis;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('command generates the specified number of records', function () {
    // Get the initial record counts
    $initialPatientCount = Patient::count();
    $initialAnamnesisCount = Anamnesis::count();
    $initialMedicalRecordCount = MedicalRecord::count();
    
    // Execute the command with 5 records
    $this->artisan(GenerateMedicalData::class, ['count' => 5])
         ->assertSuccessful();
    
    // Verify that 5 patients were created
    $this->assertDatabaseCount('patients', $initialPatientCount + 5);
    
    // The command creates 1-3 anamneses and medical records per patient,
    // so just verify that counts have increased by at least 5
    $this->assertGreaterThanOrEqual($initialAnamnesisCount + 5, Anamnesis::count());
    $this->assertGreaterThanOrEqual($initialMedicalRecordCount + 5, MedicalRecord::count());
    
    // Also verify that the same number of anamneses and medical records were created
    $this->assertEquals(Anamnesis::count() - $initialAnamnesisCount, 
                        MedicalRecord::count() - $initialMedicalRecordCount);
});

test('command generates records with valid data', function () {
    // Execute the command with 1 record for easier testing
    $this->artisan(GenerateMedicalData::class, ['count' => 1])
         ->assertSuccessful();
    
    // Get the generated records
    $patient = Patient::latest('id')->first();
    $anamnesis = Anamnesis::where('patient_id', $patient->id)->first();
    $medicalRecord = MedicalRecord::where('patient_id', $patient->id)->first();
    
    // Verify the patient data is valid
    expect($patient)->not->toBeNull()
        ->and($patient->name)->not->toBeEmpty()
        ->and($patient->email)->toContain('@')
        ->and($patient->birth_date)->not->toBeNull();
    
    // Verify the anamnesis data is valid and linked to the patient
    expect($anamnesis)->not->toBeNull()
        ->and($anamnesis->patient_id)->toBe($patient->id)
        ->and($anamnesis->blood_pressure)->not->toBeNull();
    
    // Verify the medical record data is valid and linked to the patient
    expect($medicalRecord)->not->toBeNull()
        ->and($medicalRecord->patient_id)->toBe($patient->id)
        ->and($medicalRecord->symptoms)->not->toBeEmpty();
});

test('command respects transaction handling', function () {
    // Mock the MedicalRecordService to throw an exception
    $this->mock(\App\Services\MedicalRecordService::class, function ($mock) {
        $mock->shouldReceive('create')
             ->andThrow(new \Exception('Test exception'));
    });
    
    // Execute the command and expect failure
    $this->artisan(GenerateMedicalData::class)
         ->assertFailed();
    
    // Verify that no records were created due to transaction rollback
    $this->assertDatabaseCount('patients', 0);
    $this->assertDatabaseCount('anamneses', 0);
    $this->assertDatabaseCount('medical_records', 0);
});
