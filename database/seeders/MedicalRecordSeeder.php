<?php

namespace Database\Seeders;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MedicalRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = Patient::all();
        if ($patients->isEmpty()) {
            return;
        }
        
        // Sample medical record data
        $medicalRecords = [
            // Patient 1 - 4 records
            [
                'patient_index' => 0,
                'date' => '2023-01-15',
                'symptoms' => 'Headache, dizziness, elevated blood pressure (150/95)',
                'diagnosis' => 'Hypertension',
                'treatment' => 'Lisinopril 10mg once daily',
                'notes' => 'Follow-up in 2 weeks to monitor blood pressure',
            ],
            [
                'patient_index' => 0,
                'date' => '2023-02-01',
                'symptoms' => 'Follow-up visit, blood pressure reduced (135/85)',
                'diagnosis' => 'Improving hypertension',
                'treatment' => 'Continue Lisinopril 10mg once daily',
                'notes' => 'Continue medication and monitor diet',
            ],
            [
                'patient_index' => 0,
                'date' => '2023-04-10',
                'symptoms' => 'Mild cough, sore throat, runny nose',
                'diagnosis' => 'Common cold',
                'treatment' => 'Rest, fluids, over-the-counter cold medicine',
                'notes' => 'Not related to hypertension treatment',
            ],
            [
                'patient_index' => 0,
                'date' => '2023-06-20',
                'symptoms' => 'Routine check-up, blood pressure stable (130/80)',
                'diagnosis' => 'Controlled hypertension',
                'treatment' => 'Continue current medication',
                'notes' => 'Next follow-up in 3 months',
            ],
            
            // Patient 2 - 3 records
            [
                'patient_index' => 1,
                'date' => '2023-02-05',
                'symptoms' => 'Skin rash, itching after taking amoxicillin',
                'diagnosis' => 'Antibiotic allergy reaction',
                'treatment' => 'Discontinue amoxicillin, take Benadryl for itching',
                'notes' => 'Update allergy information to include all penicillin derivatives',
            ],
            [
                'patient_index' => 1,
                'date' => '2023-03-22',
                'symptoms' => 'Fever, severe sore throat, white patches on tonsils',
                'diagnosis' => 'Strep throat',
                'treatment' => 'Azithromycin 500mg first day, then 250mg for 4 days',
                'notes' => 'Alternative antibiotic due to penicillin allergy',
            ],
            [
                'patient_index' => 1,
                'date' => '2023-05-18',
                'symptoms' => 'Annual physical examination, no complaints',
                'diagnosis' => 'Healthy, no acute issues',
                'treatment' => 'None required',
                'notes' => 'Recommended routine health screenings',
            ],
            
            // Patient 3 - 3 records
            [
                'patient_index' => 2,
                'date' => '2023-01-20',
                'symptoms' => 'Polyuria, polydipsia, fatigue, blood glucose level 235 mg/dL',
                'diagnosis' => 'Type 2 Diabetes, poorly controlled',
                'treatment' => 'Metformin 500mg twice daily, blood glucose monitoring',
                'notes' => 'Dietary counseling provided, follow-up in 2 weeks',
            ],
            [
                'patient_index' => 2,
                'date' => '2023-02-05',
                'symptoms' => 'Follow-up visit, blood glucose improved (150-180 range)',
                'diagnosis' => 'Type 2 Diabetes, improving control',
                'treatment' => 'Continue current medication',
                'notes' => 'Patient adhering to dietary recommendations',
            ],
            [
                'patient_index' => 2,
                'date' => '2023-04-30',
                'symptoms' => 'Numbness and tingling in feet',
                'diagnosis' => 'Early diabetic neuropathy',
                'treatment' => 'Maintain tight glucose control, Vitamin B complex',
                'notes' => 'Referred to podiatrist for evaluation',
            ],
            
            // Patient 4 - 3 records
            [
                'patient_index' => 3,
                'date' => '2023-01-05',
                'symptoms' => 'Missed period, nausea, fatigue',
                'diagnosis' => 'Pregnancy (8 weeks)',
                'treatment' => 'Prenatal vitamins',
                'notes' => 'First prenatal visit scheduled, ultrasound confirmed viable pregnancy',
            ],
            [
                'patient_index' => 3,
                'date' => '2023-03-10',
                'symptoms' => 'Routine prenatal check-up at 18 weeks',
                'diagnosis' => 'Normal pregnancy progression',
                'treatment' => 'Continue prenatal vitamins',
                'notes' => 'Ultrasound showed healthy development, gender revealed: female',
            ],
            [
                'patient_index' => 3,
                'date' => '2023-05-15',
                'symptoms' => 'Mild swelling in ankles, back pain',
                'diagnosis' => 'Normal pregnancy symptoms at 28 weeks',
                'treatment' => 'Rest, supportive belt for back pain',
                'notes' => 'All tests normal, baby growing as expected',
            ],
            
            // Patient 5 - 2 records
            [
                'patient_index' => 4,
                'date' => '2023-02-20',
                'symptoms' => 'Chest pain upon exertion, shortness of breath',
                'diagnosis' => 'Coronary artery disease, stable angina',
                'treatment' => 'Nitroglycerin as needed, Atorvastatin 20mg daily',
                'notes' => 'Referred to cardiologist, stress test scheduled',
            ],
            [
                'patient_index' => 4,
                'date' => '2023-04-05',
                'symptoms' => 'Follow-up after cardiology consultation',
                'diagnosis' => 'Confirmed triple vessel coronary artery disease',
                'treatment' => 'Added Metoprolol 25mg twice daily, continue other medications',
                'notes' => 'Discussing treatment options including possible surgery',
            ],
        ];
        
        foreach ($medicalRecords as $recordData) {
            $patientIndex = $recordData['patient_index'];
            
            if (isset($patients[$patientIndex])) {
                MedicalRecord::create([
                    'patient_id' => $patients[$patientIndex]->id,
                    'date' => $recordData['date'],
                    'symptoms' => $recordData['symptoms'],
                    'diagnosis' => $recordData['diagnosis'],
                    'treatment' => $recordData['treatment'],
                    'notes' => $recordData['notes'],
                ]);
            }
        }
    }
}
