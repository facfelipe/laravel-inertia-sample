<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'symptoms' => $this->faker->randomElement([
                'Headache and fever',
                'Chest pain and shortness of breath',
                'Abdominal pain and nausea',
                'Fatigue and dizziness',
                'Cough and sore throat',
                'Back pain and muscle stiffness'
            ]),
            'diagnosis' => $this->faker->randomElement([
                'Common cold',
                'Hypertension',
                'Gastritis',
                'Migraine',
                'Bronchitis',
                'Muscle strain'
            ]),
            'treatment' => $this->faker->randomElement([
                'Rest and hydration',
                'Prescribed medication',
                'Physical therapy',
                'Dietary changes',
                'Follow-up in 2 weeks',
                'Specialist referral'
            ]),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
