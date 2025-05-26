<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class MedicalRecordFilter
{
    protected $builder;
    protected $filters;

    public function __construct(Builder $builder, array $filters)
    {
        $this->builder = $builder;
        $this->filters = $filters;
    }

    /**
     * Apply all filters to the query builder.
     */
    public function apply(): Builder
    {
        foreach ($this->filters as $name => $value) {
            if (method_exists($this, $name) && !empty($value)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }

    /**
     * Filter by patient name (case insensitive).
     */
    public function patient_filter($value): void
    {
        $this->builder->whereHas('patient', function ($query) use ($value) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($value) . '%']);
        });
    }

    /**
     * Filter by date from.
     */
    public function date_from($value): void
    {
        $this->builder->whereDate('updated_at', '>=', $value);
    }

    /**
     * Filter by date to.
     */
    public function date_to($value): void
    {
        $this->builder->whereDate('updated_at', '<=', $value);
    }

    /**
     * Apply sorting to the query.
     */
    public function sort_by($value): void
    {
        $direction = $this->filters['sort_direction'] ?? 'desc';
        
        if ($value === 'patient_name') {
            $this->builder->leftJoin('patients', 'medical_records.patient_id', '=', 'patients.id')
                          ->orderBy('patients.name', $direction);
        } else {
            $this->builder->orderBy($value, $direction);
        }
    }
} 