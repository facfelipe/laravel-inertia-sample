<?php

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user for authentication (though not currently used in routes)
    $this->user = User::factory()->create();
    
    // Create a patient for testing
    $this->patient = Patient::factory()->create();
    
    // Create a medical record for testing
    $this->medicalRecord = MedicalRecord::factory()->create([
        'patient_id' => $this->patient->id,
        'symptoms' => 'Test symptoms',
        'diagnosis' => 'Test diagnosis',
        'treatment' => 'Test treatment',
        'notes' => 'Test notes',
    ]);
});

test('can get medical records index page', function () {
    // Act
    $response = $this->get('/medical-records');
    
    // Assert
    $response->assertStatus(200);
    
    // Verify the medical record exists in database
    $this->assertDatabaseHas('medical_records', [
        'id' => $this->medicalRecord->id,
        'patient_id' => $this->patient->id
    ]);
});

test('can show a specific medical record', function () {
    // Act
    $response = $this->get("/medical-records/{$this->medicalRecord->id}");
    
    // Assert
    $response->assertStatus(200);
    
    // Verify the medical record data
    $this->assertDatabaseHas('medical_records', [
        'id' => $this->medicalRecord->id,
        'patient_id' => $this->patient->id,
        'symptoms' => 'Test symptoms'
    ]);
});

test('returns redirect for non-existent medical record', function () {
    // Act
    $response = $this->get('/medical-records/999');
    
    // Assert - should redirect with error for web routes
    $response->assertStatus(302);
});

test('can store a new medical record', function () {
    // Arrange
    $data = [
        'patient_id' => $this->patient->id,
        'symptoms' => 'New symptoms',
        'diagnosis' => 'New diagnosis',
        'treatment' => 'New treatment',
        'notes' => 'New notes',
    ];
    
    // Act
    $response = $this->post('/medical-records', $data);
    
    // Assert - web routes typically redirect after POST
    $response->assertStatus(302);
    
    // Verify the record was created
    $this->assertDatabaseHas('medical_records', [
        'patient_id' => $this->patient->id,
        'symptoms' => 'New symptoms',
        'diagnosis' => 'New diagnosis',
        'treatment' => 'New treatment',
        'notes' => 'New notes',
    ]);
});

test('can update a medical record', function () {
    // Arrange
    $data = [
        'patient_id' => $this->patient->id,
        'symptoms' => 'Updated symptoms',
        'diagnosis' => 'Updated diagnosis',
        'treatment' => 'Updated treatment',
        'notes' => 'Updated notes',
    ];
    
    // Act
    $response = $this->put("/medical-records/{$this->medicalRecord->id}", $data);
    
    // Assert - web routes typically redirect after PUT
    $response->assertStatus(302);
    
    // Verify the record was updated
    $this->assertDatabaseHas('medical_records', [
        'id' => $this->medicalRecord->id,
        'symptoms' => 'Updated symptoms',
        'diagnosis' => 'Updated diagnosis',
        'treatment' => 'Updated treatment',
        'notes' => 'Updated notes',
    ]);
});

test('can delete a medical record', function () {
    // Act
    $response = $this->delete("/medical-records/{$this->medicalRecord->id}");
    
    // Assert - web routes typically redirect after DELETE
    $response->assertStatus(302);
    
    // Verify the record was deleted
    $this->assertDatabaseMissing('medical_records', [
        'id' => $this->medicalRecord->id
    ]);
});

test('returns redirect when deleting non-existent medical record', function () {
    // Act
    $response = $this->delete('/medical-records/999');
    
    // Assert - should redirect with error
    $response->assertStatus(302);
});

test('validates required fields when creating a medical record', function () {
    // Act - try to create without required fields
    $response = $this->post('/medical-records', []);
    
    // Assert - should redirect back with validation errors
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['symptoms']); // symptoms is required
}); 