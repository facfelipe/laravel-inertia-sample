<?php

namespace App\Observers;

use App\Events\MedicalRecordUpdated;
use App\Models\MedicalRecord;

class MedicalRecordObserver
{
    /**
     * Handle the MedicalRecord "created" event.
     */
    public function created(MedicalRecord $medicalRecord): void
    {
        // Only set status if the record doesn't already have one
        if (!$medicalRecord->statuses()->exists()) {
            $medicalRecord->setStatus(MedicalRecord::STATUS_PENDING);
        }
        
        // Broadcast the creation event
        broadcast(new MedicalRecordUpdated($medicalRecord, 'created'));
    }

    /**
     * Handle the MedicalRecord "updated" event.
     */
    public function updated(MedicalRecord $medicalRecord): void
    {
        // Broadcast the update event
        broadcast(new MedicalRecordUpdated($medicalRecord, 'updated'));
    }

    /**
     * Handle the MedicalRecord "deleted" event.
     */
    public function deleted(MedicalRecord $medicalRecord): void
    {
        // Broadcast the deletion event
        broadcast(new MedicalRecordUpdated($medicalRecord, 'deleted'));
    }

    /**
     * Handle the MedicalRecord "restored" event.
     */
    public function restored(MedicalRecord $medicalRecord): void
    {
        // Broadcast the restoration event
        broadcast(new MedicalRecordUpdated($medicalRecord, 'restored'));
    }

    /**
     * Handle status changes specifically.
     * This method can be called manually when status changes occur.
     */
    public function statusChanged(MedicalRecord $medicalRecord): void
    {
        // Broadcast the status change event
        broadcast(new MedicalRecordUpdated($medicalRecord, 'status_changed'));
    }
} 