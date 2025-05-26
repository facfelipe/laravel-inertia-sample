<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalRecordRequest extends FormRequest
{
    // Authorization skipped for demo - implement proper user/role checks in production
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'symptoms' => 'required|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'treatment' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ];
        
        // Only require patient_id when not in a nested route
        if (!$this->route('patient')) {
            $rules['patient_id'] = 'required|exists:patients,id';
        }
        
        return $rules;
    }
}
