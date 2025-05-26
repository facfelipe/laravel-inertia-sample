<?php

namespace App\Services;

use App\Models\Anamnesis;
use App\Repositories\AnamnesisRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnamnesisService
{
    protected $anamnesisRepository;

    public function __construct(AnamnesisRepository $anamnesisRepository)
    {
        $this->anamnesisRepository = $anamnesisRepository;
    }

    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->anamnesisRepository->getAll($perPage);
    }

    public function getById(int $id): ?Anamnesis
    {
        return $this->anamnesisRepository->getById($id);
    }

    public function getAnamnesesByPatient(int $patientId): Collection
    {
        return $this->anamnesisRepository->getByPatientId($patientId);
    }

    public function create(array $data): Anamnesis
    {
        return $this->anamnesisRepository->create($data);
    }

    public function update(int $id, array $data): Anamnesis
    {
        $this->anamnesisRepository->update($id, $data);
        return $this->anamnesisRepository->getById($id);
    }

    public function getLatestByPatientId(int $patientId): ?Anamnesis
    {
        return $this->anamnesisRepository->getLatestByPatientId($patientId);
    }

    public function upsert(array $data): Anamnesis
    {
        return $this->anamnesisRepository->upsert($data);
    }

    public function delete(int $id): bool
    {
        return $this->anamnesisRepository->delete($id);
    }
}