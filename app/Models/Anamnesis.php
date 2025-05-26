<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anamnesis extends Model
{
    use HasFactory;

    protected $table = 'anamneses';

    protected $fillable = [
        'patient_id',
        'blood_pressure',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'weight',
        'height',
    ];

    protected $casts = [
        'temperature' => 'float',
        'weight' => 'float',
        'height' => 'float',
        'heart_rate' => 'integer',
        'respiratory_rate' => 'integer',
    ];

    /**
     * Get the patient that the anamnesis belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the BMI (Body Mass Index) calculated from height and weight.
     */
    public function getBmiAttribute(): ?float
    {
        if (!$this->height || !$this->weight) {
            return null;
        }

        // BMI = weight(kg) / (height(m))²
        $heightInMeters = $this->height / 100; // convert cm to m
        return round($this->weight / ($heightInMeters * $heightInMeters), 1);
    }
}
