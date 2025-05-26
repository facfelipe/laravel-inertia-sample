<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\ModelStatus\HasStatuses;

class MedicalRecord extends Model
{
    use HasFactory, HasStatuses;

    const STATUS_PENDING = 'Pending';
    const STATUS_ATTENDING = 'Attending';
    const STATUS_FINALIZED = 'Finalized';
    const STATUS_NEEDS_FOLLOWUP = 'Needs Follow-up';

    protected $fillable = [
        'patient_id',
        'anamnesis_id',
        'symptoms',
        'diagnosis',
        'treatment',
        'notes',
    ];

    /**
     * Get all available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ATTENDING,
            self::STATUS_FINALIZED,
            self::STATUS_NEEDS_FOLLOWUP,
        ];
    }

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
