<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Anamnesis\StoreAnamnesisRequest;
use App\Services\AnamnesisService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnamnesisController extends Controller
{
    protected AnamnesisService $anamnesisService;

    public function __construct(AnamnesisService $anamnesisService)
    {
        $this->anamnesisService = $anamnesisService;
    }

    public function store(Request $request, $patientId, StoreAnamnesisRequest $storeRequest)
    {
        try {
            $validatedData = $storeRequest->validated();
            $validatedData['patient_id'] = (int) $patientId;

            // Use upsert to create or update existing anamnesis
            $anamnesis = $this->anamnesisService->upsert($validatedData);

            return redirect()->back()->with([
                'success' => 'Anamnesis saved successfully.',
                'anamnesis' => $anamnesis
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'error' => 'Failed to save anamnesis. Please try again.'
            ]);
        }
    }
} 