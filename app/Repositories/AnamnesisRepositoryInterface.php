<?php

namespace App\Repositories;

use App\Models\Anamnesis;
use Illuminate\Pagination\LengthAwarePaginator;

interface AnamnesisRepositoryInterface
{
    public function getAll(int $perPage = 10): LengthAwarePaginator;
    public function getById(int $id): ?Anamnesis;
    public function getByPatientId(int $patientId): ?Anamnesis;
    public function getLatestByPatientId(int $patientId): ?Anamnesis;
    public function create(array $data): Anamnesis;
    public function update(int $id, array $data): bool;
    public function upsert(array $data): Anamnesis;
    public function delete(int $id): bool;
} 