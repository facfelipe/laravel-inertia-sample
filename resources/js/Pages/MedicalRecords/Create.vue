<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import AnamnesisForm from './Partials/AnamnesisForm.vue';
import MedicalRecordForm from './Partials/MedicalRecordForm.vue';

const currentStep = ref(1);
const totalSteps = 2;

const formData = reactive({
  // Anamnesis data (Step 1)
  anamnesis: {
    patient_id: null,
    blood_pressure: '',
    temperature: '',
    heart_rate: '',
    respiratory_rate: '',
    weight: '',
    height: '',
  },
  
  // Medical record data (Step 2)
  medicalRecord: {
    patient_id: null,
    date: new Date().toISOString().substr(0, 10),
    symptoms: '',
    diagnosis: '',
    treatment: '',
    notes: '',
  }
});

// This function synchronizes patient_id between steps
const updatePatientId = (patientId) => {
  formData.anamnesis.patient_id = patientId;
  formData.medicalRecord.patient_id = patientId;
};

const goToNextStep = () => {
  if (currentStep.value < totalSteps) {
    currentStep.value++;
  }
};

const goToPreviousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
};

const submitForm = () => {
  // In a real implementation, this would send the entire formData object
  // to a single endpoint that would handle both anamnesis and medical record creation
  console.log('Submitting complete form data:', formData);
  
  // For now, just submit the medical record part
  router.post('/medical-records', formData.medicalRecord);
};
</script>

<template>
  <MainLayout title="Create Medical Record">
    <!-- Step indicator -->
    <div class="mb-8">
      <div class="flex justify-between">
        <div 
          v-for="step in totalSteps" 
          :key="step" 
          class="flex items-center"
        >
          <div 
            :class="[
              'w-8 h-8 rounded-full flex items-center justify-center', 
              currentStep >= step ? 'bg-blue-500 text-white' : 'bg-gray-200'
            ]"
          >
            {{ step }}
          </div>
          <div 
            v-if="step < totalSteps" 
            :class="[
              'h-1 w-16 mx-2', 
              currentStep > step ? 'bg-blue-500' : 'bg-gray-200'
            ]"
          ></div>
          <div 
            :class="[
              'ml-2 font-medium', 
              currentStep >= step ? 'text-gray-800' : 'text-gray-400'
            ]"
          >
            {{ step === 1 ? 'Anamnesis' : 'Medical Record' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Step content -->
    <div class="bg-white p-6 rounded border">
      <!-- Step 1: Anamnesis Form -->
      <div v-if="currentStep === 1">
        <h2 class="text-xl font-bold mb-4">Patient Pre-Consultation (Anamnesis)</h2>
        <p class="mb-4 text-gray-600">Fill in the initial patient assessment data before the doctor's consultation.</p>
        
        <AnamnesisForm 
          v-model:anamnesis="formData.anamnesis"
          @update:patient-id="updatePatientId"
        />
      </div>
      
      <!-- Step 2: Medical Record Form -->
      <div v-if="currentStep === 2">
        <h2 class="text-xl font-bold mb-4">Medical Record Details</h2>
        <p class="mb-4 text-gray-600">Enter the details from the doctor's consultation.</p>
        
        <MedicalRecordForm 
          v-model:medicalRecord="formData.medicalRecord"
          @update:patient-id="updatePatientId"
        />
      </div>
      
      <!-- Navigation buttons -->
      <div class="flex justify-between mt-6">
        <button 
          v-if="currentStep > 1" 
          @click="goToPreviousStep" 
          class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100"
        >
          Previous
        </button>
        <div v-else></div>
        
        <div>
          <button 
            v-if="currentStep < totalSteps" 
            @click="goToNextStep" 
            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
          >
            Next
          </button>
          <button 
            v-else 
            @click="submitForm" 
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
          >
            Submit
          </button>
        </div>
      </div>
    </div>
    
    <div class="mt-4">
      <Link href="/medical-records" class="text-blue-500 hover:underline">
        Cancel and return to Medical Records
      </Link>
    </div>
  </MainLayout>
</template> 