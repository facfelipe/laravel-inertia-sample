<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalRecord;

class AssignDefaultStatusToMedicalRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Assigning default status to medical records without status...');

        // Get all medical records that don't have any status
        $recordsWithoutStatus = MedicalRecord::whereDoesntHave('statuses')->get();

        $this->command->info("Found {$recordsWithoutStatus->count()} records without status.");

        foreach ($recordsWithoutStatus as $record) {
            // Set default status to 'Pending'
            $record->setStatus(MedicalRecord::STATUS_PENDING);
            $this->command->info("Assigned 'Pending' status to record ID: {$record->id}");
        }

        $this->command->info('Completed assigning default statuses.');
    }
}
