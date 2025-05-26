<?php

namespace Tests\Unit;

use App\Models\Anamnesis;
use App\Models\Patient;
use App\Repositories\AnamnesisRepository;
use App\Services\AnamnesisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnamnesisServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected $anamnesisRepository;
    protected $anamnesisService;
    protected $patient;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a patient for testing
        $this->patient = Patient::create([
            'name' => 'Test Patient',
            'email' => 'patient@example.com',
            'phone' => '123-456-7890',
            'birth_date' => '1990-01-01',
            'address' => '123 Test St',
        ]);
        
        // Create the repository and service
        $this->anamnesisRepository = new AnamnesisRepository();
        $this->anamnesisService = new AnamnesisService($this->anamnesisRepository);
    }

    public function test_can_create_anamnesis_with_valid_data()
    {
        // Prepare test data
        $data = [
            'patient_id' => $this->patient->id,
            'blood_pressure' => '120/80',
            'temperature' => 37.2,
            'heart_rate' => 72,
            'respiratory_rate' => 16,
            'weight' => 70.5,
            'height' => 175,
        ];
        
        // Call the service method
        $anamnesis = $this->anamnesisService->create($data);
        
        // Assert that an anamnesis was created
        $this->assertInstanceOf(Anamnesis::class, $anamnesis);
        $this->assertEquals($this->patient->id, $anamnesis->patient_id);
        $this->assertEquals('120/80', $anamnesis->blood_pressure);
        $this->assertEquals(37.2, $anamnesis->temperature);
        $this->assertEquals(72, $anamnesis->heart_rate);
        $this->assertEquals(16, $anamnesis->respiratory_rate);
        $this->assertEquals(70.5, $anamnesis->weight);
        $this->assertEquals(175, $anamnesis->height);
    }

    public function test_calculating_BMI_works_correctly()
    {
        // Create anamnesis with height and weight
        $anamnesis = $this->anamnesisService->create([
            'patient_id' => $this->patient->id,
            'weight' => 70, // kg
            'height' => 175, // cm
        ]);
        
        // Calculate expected BMI: 70 / (1.75 * 1.75) ≈ 22.9
        $expectedBmi = round(70 / pow(175/100, 2), 1);
        
        // Assert that BMI is calculated correctly
        $this->assertEquals($expectedBmi, $anamnesis->bmi);
    }
}
