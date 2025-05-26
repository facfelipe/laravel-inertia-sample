<?php

namespace App\Repositories;

use App\Models\MedicalRecord;
use Illuminate\Support\Collection;

class MedicalRecordRepository
{
    /**
     * Get all medical records with patient data
     */
    public function getAllWithPatients()
    {
        return MedicalRecord::with('patient')->get();
    }

    /**
     * Find a medical record by ID
     */
    public function find($id)
    {
        return MedicalRecord::find($id);
    }

    /**
     * Get medical records for a specific patient
     */
    public function getByPatientId($patientId)
    {
        return MedicalRecord::where('patient_id', $patientId)->get();
    }

    /**
     * Create a new medical record
     */
    public function create(array $data)
    {
        return MedicalRecord::create($data);
    }

    /**
     * Update a medical record
     */
    public function update($id, array $data)
    {
        $record = $this->find($id);
        
        if (!$record) {
            return false;
        }
        
        $record->update($data);
        return $record;
    }

    /**
     * Delete a medical record
     */
    public function delete($id)
    {
        $record = $this->find($id);
        
        if (!$record) {
            return false;
        }
        
        return $record->delete();
    }
} 