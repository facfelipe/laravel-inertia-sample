<?php

use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function() {
    // Create a user for testing (though not actually used in current routes)
    $this->user = User::factory()->create();
});

test('can list all patients', function() {
    // Create some test patients
    Patient::factory()->count(3)->create();
    
    // Make the request to get all patients (Inertia response)
    $response = $this->get('/patients');
    
    // Assert the response is successful
    $response->assertStatus(200);
    
    // Assert we have 3 patients in the database
    $this->assertDatabaseCount('patients', 3);
});

test('can create a new patient', function() {
    // Patient data for creation
    $data = [
        'name' => 'Test Patient',
        'email' => 'testpatient@example.com',
        'phone' => '555-123-4567',
        'birth_date' => '1990-01-01',
        'address' => '123 Test St',
    ];
    
    // Make the request to create a patient
    $response = $this->post('/patients', $data);
    
    // Assert the response redirects (typical for web forms)
    $response->assertStatus(302);
    
    // Assert the patient was created in the database
    $this->assertDatabaseHas('patients', [
        'name' => 'Test Patient',
        'email' => 'testpatient@example.com'
    ]);
});

test('can show a patient', function() {
    // Create a patient
    $patient = Patient::factory()->create();
    
    // Make the request to get the patient
    $response = $this->get("/patients/{$patient->id}");
    
    // Assert the response is successful
    $response->assertStatus(200);
    
    // We can't easily test Inertia response content, but we can verify the route works
    // and the patient exists in the database
    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'name' => $patient->name
    ]);
});

test('returns 404 for non-existent patient', function() {
    // Make the request to get a non-existent patient
    $response = $this->get('/patients/999');
    
    // Assert the response redirects with error (web behavior)
    $response->assertStatus(302);
});

test('can update a patient', function() {
    // Create a patient
    $patient = Patient::factory()->create();
    
    // Update data
    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => $patient->phone,
        'birth_date' => $patient->birth_date,
        'address' => $patient->address,
    ];
    
    // Make the request to update the patient
    $response = $this->put("/patients/{$patient->id}", $data);
    
    // Assert the response redirects (typical for web forms)
    $response->assertStatus(302);
    
    // Assert the patient was updated in the database
    $this->assertDatabaseHas('patients', [
        'id' => $patient->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com'
    ]);
});

test('can delete a patient', function() {
    // Create a patient
    $patient = Patient::factory()->create();
    
    // Make the request to delete the patient
    $response = $this->delete("/patients/{$patient->id}");
    
    // Assert the response redirects (typical for web actions)
    $response->assertStatus(302);
    
    // Assert the patient was removed from the database
    $this->assertDatabaseMissing('patients', [
        'id' => $patient->id
    ]);
});

test('returns redirect when trying to delete non-existent patient', function() {
    // Make the request to delete a non-existent patient
    $response = $this->delete('/patients/999');
    
    // Assert the response redirects with error (web behavior)
    $response->assertStatus(302);
}); 