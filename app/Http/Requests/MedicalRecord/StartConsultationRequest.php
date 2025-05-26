<?php

namespace App\Http\Requests\MedicalRecord;

use Illuminate\Foundation\Http\FormRequest;

class StartConsultationRequest extends FormRequest
{
    // Authorization skipped for demo - implement proper user/role checks in production
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // No validation rules needed for starting consultation
        // This request class is mainly for authorization and consistency
        return [];
    }
} 