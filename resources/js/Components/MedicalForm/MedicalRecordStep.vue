<template>
  <div>
    <!-- Patient info header card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 rounded-lg p-5 mb-8 shadow-sm">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-indigo-100 rounded-full p-2">
          <svg class="h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <div class="ml-4">
          <h3 class="text-lg font-medium text-indigo-800">Patient Information</h3>
          <p class="text-indigo-600 font-medium">
            Creating medical record for: <span class="text-indigo-900 font-bold">{{ patientName }}</span>
          </p>
        </div>
      </div>
    </div>
    
    <!-- Quick Fill Button -->
    <div class="mb-6 flex justify-end">
      <button 
        @click="fillSampleData" 
        type="button"
        class="inline-flex items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Fill with Sample Data
      </button>
    </div>
    
    <form class="space-y-8">
      <!-- Symptoms Section -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
          <h3 class="text-base font-semibold text-gray-900 flex items-center">
            <svg class="h-5 w-5 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Symptoms
          </h3>
        </div>
        <div class="px-4 py-4">
          <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-1">Patient Symptoms</label>
          <textarea 
            id="symptoms" 
            v-model="medicalRecordData.symptoms" 
            rows="3" 
            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md shadow-sm transition-colors duration-200"
            :class="{ 'border-red-300 ring-red-300 focus:ring-red-500 focus:border-red-500': validationErrors.symptoms }"
            placeholder="Describe the patient's symptoms in detail"
          ></textarea>
          <p v-if="validationErrors.symptoms" class="mt-2 text-sm text-red-600">{{ validationErrors.symptoms }}</p>
        </div>
      </div>
      
      <!-- Additional Notes Section -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
          <h3 class="text-base font-semibold text-gray-900 flex items-center">
            <svg class="h-5 w-5 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Additional Notes
          </h3>
        </div>
        <div class="px-4 py-4">
          <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
          <textarea 
            id="notes" 
            v-model="medicalRecordData.notes" 
            rows="3" 
            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md shadow-sm transition-colors duration-200"
            :class="{ 'border-red-300 ring-red-300 focus:ring-red-500 focus:border-red-500': validationErrors.notes }"
            placeholder="Additional notes, observations, or follow-up instructions"
          ></textarea>
          <p v-if="validationErrors.notes" class="mt-2 text-sm text-red-600">{{ validationErrors.notes }}</p>
        </div>
      </div>
    </form>
    
    <div class="flex justify-center items-center mt-8 gap-4">
      <button 
        @click="prevStep" 
        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Previous Step
      </button>
      
      <button 
        @click="submitForm" 
        class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-md min-w-[200px]"
        :disabled="isSubmitting"
      >
        <span v-if="isSubmitting" class="flex items-center">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Submitting...
        </span>
        <span v-else class="flex items-center">
          <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Submit Medical Record
        </span>
      </button>
    </div>
    
    <!-- Success modal -->
    <div v-if="formStatus.success" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50">
      <div class="bg-white p-8 rounded-lg max-w-md w-full transform transition-all animate-fade-scale-in shadow-xl">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
            <svg class="h-10 w-10 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-lg font-bold text-gray-900 mt-5">Success!</h3>
          <p class="text-sm text-gray-500 mt-2">The medical record has been created successfully.</p>
          
          <div class="mt-6 flex justify-center space-x-3">
            <button 
              @click="startNewRecord" 
              class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
              <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Create Another Record
            </button>
            <button 
              @click="viewAllRecords" 
              class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
              <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              View All Records
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'
import { validateMedicalRecord } from '@/Utils/validation'
import { router, usePage } from '@inertiajs/vue3'

const store = useMedicalFormStore()
const { props: pageProps } = usePage()
const isSubmitting = ref(false)
const validationErrors = ref({})

// Computed properties from store
const patientId = computed(() => store.patientId)
const patientName = computed(() => store.patientName)
const medicalRecordData = computed(() => store.medicalRecordData)
const formStatus = computed(() => store.formStatus)

// Go back to previous step
function prevStep() {
  store.goToPrevStep()
}

// Update medical record data in store
function updateMedicalRecordField(field, value) {
  const update = { [field]: value }
  store.updateMedicalRecordData(update)
}

// Validate and submit the form
async function submitForm() {
  try {
    validationErrors.value = {}
    store.setError(null)
    
    // Form validation
    const { isValid, errors } = validateMedicalRecord(medicalRecordData.value)
    
    if (!isValid) {
      validationErrors.value = errors
      return
    }
    
    // Submit medical record data using Inertia
    isSubmitting.value = true
    store.setSubmitting(true)
    
    // Prepare the data for submission
    const submissionData = {
      ...medicalRecordData.value,
      patient_id: patientId.value
    }
    
    // Add anamnesis_id if there's an anamnesis for this patient
    const anamnesis = store.anamnesisData
    if (anamnesis && Object.keys(anamnesis).length > 0) {
      // If anamnesis data exists, we need to get the anamnesis ID from the flash data
      const flashAnamnesis = pageProps.value?.flash?.anamnesis
      if (flashAnamnesis?.id) {
        submissionData.anamnesis_id = flashAnamnesis.id
      }
    }
    
    router.post('/medical-records', submissionData, {
      onSuccess: () => {
        store.setSuccess(true)
        // Clear localStorage after successful submission
        localStorage.removeItem('medicalFormState')
      },
      onError: (errors) => {
        validationErrors.value = errors
        store.setError('There was an error creating the medical record. Please check the form and try again.')
      },
      onFinish: () => {
        isSubmitting.value = false
        store.setSubmitting(false)
      }
    })
    
  } catch (error) {
    console.error('Error in medical record step:', error)
    store.setSuccess(false)
    store.setError('There was an error creating the medical record. Please try again.')
    
    // Handle validation errors from the server
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors
    }
    
    isSubmitting.value = false
    store.setSubmitting(false)
  }
}

// Start a new medical record
function startNewRecord() {
  // Reset the entire form, not just the medical record portion
  store.resetForm()
  
  // Reset success state
  store.setSuccess(false)
  validationErrors.value = {}
  
  // Remove form data from localStorage to ensure a clean state
  localStorage.removeItem('medicalFormState');
}

// View all medical records for this patient
function viewAllRecords() {
  // Clear form state before navigating
  localStorage.removeItem('medicalFormState');
  router.visit(`/medical-records`)
}

// Fill with sample data
function fillSampleData() {
  const sampleData = {
    symptoms: 'Patient reports mild headache and fatigue lasting 2 days. No fever or nausea.',
    notes: 'Patient appears well. Vital signs stable. Advised on stress management techniques and importance of adequate hydration.'
  }
  
  store.updateMedicalRecordData(sampleData)
  
  // Clear any validation errors
  validationErrors.value = {}
}
</script>

<style scoped>
.animate-fade-scale-in {
  animation: fadeScaleIn 0.3s ease-out forwards;
}

@keyframes fadeScaleIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style> 