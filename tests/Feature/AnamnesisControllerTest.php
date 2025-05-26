<?php

use App\Models\Anamnesis;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a user for authentication (though not currently used in routes)
    $this->user = User::factory()->create();
    
    // Create a patient for testing
    $this->patient = Patient::factory()->create();
});

test('can store anamnesis for a patient', function () {
    // Arrange
    $data = [
        'blood_pressure' => '120/80',
        'temperature' => 36.5,
        'heart_rate' => 75,
        'respiratory_rate' => 18,
        'weight' => 70.5,
        'height' => 175.0,
    ];
    
    // Act
    $response = $this->post("/patients/{$this->patient->id}/anamneses", $data);
    
    // Assert - web routes typically redirect after POST
    $response->assertStatus(302);
    
    // Verify the anamnesis was created
    $this->assertDatabaseHas('anamneses', [
        'patient_id' => $this->patient->id,
        'blood_pressure' => '120/80',
        'temperature' => 36.5,
        'heart_rate' => 75,
        'respiratory_rate' => 18,
        'weight' => 70.5,
        'height' => 175.0,
    ]);
});

test('can update existing anamnesis for a patient', function () {
    // Create an existing anamnesis
    $existingAnamnesis = Anamnesis::factory()->create([
        'patient_id' => $this->patient->id,
        'blood_pressure' => '110/70',
        'temperature' => 36.0,
    ]);
    
    // New data to update
    $data = [
        'blood_pressure' => '125/85',
        'temperature' => 37.0,
        'heart_rate' => 80,
    ];
    
    // Act - should update existing anamnesis
    $response = $this->post("/patients/{$this->patient->id}/anamneses", $data);
    
    // Assert
    $response->assertStatus(302);
    
    // Verify the anamnesis was updated (not a new one created)
    $this->assertDatabaseCount('anamneses', 1);
    $this->assertDatabaseHas('anamneses', [
        'patient_id' => $this->patient->id,
        'blood_pressure' => '125/85',
        'temperature' => 37.0,
        'heart_rate' => 80,
    ]);
});

test('returns redirect for non-existent patient', function () {
    // Arrange
    $data = [
        'blood_pressure' => '120/80',
        'temperature' => 36.5,
    ];
    
    // Act
    $response = $this->post('/patients/999/anamneses', $data);
    
    // Assert - should redirect with error
    $response->assertStatus(302);
});

test('validates numeric values in anamnesis', function () {
    // Arrange - invalid data
    $invalidData = [
        'temperature' => 'not-a-number',
        'heart_rate' => 'invalid',
        'weight' => 'abc',
        'height' => 'xyz',
    ];
    
    // Act
    $response = $this->post("/patients/{$this->patient->id}/anamneses", $invalidData);
    
    // Assert - should redirect back with validation errors
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['temperature', 'heart_rate', 'weight', 'height']);
});
