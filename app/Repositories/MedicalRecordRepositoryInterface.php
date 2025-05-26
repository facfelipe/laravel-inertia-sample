<?php

namespace App\Repositories;

use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicalRecordRepositoryInterface
{
    public function getAll(): Collection;
    
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;
    
    public function findById(int $id): ?MedicalRecord;
    
    public function create(array $data): MedicalRecord;
    
    public function update(int $id, array $data): ?MedicalRecord;
    
    public function delete(int $id): bool;
    
    public function getByPatient(int $patientId): Collection;
    
    public function getByUser(int $userId): Collection;
} 