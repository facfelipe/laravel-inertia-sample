<?php

namespace App\Console\Commands;

use App\Services\AnamnesisService;
use App\Services\MedicalRecordService;
use App\Services\PatientService;
use Illuminate\Console\Command;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class GenerateMedicalData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medical:generate {count=10 : Number of patients to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake medical data including patients, anamneses, and medical records';

    /**
     * Execute the console command.
     */
    public function handle(
        PatientService $patientService,
        AnamnesisService $anamnesisService,
        MedicalRecordService $medicalRecordService
    ) {
        $patientCount = $this->argument('count');
        $faker = Faker::create();
        
        $this->info("Generating data for {$patientCount} patients...");
        $this->info("Real-time broadcasting enabled - check your browser for live updates!");
        
        $progressBar = $this->output->createProgressBar($patientCount);
        $progressBar->start();
        
        $totalRecordsCreated = 0;
        $successfulPatients = 0;
        
        // Use a single transaction for small counts (like in tests) to ensure atomicity
        $useSingleTransaction = $patientCount <= 1;
        
        if ($useSingleTransaction) {
            DB::beginTransaction();
        }
        
        try {
            for ($i = 0; $i < $patientCount; $i++) {
                try {
                    // Use individual transactions for each patient only for larger counts
                    if (!$useSingleTransaction) {
                        DB::beginTransaction();
                    }
                    
                    // 1. Create a patient
                    $patient = $patientService->create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                        'phone' => $faker->phoneNumber,
                        'birth_date' => $faker->date('Y-m-d', '-18 years'),
                        'address' => $faker->address,
                    ]);
                    
                    // Determine how many medical records to create for this patient (1-3)
                    $recordCount = rand(1, 3);
                    
                    for ($j = 0; $j < $recordCount; $j++) {
                        // 2. Create an anamnesis for the patient for each visit
                        $anamnesis = $anamnesisService->create([
                            'patient_id' => $patient->id,
                            'blood_pressure' => rand(90, 140) . '/' . rand(60, 90),
                            'temperature' => $faker->randomFloat(1, 36.0, 38.5),
                            'heart_rate' => $faker->numberBetween(60, 100),
                            'respiratory_rate' => $faker->numberBetween(12, 20),
                            'weight' => $faker->randomFloat(1, 50, 120),
                            'height' => $faker->numberBetween(150, 200),
                        ]);
                        
                        // 3. Create a medical record linked to the anamnesis
                        $medicalRecord = $medicalRecordService->create([
                            'patient_id' => $patient->id,
                            'anamnesis_id' => $anamnesis->id,
                            'symptoms' => $faker->sentence(10),
                            'diagnosis' => $faker->sentence(5),
                            'treatment' => $faker->paragraph(1),
                            'notes' => $faker->paragraph(2),
                        ]);
                        
                        $totalRecordsCreated++;
                    }
                    
                    if (!$useSingleTransaction) {
                        DB::commit();
                    }
                    
                    $successfulPatients++;
                    
                    // Add a small delay to make real-time updates more visible (only for larger counts)
                    if (!$useSingleTransaction) {
                        usleep(500000); // 0.5 seconds
                    }
                    
                } catch (\Exception $e) {
                    if (!$useSingleTransaction) {
                        DB::rollBack();
                        $this->newLine();
                        $this->error("Failed to generate data for patient {$i}: {$e->getMessage()}");
                        continue;
                    } else {
                        // For single transaction mode (tests), re-throw the exception
                        throw $e;
                    }
                }
                
                $progressBar->advance();
            }
            
            if ($useSingleTransaction) {
                DB::commit();
            }
            
        } catch (\Exception $e) {
            if ($useSingleTransaction) {
                DB::rollBack();
                $this->newLine();
                $this->error("Failed to generate data: {$e->getMessage()}");
                return Command::FAILURE;
            }
            throw $e;
        }
        
        $progressBar->finish();
        
        // Check if any patients were successfully created
        if ($successfulPatients === 0) {
            $this->newLine();
            $this->error("Failed to generate any patients successfully!");
            return Command::FAILURE;
        }
        
        $this->newLine();
        $this->info("Successfully generated data for {$successfulPatients} patients!");
        $this->info("Total medical records created: {$totalRecordsCreated}");
        $this->info("🚀 Check your browser - records should have appeared in real-time!");
        return Command::SUCCESS;
    }
}
