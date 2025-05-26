<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Both staff and doctors can view medical records
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        // Both staff and doctors can view medical records
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Both staff and doctors can create medical records
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        // Both staff and doctors can update medical records
        return true;
    }

    /**
     * Determine whether the user can update diagnosis and treatment fields.
     */
    public function updateDiagnosisAndTreatment(User $user, MedicalRecord $medicalRecord): bool
    {
        // Only doctors can update diagnosis and treatment
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can start a consultation.
     */
    public function startConsultation(User $user, MedicalRecord $medicalRecord): bool
    {
        // Only doctors can start consultations
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can finish/complete a consultation.
     */
    public function finishConsultation(User $user, MedicalRecord $medicalRecord): bool
    {
        // Only doctors can finish consultations
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        // Both staff and doctors can delete medical records
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalRecord $medicalRecord): bool
    {
        // Both staff and doctors can restore medical records
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalRecord $medicalRecord): bool
    {
        // Both staff and doctors can force delete medical records
        return true;
    }
}
