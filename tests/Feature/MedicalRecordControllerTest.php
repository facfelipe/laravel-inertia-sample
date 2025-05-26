<?php

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use App\Services\CurrentUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
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
    
    // Set default user to staff for most tests
    app(CurrentUserService::class)->setCurrentUser($this->staffUser);
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

test('can update a medical record as staff without diagnosis and treatment', function () {
    // Arrange - staff user can update but not diagnosis/treatment
    $data = [
        'patient_id' => $this->patient->id,
        'symptoms' => 'Updated symptoms',
        'notes' => 'Updated notes',
    ];
    
    // Act
    $response = $this->put("/medical-records/{$this->medicalRecord->id}", $data);
    
    // Assert - web routes typically redirect after PUT
    $response->assertStatus(302);
    
    // Verify the record was updated (only allowed fields)
    $this->assertDatabaseHas('medical_records', [
        'id' => $this->medicalRecord->id,
        'symptoms' => 'Updated symptoms',
        'notes' => 'Updated notes',
        // diagnosis and treatment should remain unchanged
        'diagnosis' => 'Test diagnosis',
        'treatment' => 'Test treatment',
    ]);
});

test('doctor can update medical record including diagnosis and treatment', function () {
    // Set current user to doctor
    app(CurrentUserService::class)->setCurrentUser($this->doctorUser);
    
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

test('staff cannot update diagnosis and treatment fields', function () {
    // Arrange - staff user tries to update diagnosis/treatment
    $data = [
        'patient_id' => $this->patient->id,
        'symptoms' => 'Updated symptoms',
        'diagnosis' => 'Updated diagnosis', // Staff cannot update this
        'treatment' => 'Updated treatment', // Staff cannot update this
        'notes' => 'Updated notes',
    ];
    
    // Act
    $response = $this->put("/medical-records/{$this->medicalRecord->id}", $data);
    
    // Assert - should redirect back with error
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['error']);
    
    // Verify diagnosis and treatment were NOT updated
    $this->assertDatabaseHas('medical_records', [
        'id' => $this->medicalRecord->id,
        'diagnosis' => 'Test diagnosis', // Should remain unchanged
        'treatment' => 'Test treatment', // Should remain unchanged
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