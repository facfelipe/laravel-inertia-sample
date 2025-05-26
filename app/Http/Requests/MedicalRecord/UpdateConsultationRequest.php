<?php

namespace App\Http\Requests\MedicalRecord;

use App\Models\MedicalRecord;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationRequest extends FormRequest
{
    // Authorization skipped for demo - implement proper user/role checks in production
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:' . MedicalRecord::STATUS_FINALIZED . ',' . MedicalRecord::STATUS_NEEDS_FOLLOWUP
        ];
    }
} 