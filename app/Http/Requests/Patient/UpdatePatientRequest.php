<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    // Authorization skipped for demo - implement proper user/role checks in production
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('patients')->ignore($this->route('patient'))],
            'phone' => 'sometimes|nullable|string|max:20',
            'birth_date' => 'sometimes|date',
            'address' => 'sometimes|nullable|string|max:255',
        ];
    }
}