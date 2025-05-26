<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository
{
    /**
     * Get all patients with pagination.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Patient::orderBy('name')->paginate($perPage);
    }

    /**
     * Get a specific patient by ID.
     */
    public function getById(int $id): ?Patient
    {
        return Patient::find($id);
    }

    /**
     * Create a new patient.
     */
    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    /**
     * Update an existing patient.
     */
    public function update(int $id, array $data): bool
    {
        $patient = Patient::find($id);
        
        if (!$patient) {
            return false;
        }
        
        return $patient->update($data);
    }

    /**
     * Delete a patient.
     */
    public function delete(int $id): bool
    {
        $patient = Patient::find($id);
        
        if (!$patient) {
            return false;
        }
        
        return $patient->delete();
    }
} 