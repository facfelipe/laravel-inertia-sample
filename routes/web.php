<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Web\MedicalRecordController;
use App\Http\Controllers\Web\PatientController;
use App\Http\Controllers\Web\AnamnesisController;
use App\Services\MedicalRecordService;

// NOTE: Authentication is skipped for demo purposes
// In production, wrap these routes with: Route::middleware('auth')->group(function () {

Route::get('/', function () {
    $medicalRecordService = app(MedicalRecordService::class);
    $medicalRecords = $medicalRecordService->getAllWithPatients();
    
    return Inertia::render('Home', [
        'medicalRecords' => $medicalRecords,
        'title' => 'Dashboard'
    ]);
})->name('home');

// Medical records routes - Auth required in production
Route::get('medical-form', [MedicalRecordController::class, 'create'])->name('medical-form');
Route::post('medical-records', [MedicalRecordController::class, 'store'])->name('medical-records.store');
Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
Route::get('medical-records/{id}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
Route::get('medical-records/{id}/edit', [MedicalRecordController::class, 'edit'])->name('medical-records.edit');
Route::put('medical-records/{id}', [MedicalRecordController::class, 'update'])->name('medical-records.update');
Route::delete('medical-records/{id}', [MedicalRecordController::class, 'destroy'])->name('medical-records.destroy');

// Consultation routes
Route::post('medical-records/{id}/start-consultation', [MedicalRecordController::class, 'startConsultation'])->name('medical-records.start-consultation');
Route::get('medical-records/{id}/consultation', [MedicalRecordController::class, 'consultation'])->name('medical-records.consultation');
Route::put('medical-records/{id}/consultation', [MedicalRecordController::class, 'updateConsultation'])->name('medical-records.update-consultation');

// Patient routes - Auth required in production
Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
Route::get('patients/create', [PatientController::class, 'create'])->name('patients.create');
Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
Route::get('patients/{id}', [PatientController::class, 'show'])->name('patients.show');
Route::get('patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
Route::put('patients/{id}', [PatientController::class, 'update'])->name('patients.update');
Route::delete('patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

// Anamnesis routes - Auth required in production
Route::post('patients/{patientId}/anamneses', [AnamnesisController::class, 'store'])->name('anamneses.store');

// }); // End of auth middleware group for production
