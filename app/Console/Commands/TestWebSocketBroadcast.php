<?php

namespace App\Console\Commands;

use App\Events\MedicalRecordUpdated;
use App\Models\MedicalRecord;
use Illuminate\Console\Command;

class TestWebSocketBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:websocket {--record-id= : ID of medical record to broadcast}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test WebSocket broadcasting with medical records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing WebSocket Broadcasting...');
        
        // Check broadcasting configuration
        $driver = config('broadcasting.default');
        $this->info("📡 Broadcasting driver: {$driver}");
        
        if ($driver === 'null') {
            $this->warn('⚠️ Broadcasting is disabled (driver: null)');
            $this->info('💡 To enable broadcasting, set BROADCAST_DRIVER=reverb or BROADCAST_DRIVER=pusher in your .env file');
            return 1;
        }
        
        // Get medical record to broadcast
        $recordId = $this->option('record-id');
        
        if ($recordId) {
            $medicalRecord = MedicalRecord::with(['patient', 'anamnesis'])->find($recordId);
            
            if (!$medicalRecord) {
                $this->error("❌ Medical record with ID {$recordId} not found");
                return 1;
            }
        } else {
            // Get the latest medical record
            $medicalRecord = MedicalRecord::with(['patient', 'anamnesis'])->latest()->first();
            
            if (!$medicalRecord) {
                $this->error('❌ No medical records found in database');
                $this->info('💡 Create a medical record first or run: php artisan db:seed');
                return 1;
            }
        }
        
        $this->info("📋 Broadcasting update for Medical Record #{$medicalRecord->id}");
        $this->info("👤 Patient: {$medicalRecord->patient->name}");
        
        try {
            // Broadcast the event
            broadcast(new MedicalRecordUpdated($medicalRecord, 'test_broadcast'));
            
            $this->info('✅ Broadcast event dispatched successfully!');
            $this->info('🔍 Check your browser console for real-time updates');
            
            if ($driver === 'reverb') {
                $this->info('🚀 Make sure Reverb server is running: php artisan reverb:start');
            } elseif ($driver === 'pusher') {
                $this->info('📡 Using Pusher service for broadcasting');
            }
            
            $this->info('⚡ Make sure queue worker is running: php artisan queue:work');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to broadcast event: ' . $e->getMessage());
            
            if ($driver === 'reverb') {
                $this->error('💡 Reverb troubleshooting:');
                $this->error('   • Check if Reverb server is running: php artisan reverb:start');
                $this->error('   • Verify REVERB_* environment variables in .env');
                $this->error('   • Check if port 8080 is available');
            } elseif ($driver === 'pusher') {
                $this->error('💡 Pusher troubleshooting:');
                $this->error('   • Verify PUSHER_* credentials in .env');
                $this->error('   • Check if your Pusher app is active');
                $this->error('   • Ensure network connectivity to Pusher');
            }
            
            return 1;
        }
        
        return 0;
    }
} 