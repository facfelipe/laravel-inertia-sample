<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the anamneses for the patient.
     */
    public function anamneses(): HasMany
    {
        return $this->hasMany(Anamnesis::class);
    }

    /**
     * Get the medical records for the patient.
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
    
    /**
     * Get the patient's age based on birth date.
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date->age;
    }
} 