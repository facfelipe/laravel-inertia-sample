<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Repositories\MedicalRecordRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicalRecordService
{
    protected $medicalRecordRepository;

    public function __construct(MedicalRecordRepository $medicalRecordRepository)
    {
        $this->medicalRecordRepository = $medicalRecordRepository;
    }

    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return $this->medicalRecordRepository->getAll($perPage);
    }

    public function getAllRecords(): Collection
    {
        return MedicalRecord::all();
    }

    public function getAllWithPatients()
    {
        return $this->medicalRecordRepository->getAllWithPatients();
    }

    public function getById($id)
    {
        return MedicalRecord::with(['statuses' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);
    }

    public function getByPatientId($patientId)
    {
        return $this->medicalRecordRepository->getByPatientId($patientId);
    }

    public function create(array $data)
    {
        return $this->medicalRecordRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->medicalRecordRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->medicalRecordRepository->delete($id);
    }
}