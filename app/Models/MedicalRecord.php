<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'anamnesis_id',
        'symptoms',
        'diagnosis',
        'treatment',
        'notes',
    ];

    /**
     * Apply filters to the query.
     */
    public function scopeFilter($query, array $filters)
    {
        return (new \App\Filters\MedicalRecordFilter($query, $filters))->apply();
    }

    /**
     * Get the patient that the medical record belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
    
    /**
     * Get the anamnesis associated with this medical record.
     */
    public function anamnesis(): BelongsTo
    {
        return $this->belongsTo(Anamnesis::class);
    }
}
