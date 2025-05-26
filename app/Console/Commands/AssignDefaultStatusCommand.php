<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MedicalRecord;

class AssignDefaultStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medical-records:assign-default-status {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign default "Pending" status to medical records without any status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Checking for medical records without status...');

        // Get all medical records that don't have any status
        $recordsWithoutStatus = MedicalRecord::whereDoesntHave('statuses')->get();

        if ($recordsWithoutStatus->isEmpty()) {
            $this->info('✅ All medical records already have a status assigned.');
            return 0;
        }

        $count = $recordsWithoutStatus->count();
        
        if ($dryRun) {
            $this->warn("🔍 DRY RUN: Found {$count} records without status.");
            $this->warn('🔍 DRY RUN: Would assign "Pending" status to these records.');
            
            $this->table(['ID', 'Patient ID', 'Symptoms', 'Created At'], 
                $recordsWithoutStatus->map(function ($record) {
                    return [
                        $record->id,
                        $record->patient_id,
                        \Str::limit($record->symptoms, 50),
                        $record->created_at->format('Y-m-d H:i:s')
                    ];
                })->toArray()
            );
            
            $this->info('🔍 DRY RUN: Run without --dry-run flag to apply changes.');
            return 0;
        }

        if (!$this->confirm("Found {$count} records without status. Assign 'Pending' status to all of them?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($recordsWithoutStatus as $record) {
            $record->setStatus(MedicalRecord::STATUS_PENDING);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $this->info("✅ Successfully assigned 'Pending' status to {$count} medical records.");
        $this->info('💡 Tip: Use --dry-run flag to preview changes before applying them.');
        
        return 0;
    }
}
