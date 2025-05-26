<?php

namespace App\Console\Commands;

use App\Events\MedicalRecordUpdated;
use App\Models\MedicalRecord;
use Illuminate\Console\Command;

class TestMedicalRecordBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:broadcast {--record-id= : ID of medical record to broadcast}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test medical record broadcasting functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $recordId = $this->option('record-id');
        
        if ($recordId) {
            $medicalRecord = MedicalRecord::with(['patient', 'anamnesis'])->find($recordId);
            
            if (!$medicalRecord) {
                $this->error("Medical record with ID {$recordId} not found.");
                return 1;
            }
            
            $this->info("Broadcasting update for medical record ID: {$recordId}");
        } else {
            // Get the latest medical record
            $medicalRecord = MedicalRecord::with(['patient', 'anamnesis'])->latest()->first();
            
            if (!$medicalRecord) {
                $this->error("No medical records found in the database.");
                return 1;
            }
            
            $this->info("Broadcasting update for latest medical record ID: {$medicalRecord->id}");
        }

        // Broadcast the event
        try {
            broadcast(new MedicalRecordUpdated($medicalRecord, 'test_update'));
            $this->info("✅ Broadcast sent successfully!");
            $this->info("Patient: " . ($medicalRecord->patient->name ?? 'Unknown'));
            $this->info("Status: " . ($medicalRecord->status ?? 'No status'));
            $this->info("Updated: {$medicalRecord->updated_at}");
        } catch (\Exception $e) {
            $this->error("❌ Failed to broadcast: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
