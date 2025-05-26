<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    protected $patientService;

    public function __construct(PatientService $patientService)
    {
        $this->patientService = $patientService;
    }

    public function index()
    {
        $patients = $this->patientService->getAll();
        
        return Inertia::render('Patients/Index', [
            'patients' => $patients
        ]);
    }

    public function create()
    {
        return Inertia::render('Patients/Create');
    }

    public function show($id)
    {
        $patient = $this->patientService->getById($id);
        
        if (!$patient) {
            return redirect()->route('patients.index')
                ->with('error', 'Patient not found');
        }
        
        return Inertia::render('Patients/Show', [
            'patient' => $patient
        ]);
    }
    
    public function edit($id)
    {
        $patient = $this->patientService->getById($id);
        
        if (!$patient) {
            return redirect()->route('patients.index')
                ->with('error', 'Patient not found');
        }
        
        return Inertia::render('Patients/Edit', [
            'patient' => $patient
        ]);
    }
    
    public function update(StorePatientRequest $request, $id)
    {
        try {
            $patient = $this->patientService->updatePatient($id, $request->validated());
            
            if (!$patient) {
                return redirect()->route('patients.index')
                    ->with('error', 'Patient not found');
            }
            
            return redirect()->route('patients.show', $id)
                ->with('success', 'Patient updated successfully');
                
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to update patient: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    public function store(StorePatientRequest $request)
    {
        try {
            $patient = $this->patientService->create($request->validated());
            
            if ($request->header('X-Inertia')) {
                return back()->with([
                    'success' => 'Patient created successfully',
                    'patient' => $patient
                ]);
            }
            
            return redirect()->route('patients.index')
                ->with('success', 'Patient created successfully');
                
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Failed to create patient: ' . $e->getMessage()
            ])->withInput();
        }
    }
    
    public function destroy($id)
    {
        try {
            $success = $this->patientService->deletePatient($id);
            
            if (!$success) {
                return redirect()->route('patients.index')
                    ->with('error', 'Patient not found');
            }
            
            return redirect()->route('patients.index')
                ->with('success', 'Patient deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->route('patients.index')
                ->with('error', 'Failed to delete patient: ' . $e->getMessage());
        }
    }
} 