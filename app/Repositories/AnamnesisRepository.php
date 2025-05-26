<?php

namespace App\Repositories;

use App\Models\Anamnesis;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AnamnesisRepository implements AnamnesisRepositoryInterface
{
    /**
     * Get all anamneses with pagination.
     */
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Anamnesis::with('patient')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get anamnesis for a specific patient (latest one only for interface consistency).
     */
    public function getByPatientId(int $patientId): ?Anamnesis
    {
        return Anamnesis::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get all anamneses for a specific patient (keeping original functionality).
     */
    public function getAllByPatientId(int $patientId): Collection
    {
        return Anamnesis::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a specific anamnesis by ID.
     */
    public function getById(int $id): ?Anamnesis
    {
        return Anamnesis::with('patient')->find($id);
    }

    /**
     * Create a new anamnesis.
     */
    public function create(array $data): Anamnesis
    {
        return Anamnesis::create($data);
    }

    /**
     * Update an existing anamnesis.
     */
    public function update(int $id, array $data): bool
    {
        $anamnesis = Anamnesis::find($id);
        
        if (!$anamnesis) {
            return false;
        }
        
        return $anamnesis->update($data);
    }

    /**
     * Delete an anamnesis.
     */
    public function delete(int $id): bool
    {
        $anamnesis = Anamnesis::find($id);
        
        if (!$anamnesis) {
            return false;
        }
        
        return $anamnesis->delete();
    }

    /**
     * Get the most recent anamnesis for a specific patient.
     */
    public function getLatestByPatientId(int $patientId): ?Anamnesis
    {
        return Anamnesis::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Create or update anamnesis for a patient (upsert).
     */
    public function upsert(array $data): Anamnesis
    {
        $patientId = (int) $data['patient_id'];
        
        // Check if anamnesis already exists for this patient
        $existingAnamnesis = $this->getLatestByPatientId($patientId);
        
        if ($existingAnamnesis) {
            // Update existing anamnesis
            $existingAnamnesis->update($data);
            return $existingAnamnesis->fresh();
        } else {
            // Create new anamnesis
            return $this->create($data);
        }
    }
} 