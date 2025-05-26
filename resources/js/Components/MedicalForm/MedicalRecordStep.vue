<template>
  <div 
    role="main" 
    aria-labelledby="medical-record-step-title"
    @keydown="handleGlobalKeydown"
  >
    <!-- Patient info header card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-500 rounded-lg p-5 mb-8 shadow-sm" role="banner" aria-labelledby="patient-banner">
      <div class="flex items-center">
        <div class="flex-shrink-0 bg-indigo-100 rounded-full p-2">
          <svg class="h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <div class="ml-4">
          <h2 id="medical-record-step-title" class="text-lg font-medium text-indigo-800" tabindex="-1" ref="stepTitleRef">
            Medical Record Information
          </h2>
          <p id="patient-banner" class="text-indigo-600 font-medium">
            Creating medical record for: <span class="text-indigo-900 font-bold">{{ patientName }}</span>
          </p>
        </div>
      </div>
    </div>
    
    <!-- Keyboard shortcuts help -->
    <div class="sr-only" aria-live="polite" id="record-keyboard-help">
      Press Tab to navigate fields, S to fill sample data, Alt+P for previous step, Alt+S to submit, Ctrl+Enter to submit form
    </div>
    
    <!-- Quick Fill Button -->
    <div class="mb-6 flex justify-end">
      <button 
        @click="fillSampleData" 
        @keydown.s.prevent="fillSampleData"
        type="button"
        class="inline-flex items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
        aria-describedby="sample-data-help"
        ref="sampleDataButton"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Fill with Sample Data
        <span class="sr-only">(Press S)</span>
      </button>
      <div id="sample-data-help" class="sr-only">
        Click to fill the form with sample medical record data for testing purposes
      </div>
    </div>
    
    <form class="space-y-8" role="form" aria-labelledby="medical-record-form-title" @keydown="handleFormKeydown">
      <h3 id="medical-record-form-title" class="sr-only">Medical Record Information Form</h3>
      
      <!-- Symptoms Section -->
      <fieldset class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <legend class="sr-only">Patient Symptoms Information</legend>
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
          <h3 class="text-base font-semibold text-gray-900 flex items-center" id="symptoms-section">
            <svg class="h-5 w-5 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Symptoms
          </h3>
        </div>
        <div class="px-4 py-4">
          <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-1">
            Patient Symptoms
            <span class="sr-only">(describe what the patient is experiencing)</span>
          </label>
          <textarea 
            id="symptoms" 
            v-model="medicalRecordData.symptoms" 
            rows="3" 
            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md shadow-sm transition-colors duration-200 focus:outline-none"
            :class="{ 'border-red-300 ring-red-300 focus:ring-red-500 focus:border-red-500': validationErrors.symptoms }"
            placeholder="Describe the patient's symptoms in detail"
            :aria-invalid="!!validationErrors.symptoms"
            :aria-describedby="validationErrors.symptoms ? 'symptoms-error' : 'symptoms-help'"
            @keydown.ctrl.enter.prevent="submitForm"
            maxlength="1000"
            ref="firstInputRef"
          ></textarea>
          <div id="symptoms-help" class="mt-1 text-xs text-gray-500">
            Detailed description of the patient's current symptoms and complaints. Press Ctrl+Enter to submit.
          </div>
          <p 
            v-if="validationErrors.symptoms" 
            id="symptoms-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.symptoms }}
          </p>
        </div>
      </fieldset>
      
      <!-- Additional Notes Section -->
      <fieldset class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <legend class="sr-only">Additional Medical Notes</legend>
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
          <h3 class="text-base font-semibold text-gray-900 flex items-center" id="notes-section">
            <svg class="h-5 w-5 text-indigo-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Additional Notes
          </h3>
        </div>
        <div class="px-4 py-4">
          <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
            Notes
            <span class="text-sm text-gray-500">(optional)</span>
          </label>
          <textarea 
            id="notes" 
            v-model="medicalRecordData.notes" 
            rows="3" 
            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full border-gray-300 rounded-md shadow-sm transition-colors duration-200 focus:outline-none"
            :class="{ 'border-red-300 ring-red-300 focus:ring-red-500 focus:border-red-500': validationErrors.notes }"
            placeholder="Additional notes, observations, or follow-up instructions"
            :aria-invalid="!!validationErrors.notes"
            :aria-describedby="validationErrors.notes ? 'notes-error' : 'notes-help'"
            @keydown.ctrl.enter.prevent="submitForm"
            maxlength="1000"
          ></textarea>
          <div id="notes-help" class="mt-1 text-xs text-gray-500">
            Any additional observations, treatment notes, or follow-up instructions
          </div>
          <p 
            v-if="validationErrors.notes" 
            id="notes-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.notes }}
          </p>
        </div>
      </fieldset>
    </form>
    
    <nav class="flex justify-center items-center mt-8 gap-4" role="navigation" aria-label="Form navigation">
      <button 
        @click="prevStep" 
        @keydown.alt.p.prevent="prevStep"
        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
        aria-describedby="prev-button-help"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Previous Step
        <span class="sr-only">(Alt+P)</span>
      </button>
      <div id="prev-button-help" class="sr-only">
        Return to the anamnesis step to make changes
      </div>
      
      <button 
        @click="submitForm" 
        @keydown.alt.s.prevent="submitForm"
        class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200 shadow-md min-w-[200px] disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="isSubmitting"
        aria-describedby="submit-button-help"
        ref="submitButtonRef"
      >
        <span v-if="isSubmitting" class="flex items-center" role="status" aria-live="polite">
          <span class="sr-only">Submitting medical record, please wait...</span>
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Submitting...
        </span>
        <span v-else class="flex items-center">
          <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Submit Medical Record
          <span class="sr-only">(Alt+S)</span>
        </span>
      </button>
      <div id="submit-button-help" class="sr-only">
        Submit the complete medical record to the system
      </div>
    </nav>
    
    <!-- Loading announcement for screen readers -->
    <div v-if="isSubmitting" aria-live="assertive" class="sr-only">
      Submitting medical record. Please wait.
    </div>
    
    <!-- Status announcements for screen readers -->
    <div id="record-status" aria-live="polite" class="sr-only"></div>
    
    <!-- Success modal -->
    <div 
      v-if="formStatus.success" 
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50"
      role="dialog"
      aria-modal="true"
      aria-labelledby="success-title"
      aria-describedby="success-description"
      @keydown="handleModalKeydown"
      ref="successModalRef"
    >
      <div class="bg-white p-8 rounded-lg max-w-md w-full transform transition-all animate-fade-scale-in shadow-xl" role="document">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
            <svg class="h-10 w-10 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 id="success-title" class="text-lg font-bold text-gray-900 mt-5">Success!</h3>
          <p id="success-description" class="text-sm text-gray-500 mt-2">The medical record has been created successfully.</p>
          
          <div class="mt-6 flex justify-center space-x-3" role="group" aria-label="Post-submission actions">
            <button 
              @click="startNewRecord" 
              @keydown.enter.prevent="startNewRecord"
              class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
              ref="newRecordButtonRef"
              aria-describedby="new-record-help"
            >
              <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Create Another Record
            </button>
            <div id="new-record-help" class="sr-only">
              Start creating a new medical record for another patient
            </div>
            
            <button 
              @click="viewAllRecords" 
              @keydown.enter.prevent="viewAllRecords"
              class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
              aria-describedby="view-records-help"
            >
              <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
              View All Records
            </button>
            <div id="view-records-help" class="sr-only">
              Navigate to the medical records list to view all existing records
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'
import { validateMedicalRecord } from '@/Utils/validation'
import { router, usePage } from '@inertiajs/vue3'

const store = useMedicalFormStore()
const { props: pageProps } = usePage()
const isSubmitting = ref(false)
const validationErrors = ref({})

// Template refs for focus management
const stepTitleRef = ref(null)
const sampleDataButton = ref(null)
const firstInputRef = ref(null)
const submitButtonRef = ref(null)
const successModalRef = ref(null)
const newRecordButtonRef = ref(null)

// Computed properties from store
const patientId = computed(() => store.patientId)
const patientName = computed(() => store.patientName)
const medicalRecordData = computed(() => store.medicalRecordData)
const formStatus = computed(() => store.formStatus)

// Focus management
onMounted(() => {
  nextTick(() => {
    if (stepTitleRef.value) {
      stepTitleRef.value.focus()
    }
  })
})

// Watch for success modal and manage focus
watch(() => formStatus.value.success, (isSuccess) => {
  if (isSuccess) {
    nextTick(() => {
      if (newRecordButtonRef.value) {
        newRecordButtonRef.value.focus()
      }
      announceStatus('Medical record created successfully. Choose your next action.')
    })
  }
})

// Keyboard navigation functions
function handleGlobalKeydown(event) {
  // Global keyboard shortcuts
  if (event.altKey) {
    switch (event.key.toLowerCase()) {
      case 'p':
        event.preventDefault()
        prevStep()
        break
      case 's':
        event.preventDefault()
        submitForm()
        break
    }
  }
  
  // S key for sample data (when not in alt combination)
  if (event.key.toLowerCase() === 's' && !event.ctrlKey && !event.altKey) {
    event.preventDefault()
    fillSampleData()
  }
  
  // Ctrl+Enter to submit
  if (event.ctrlKey && event.key === 'Enter') {
    event.preventDefault()
    submitForm()
  }
  
  // Escape key handling
  if (event.key === 'Escape') {
    if (document.activeElement) {
      document.activeElement.blur()
    }
  }
}

function handleFormKeydown(event) {
  // Enhanced form navigation
  if (event.key === 'Tab' && !event.shiftKey) {
    // Tab navigation enhancement
  }
}

function handleModalKeydown(event) {
  // Modal keyboard navigation
  if (event.key === 'Escape') {
    // In this case, we don't want to close the modal on escape
    // since it's a success modal and user should make a conscious choice
    event.preventDefault()
  }
  
  // Tab trapping within modal
  if (event.key === 'Tab') {
    const focusableElements = successModalRef.value?.querySelectorAll('button')
    if (focusableElements && focusableElements.length > 0) {
      const firstElement = focusableElements[0]
      const lastElement = focusableElements[focusableElements.length - 1]
      
      if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault()
        lastElement.focus()
      } else if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault()
        firstElement.focus()
      }
    }
  }
}

function announceStatus(message) {
  const statusElement = document.getElementById('record-status')
  if (statusElement) {
    statusElement.textContent = message
  }
}

// Go back to previous step
function prevStep() {
  announceStatus('Returning to anamnesis step')
  store.goToPrevStep()
}

// Update medical record data in store
function updateMedicalRecordField(field, value) {
  const update = { [field]: value }
  store.updateMedicalRecordData(update)
}

// Fill with sample data
function fillSampleData() {
  const sampleData = {
    symptoms: 'Patient reports mild headache and fatigue lasting 2 days. No fever or nausea.',
    notes: 'Patient appears well. Vital signs stable. Advised on stress management techniques and importance of adequate hydration.'
  }
  
  store.updateMedicalRecordData(sampleData)
  announceStatus('Sample medical record data filled in all fields')
  
  // Clear any validation errors
  validationErrors.value = {}
  
  // Focus the submit button
  nextTick(() => {
    if (submitButtonRef.value) {
      submitButtonRef.value.focus()
    }
  })
}

// Validate and submit the form
async function submitForm() {
  try {
    validationErrors.value = {}
    store.setError(null)
    announceStatus('Validating medical record data...')
    
    // Form validation
    const { isValid, errors } = validateMedicalRecord(medicalRecordData.value)
    
    if (!isValid) {
      validationErrors.value = errors
      announceStatus('Please correct the validation errors before submitting')
      
      // Focus the first field with an error
      const firstErrorField = Object.keys(errors)[0]
      if (firstErrorField) {
        const errorElement = document.getElementById(firstErrorField)
        if (errorElement) {
          errorElement.focus()
        }
      }
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
      onStart: () => {
        announceStatus('Submitting medical record to server...')
      },
      onSuccess: () => {
        store.setSuccess(true)
        announceStatus('Medical record submitted successfully!')
        
        // Clear localStorage after successful submission
        localStorage.removeItem('medicalFormState')
      },
      onError: (errors) => {
        validationErrors.value = errors
        store.setError('There was an error creating the medical record. Please check the form and try again.')
        announceStatus('Error submitting medical record. Please check the form and try again.')
        
        // Focus first error field
        const firstErrorField = Object.keys(errors)[0]
        if (firstErrorField) {
          const errorElement = document.getElementById(firstErrorField)
          if (errorElement) {
            errorElement.focus()
          }
        }
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
    announceStatus('An unexpected error occurred. Please try again.')
    
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
  announceStatus('Starting new medical record. Resetting form...')
  
  // Reset the entire form, not just the medical record portion
  store.resetForm()
  
  // Reset success state
  store.setSuccess(false)
  validationErrors.value = {}
  
  // Remove form data from localStorage to ensure a clean state
  localStorage.removeItem('medicalFormState')
}

// View all medical records for this patient
function viewAllRecords() {
  announceStatus('Navigating to medical records list...')
  
  // Clear form state before navigating
  localStorage.removeItem('medicalFormState')
  router.visit(`/medical-records`)
}
</script>

<style scoped>
/* Focus indicators */
button:focus,
input:focus,
select:focus,
textarea:focus {
  @apply ring-2 ring-indigo-500 ring-offset-2;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border-gray-300 {
    @apply border-gray-900;
  }
  
  .text-gray-500 {
    @apply text-gray-900;
  }
  
  .bg-gray-50 {
    @apply bg-white border border-gray-900;
  }
  
  .bg-gradient-to-r {
    @apply bg-white border-2 border-indigo-900;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .transition-colors {
    transition: none;
  }
  
  .animate-spin {
    animation: none;
  }
  
  .animate-fade-scale-in {
    animation: none;
    opacity: 1;
    transform: scale(1);
  }
}

/* Screen reader only content */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* Focus within fieldsets */
fieldset:focus-within {
  @apply ring-2 ring-indigo-500 ring-offset-2 rounded-md;
}

/* Modal animations */
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

/* Modal focus management */
[role="dialog"] {
  isolation: isolate;
}
</style> 