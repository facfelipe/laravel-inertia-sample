<?php

namespace App\Services;

use App\Models\Patient;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PatientService
{
    protected $patientRepository;

    public function __construct(PatientRepository $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    public function getAll(?int $perPage = null): Collection|LengthAwarePaginator
    {
        if ($perPage) {
            return $this->patientRepository->getAll($perPage);
        }
        
        return $this->getAllPatients();
    }

    public function getAllPatients(): Collection
    {
        return Patient::all();
    }

    public function getPatientById(int $id): ?Patient
    {
        return $this->patientRepository->getById($id);
    }

    public function getById(int $id): ?Patient
    {
        return $this->getPatientById($id);
    }

    public function create(array $data): Patient
    {
        try {
            return DB::transaction(function () use ($data) {
                return $this->patientRepository->create($data);
            });
        } catch (\Exception $e) {
            Log::error('Error creating patient: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updatePatient(int $id, array $data): Patient
    {
        $this->patientRepository->update($id, $data);
        return $this->getPatientById($id);
    }

    public function deletePatient(int $id): bool
    {
        return $this->patientRepository->delete($id);
    }
}