<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Filters\MedicalRecordFilter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MedicalRecordQueryService
{
    public function getPaginatedRecords(array $filters): LengthAwarePaginator
    {
        return MedicalRecord::with(['patient', 'statuses' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->select('medical_records.*')
            ->filter($filters)
            ->paginate($filters['per_page'])
            ->appends(request()->query());
    }

    public function getStatistics(): array
    {
        return Cache::remember('medical_records_stats', 300, function () {
            // Get status counts for records with status
            $statusCounts = MedicalRecord::join('statuses', 'medical_records.id', '=', 'statuses.model_id')
                ->where('statuses.model_type', MedicalRecord::class)
                ->whereIn('statuses.id', function ($query) {
                    $query->select(\DB::raw('MAX(id)'))
                        ->from('statuses')
                        ->where('model_type', MedicalRecord::class)
                        ->groupBy('model_id');
                })
                ->selectRaw('statuses.name, COUNT(*) as count')
                ->groupBy('statuses.name')
                ->pluck('count', 'name')
                ->toArray();

            // Count records without any status
            $recordsWithoutStatus = MedicalRecord::whereDoesntHave('statuses')->count();
            
            // Add records without status if any exist
            if ($recordsWithoutStatus > 0) {
                $statusCounts['No Status'] = $recordsWithoutStatus;
            }

            return [
                'total_records' => MedicalRecord::count(),
                'total_patients' => Patient::count(),
                'records_this_month' => MedicalRecord::whereMonth('created_at', now()->month)
                                                     ->whereYear('created_at', now()->year)
                                                     ->count(),
                'status_counts' => $statusCounts,
            ];
        });
    }
}