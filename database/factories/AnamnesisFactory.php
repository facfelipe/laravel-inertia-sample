<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anamnesis>
 */
class AnamnesisFactory extends Factory
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
            'blood_pressure' => $this->faker->randomElement(['120/80', '130/85', '110/70', '140/90', '125/82']),
            'temperature' => $this->faker->randomFloat(1, 36.0, 38.5),
            'heart_rate' => $this->faker->numberBetween(60, 100),
            'respiratory_rate' => $this->faker->numberBetween(12, 20),
            'weight' => $this->faker->randomFloat(1, 50.0, 120.0),
            'height' => $this->faker->randomFloat(1, 150.0, 200.0),
        ];
    }
}
