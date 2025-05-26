<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Filters\MedicalRecordFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class MedicalRecordQueryService
{
    public function getPaginatedRecords(array $filters): LengthAwarePaginator
    {
        return MedicalRecord::with(['patient'])
            ->select('medical_records.*')
            ->filter($filters)
            ->paginate($filters['per_page'])
            ->appends(request()->query());
    }

    public function getStatistics(): array
    {
        return Cache::remember('medical_records_stats', 300, function () {
            return [
                'total_records' => MedicalRecord::count(),
                'total_patients' => Patient::count(),
                'records_this_month' => MedicalRecord::whereMonth('created_at', now()->month)
                                                     ->whereYear('created_at', now()->year)
                                                     ->count(),
            ];
        });
    }
}