<?php

namespace App\Events;

use App\Models\MedicalRecord;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicalRecordUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $medicalRecord;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(MedicalRecord $medicalRecord, string $action = 'updated')
    {
        $this->medicalRecord = $medicalRecord->load(['patient', 'anamnesis']);
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('medical-records'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'medical.record.updated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'medical_record' => [
                'id' => $this->medicalRecord->id,
                'patient_id' => $this->medicalRecord->patient_id,
                'symptoms' => $this->medicalRecord->symptoms,
                'diagnosis' => $this->medicalRecord->diagnosis,
                'treatment' => $this->medicalRecord->treatment,
                'notes' => $this->medicalRecord->notes,
                'created_at' => $this->medicalRecord->created_at,
                'updated_at' => $this->medicalRecord->updated_at,
                'patient' => $this->medicalRecord->patient ? [
                    'id' => $this->medicalRecord->patient->id,
                    'name' => $this->medicalRecord->patient->name,
                    'email' => $this->medicalRecord->patient->email,
                ] : null,
                'current_status' => $this->medicalRecord->status,
            ],
            'action' => $this->action,
            'timestamp' => now()->toISOString(),
        ];
    }
}
