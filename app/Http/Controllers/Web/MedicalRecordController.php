<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use App\Services\PatientService;
use App\Services\AnamnesisService;
use App\Http\Requests\MedicalRecord\StoreMedicalRecordRequest;
use App\Http\Requests\MedicalRecord\IndexMedicalRecordRequest;
use App\Services\MedicalRecordQueryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalRecordController extends Controller
{
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
        $filters = $request->getFiltersWithDefaults();
        
        $medicalRecordService = app(MedicalRecordQueryService::class);
        
        $medicalRecords = $medicalRecordService->getPaginatedRecords($filters);
        $stats = $medicalRecordService->getStatistics();

        return Inertia::render('MedicalRecords/Index', [
            'medicalRecords' => $medicalRecords,
            'filters' => $filters,
            'stats' => $stats,
            'availableStatuses' => MedicalRecord::getStatuses()
        ]);
    }

    public function create(Request $request)
    {
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
        
        $patient = $this->patientService->getById($medicalRecord->patient_id);
        
        $anamnesis = null;
        if ($medicalRecord->anamnesis_id) {
            $anamnesis = $this->anamnesisService->getById($medicalRecord->anamnesis_id);
        }
        
        return Inertia::render('MedicalRecords/Show', [
            'record' => $medicalRecord,
            'patient' => $patient,
            'anamnesis' => $anamnesis,
            'id' => $id
        ]);
    }

    public function edit($id)
    {
        $medicalRecord = $this->medicalRecordService->getById($id);
        
        if (!$medicalRecord) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Medical record not found');
        }
        
        $patient = $this->patientService->getById($medicalRecord->patient_id);
        $anamnesis = null;
        
        if ($medicalRecord->anamnesis_id) {
            $anamnesis = $this->anamnesisService->getById($medicalRecord->anamnesis_id);
        }
        
        return Inertia::render('MedicalRecords/Edit', [
            'record' => $medicalRecord,
            'patient' => $patient,
            'anamnesis' => $anamnesis
        ]);
    }

    public function update(StoreMedicalRecordRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $medicalRecord = $this->medicalRecordService->update($id, $validatedData);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            return redirect()->route('medical-records.show', $id)
                ->with('success', 'Medical record updated successfully');
                
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update medical record: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $success = $this->medicalRecordService->delete($id);
            
            if (!$success) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            return redirect()->route('medical-records.index')
                ->with('success', 'Medical record deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->route('medical-records.index')
                ->with('error', 'Failed to delete medical record: ' . $e->getMessage());
        }
    }

    /**
     * Start consultation - change status to Attending
     */
    public function startConsultation($id)
    {
        try {
            $medicalRecord = $this->medicalRecordService->getById($id);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            // Change status to Attending
            $medicalRecord->setStatus(MedicalRecord::STATUS_ATTENDING);
            
            return redirect()->route('medical-records.consultation', $id)
                ->with('success', 'Consultation started successfully');
                
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
            ]
        ]);
    }

    /**
     * Update consultation and status
     */
    public function updateConsultation(Request $request, $id)
    {
        $request->validate([
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:' . MedicalRecord::STATUS_FINALIZED . ',' . MedicalRecord::STATUS_NEEDS_FOLLOWUP
        ]);

        try {
            $medicalRecord = $this->medicalRecordService->getById($id);
            
            if (!$medicalRecord) {
                return redirect()->route('medical-records.index')
                    ->with('error', 'Medical record not found');
            }
            
            // Update the medical record
            $medicalRecord->update([
                'diagnosis' => $request->diagnosis,
                'treatment' => $request->treatment,
                'notes' => $request->notes
            ]);
            
            // Update status
            $medicalRecord->setStatus($request->status);
            
            return redirect()->route('medical-records.show', $id)
                ->with('success', 'Consultation completed successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to complete consultation: ' . $e->getMessage())
                ->withInput();
        }
    }
}