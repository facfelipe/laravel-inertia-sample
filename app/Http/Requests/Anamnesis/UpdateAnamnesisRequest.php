<?php

namespace App\Http\Requests\Anamnesis;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnamnesisRequest extends FormRequest
{
    // Authorization skipped for demo - implement proper user/role checks in production
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'blood_pressure' => 'nullable|string|max:20',
            'temperature' => 'nullable|numeric|between:30,45',
            'heart_rate' => 'nullable|integer|between:30,250',
            'respiratory_rate' => 'nullable|integer|between:5,60',
            'weight' => 'nullable|numeric|between:0,500',
            'height' => 'nullable|numeric|between:0,300',
        ];
    }
} 