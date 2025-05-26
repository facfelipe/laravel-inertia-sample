<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    public function getAll(): Collection;
    
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;
    
    public function findById(int $id): ?Patient;
    
    public function create(array $data): Patient;
    
    public function update(int $id, array $data): ?Patient;
    
    public function delete(int $id): bool;
    
    public function getPatientsByUser(int $userId): Collection;
} 