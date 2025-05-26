<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use App\Services\PatientService;
use App\Services\AnamnesisService;
use App\Http\Requests\MedicalRecord\StoreMedicalRecordRequest;
use App\Http\Requests\MedicalRecord\UpdateMedicalRecordRequest;
use App\Http\Requests\MedicalRecord\IndexMedicalRecordRequest;
use App\Http\Requests\MedicalRecord\UpdateConsultationRequest;
use App\Http\Requests\MedicalRecord\StartConsultationRequest;
use App\Services\MedicalRecordQueryService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
    use AuthorizesRequests;
    
    protected $medicalRecordService;
    protected $patientService;
    protected $anamnesisService;

    public function __construct(
        MedicalRecordService $medicalRecordService,
        PatientService $patientService,
        AnamnesisService $anamnesisService
    ) {
        $this->medicalRecordService = $medicalRecordService;
        $this->patientService = $patientService;
        $this->anamnesisService = $anamnesisService;
    }

    public function index(IndexMedicalRecordRequest $request)
    {
        $this->authorize('viewAny', MedicalRecord::class);
        
        $filters = $request->getFiltersWithDefaults();
        
        $medicalRecordService = app(MedicalRecordQueryService::class);
        
        $medicalRecords = $medicalRecordService->getPaginatedRecords($filters);
        $stats = $medicalRecordService->getStatistics();

        return Inertia::render('MedicalRecords/Index', [
            'medicalRecords' => $medicalRecords,
            'filters' => $filters,
            'stats' => $stats,
            'availableStatuses' => MedicalRecord::getStatuses(),
            'permissions' => [
                'canStartConsultation' => auth()->user() ? auth()->user()->isDoctor() : false,
            ]
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('create', MedicalRecord::class);
        
        $patients = $this->patientService->getAll();
        
        // Get step from query parameter, default to 1, validate range
        $step = (int) $request->query('step', 1);
        $step = max(1, min(3, $step)); // Ensure step is between 1 and 3
        
        return Inertia::render('MedicalForm', [
            'patients' => $patients,
            'initialStep' => $step
        ]);
    }

    public function store(StoreMedicalRecordRequest $request)
    {
        $this->authorize('create', MedicalRecord::class);
        
        try {
            $validatedData = $request->validated();
            
            // Create the medical record
            $medicalRecord = $this->medicalRecordService->create($validatedData);
            
            return redirect()->route('medical-records.index')
                ->with('success', 'Medical record created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create medical record. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        $medicalRecord = $this->medicalRecordService->getById($id);
        
        if (!$medicalRecord) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Medical record not found');
        }
        
        $this->authorize('view', $medicalRecord);
        
        $patient = $this->patientService->getById($medicalRecord->patient_id);
        
        $anamnesis = null;
        if ($medicalRecord->anamnesis_id) {
            $anamnesis = $this->anamnesisService->getById($medicalRecord->anamnesis_id);
        }
        
        return Inertia::render('MedicalRecords/Show', [
            'record' => $medicalRecord,
            'patient' => $patient,
            'anamnesis' => $anamnesis,
            'id' => $id,
            'permissions' => [
                'canStartConsultation' => auth()->user() ? auth()->user()->can('startConsultation', $medicalRecord) : false,
                'canFinishConsultation' => auth()->user() ? auth()->user()->can('finishConsultation', $medicalRecord) : false,
                'canUpdateDiagnosisAndTreatment' => auth()->user() ? auth()->user()->can('updateDiagnosisAndTreatment', $medicalRecord) : false,
            ]
        ]);
    }

    public function edit($id)
    {
        $medicalRecord = $this->medicalRecordService->getById($id);
        
        if (!$medicalRecord) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Medical record not found');
        }
        
        $this->authorize('update', $medicalRecord);
        
        $patient = $this->patientService->getById($medicalRecord->patient_id);
        $anamnesis = null;
        
        if ($medicalRecord->anamnesis_id) {
            $anamnesis = $this->anamnesisService->getById($medicalRecord->anamnesis_id);
        }
        
        return Inertia::render('MedicalRecords/Edit', [
            'record' => $medicalRecord,
            'patient' => $patient,
            'anamnesis' => $anamnesis,
            'permissions' => [
                'canUpdateDiagnosisAndTreatment' => auth()->user() ? auth()->user()->can('updateDiagnosisAndTreatment', $medicalRecord) : false,
            ]
        ]);
    }

    public function update(UpdateMedicalRecordRequest $request, $id)
    {
        try {
            $medicalRecord = $this->medicalRecordService->getById($id);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            $this->authorize('update', $medicalRecord);
            
            $validatedData = $request->validated();
            
            // Check if user is trying to update diagnosis/treatment and has permission
            if (isset($validatedData['diagnosis']) || isset($validatedData['treatment'])) {
                $this->authorize('updateDiagnosisAndTreatment', $medicalRecord);
            }
            
            $medicalRecord = $this->medicalRecordService->update($id, $validatedData);
            
            return redirect()->route('medical-records.show', $id)
                ->with('success', 'Medical record updated successfully');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return back()->withErrors([
                'error' => 'You do not have permission to update diagnosis and treatment fields.'
            ])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update medical record: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $medicalRecord = $this->medicalRecordService->getById($id);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            $this->authorize('delete', $medicalRecord);
            
            $success = $this->medicalRecordService->delete($id);
            
            return redirect()->route('medical-records.index')
                ->with('success', 'Medical record deleted successfully');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('medical-records.index')
                ->with('error', 'You do not have permission to delete this medical record.');
        } catch (\Exception $e) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Failed to delete medical record: ' . $e->getMessage());
        }
    }

    /**
     * Start consultation - change status to Attending
     */
    public function startConsultation(StartConsultationRequest $request, $id)
    {
        try {
            $medicalRecord = $this->medicalRecordService->getById($id);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            $this->authorize('startConsultation', $medicalRecord);
            
            // Change status to Attending
            $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);
            
            return redirect()->route('medical-records.consultation', $id)
                ->with('success', 'Consultation started successfully');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Only doctors can start consultations.');
        } catch (\Exception $e) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Failed to start consultation: ' . $e->getMessage());
        }
    }

    /**
     * Show consultation page
     */
    public function consultation($id)
    {
        $medicalRecord = $this->medicalRecordService->getById($id);
        
        if (!$medicalRecord) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Medical record not found');
        }
        
        $this->authorize('view', $medicalRecord);
        
        $patient = $this->patientService->getById($medicalRecord->patient_id);
        
        $anamnesis = null;
        if ($medicalRecord->anamnesis_id) {
            $anamnesis = $this->anamnesisService->getById($medicalRecord->anamnesis_id);
        }

        // Get status history
        $statusHistory = $medicalRecord->statuses()
            ->orderBy('created_at', 'desc')
            ->get();
        
        return Inertia::render('MedicalRecords/Consultation', [
            'record' => $medicalRecord,
            'patient' => $patient,
            'anamnesis' => $anamnesis,
            'statusHistory' => $statusHistory,
            'availableStatuses' => [
                MedicalRecord::STATUS_FINALIZED,
                MedicalRecord::STATUS_NEEDS_FOLLOWUP
            ],
            'permissions' => [
                'canFinishConsultation' => auth()->user() ? auth()->user()->can('finishConsultation', $medicalRecord) : false,
            ]
        ]);
    }

    /**
     * Update consultation and status
     */
    public function updateConsultation(UpdateConsultationRequest $request, $id)
    {
        try {
            $medicalRecord = $this->medicalRecordService->getById($id);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            $this->authorize('finishConsultation', $medicalRecord);
            
            $validatedData = $request->validated();
            
            // Update the medical record
            $medicalRecord->update([
                'diagnosis' => $validatedData['diagnosis'],
                'treatment' => $validatedData['treatment'],
                'notes' => $validatedData['notes']
            ]);
            
            // Update status
            $medicalRecord->setStatus($validatedData['status']);
            
            return redirect()->route('medical-records.show', $id)
                ->with('success', 'Consultation completed successfully');
                
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()
                ->with('error', 'Only doctors can complete consultations.')
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to complete consultation: ' . $e->getMessage())
                ->withInput();
        }
    }
}