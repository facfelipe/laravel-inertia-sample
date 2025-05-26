<?php

namespace App\Console\Commands;

use App\Events\MedicalRecordUpdated;
use App\Models\MedicalRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Broadcast;

class DebugWebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:websocket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug WebSocket configuration and connection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 WebSocket Configuration Debug');
        $this->newLine();
        
        // Check broadcasting configuration
        $driver = config('broadcasting.default');
        $this->info("📡 Broadcasting driver: {$driver}");
        
        if ($driver === 'null') {
            $this->error('❌ Broadcasting is disabled');
            return 1;
        }
        
        // Check Reverb configuration
        if ($driver === 'reverb') {
            $this->info('🚀 Reverb Configuration:');
            $this->line("   App ID: " . config('broadcasting.connections.reverb.app_id'));
            $this->line("   App Key: " . config('broadcasting.connections.reverb.key'));
            $this->line("   Host: " . config('broadcasting.connections.reverb.options.host'));
            $this->line("   Port: " . config('broadcasting.connections.reverb.options.port'));
            $this->line("   Scheme: " . config('broadcasting.connections.reverb.options.scheme'));
        }
        
        // Check queue configuration
        $queueDriver = config('queue.default');
        $this->info("⚡ Queue driver: {$queueDriver}");
        
        $this->newLine();
        $this->info('🧪 Testing Event Broadcasting...');
        
        // Get a medical record to test with
        $record = MedicalRecord::with(['patient', 'anamnesis'])->latest()->first();
        
        if (!$record) {
            $this->error('❌ No medical records found for testing');
            return 1;
        }
        
        $this->info("📋 Using Medical Record #{$record->id} - Patient: {$record->patient->name}");
        
        try {
            // Test broadcasting
            $event = new MedicalRecordUpdated($record, 'debug_test');
            broadcast($event);
            
            $this->info('✅ Event dispatched successfully!');
            $this->newLine();
            
            $this->info('🔍 Frontend Debugging Steps:');
            $this->line('1. Open browser developer console (F12)');
            $this->line('2. Navigate to your application');
            $this->line('3. Look for these console messages:');
            $this->line('   ✅ "🚀 Initializing Laravel Reverb WebSocket connection..."');
            $this->line('   ✅ "✅ Laravel Reverb WebSocket Broadcasting Enabled"');
            $this->line('   ✅ "✅ Connected to medical-records channel"');
            $this->line('   📡 "📡 Medical record update received: ..."');
            $this->newLine();
            
            $this->info('❌ If you see errors instead:');
            $this->line('   • Check if Reverb server is running: php artisan reverb:start');
            $this->line('   • Check if queue worker is running: php artisan queue:work');
            $this->line('   • Verify environment variables in .env file');
            $this->line('   • Rebuild frontend: npm run build');
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to dispatch event: {$e->getMessage()}");
            return 1;
        }
        
        return 0;
    }
} 