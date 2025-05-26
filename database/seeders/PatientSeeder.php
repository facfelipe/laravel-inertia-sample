<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample patient data
        $patients = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '555-123-4567',
                'birth_date' => '1980-05-15',
                'address' => '123 Main St, Anytown, USA',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '555-234-5678',
                'birth_date' => '1992-08-22',
                'address' => '456 Oak Ave, Somewhere, USA',
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@example.com',
                'phone' => '555-345-6789',
                'birth_date' => '1975-11-10',
                'address' => '789 Pine Rd, Elsewhere, USA',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'phone' => '555-456-7890',
                'birth_date' => '1988-03-30',
                'address' => '321 Cedar Ln, Nowhere, USA',
            ],
            [
                'name' => 'Robert Wilson',
                'email' => 'robert.wilson@example.com',
                'phone' => '555-567-8901',
                'birth_date' => '1960-07-18',
                'address' => '654 Birch Dr, Anywhere, USA',
            ],
        ];
        
        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }
    }
}
