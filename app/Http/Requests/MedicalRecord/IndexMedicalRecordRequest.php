<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;

class IndexMedicalRecordRequest extends FormRequest
{
    // Authorization skipped for demo - implement proper user/role checks in production
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:5|max:100',
            'patient_filter' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|string|in:Pending,Attending,Finalized,Needs Follow-up',
            'sort_by' => 'nullable|string|in:updated_at,patient_name,diagnosis,symptoms',
            'sort_direction' => 'nullable|string|in:asc,desc'
        ];
    }

    public function getFiltersWithDefaults(): array
    {
        $validated = $this->validated();
        
        return [
            'per_page' => $validated['per_page'] ?? 10,
            'patient_filter' => $validated['patient_filter'] ?? '',
            'date_from' => $validated['date_from'] ?? '',
            'date_to' => $validated['date_to'] ?? '',
            'status' => $validated['status'] ?? '',
            'sort_by' => $validated['sort_by'] ?? 'updated_at',
            'sort_direction' => $validated['sort_direction'] ?? 'desc',
        ];
    }
} 