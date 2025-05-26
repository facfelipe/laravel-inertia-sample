<template>
  <div 
    role="main" 
    aria-labelledby="patient-step-title"
    @keydown="handleGlobalKeydown"
  >
    <h2 
      id="patient-step-title" 
      class="text-xl font-medium mb-4"
      tabindex="-1"
      ref="stepTitleRef"
    >
      Step 1: Patient Information
    </h2>
    
    <!-- Keyboard shortcuts help -->
    <div class="sr-only" aria-live="polite" id="keyboard-help">
      Press Tab to navigate, Enter or Space to activate buttons, Alt+E for existing patient, Alt+N for new patient, Alt+S to fill sample data, Alt+Enter to proceed to next step
    </div>
    
    <div class="space-y-4">
      <!-- Toggle between existing and new patient -->
      <fieldset class="mb-4">
        <legend class="sr-only">Patient Selection Type</legend>
        <div 
          class="inline-flex rounded-md shadow-sm" 
          role="radiogroup" 
          aria-labelledby="patient-type-label"
          @keydown="handlePatientTypeKeydown"
        >
          <span id="patient-type-label" class="sr-only">Select patient type</span>
          <button 
            type="button"
            role="radio"
            :aria-checked="isExistingPatient"
            aria-describedby="existing-patient-desc"
            @click="setExistingPatient(true)" 
            @keydown.alt.e.prevent="setExistingPatient(true)"
            class="px-3 py-2 text-sm font-medium rounded-l-lg border border-gray-200 focus:outline-none"
            :class="isExistingPatient ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 hover:bg-gray-100'"
            ref="existingPatientButton"
          >
            <span aria-hidden="true">Existing Patient</span>
            <span class="sr-only">Select existing patient from database (Alt+E)</span>
          </button>
          <span id="existing-patient-desc" class="sr-only">Choose this option to select a patient that already exists in the system</span>
          
          <button 
            type="button"
            role="radio"
            :aria-checked="!isExistingPatient"
            aria-describedby="new-patient-desc"
            @click="setExistingPatient(false)" 
            @keydown.alt.n.prevent="setExistingPatient(false)"
            class="px-3 py-2 text-sm font-medium rounded-r-lg border border-gray-200 focus:outline-none"
            :class="!isExistingPatient ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 hover:bg-gray-100'"
            ref="newPatientButton"
          >
            <span aria-hidden="true">New Patient</span>
            <span class="sr-only">Create a new patient record (Alt+N)</span>
          </button>
          <span id="new-patient-desc" class="sr-only">Choose this option to create a new patient record</span>
        </div>
      </fieldset>
      
      <!-- Existing Patient Selection -->
      <div v-if="isExistingPatient" role="region" aria-labelledby="existing-patient-section">
        <h3 id="existing-patient-section" class="sr-only">Existing Patient Selection</h3>
        <label for="patients" class="block mb-2 text-sm font-medium text-gray-900">
          Select a Patient
          <span class="sr-only">(required)</span>
        </label>
        <select 
          id="patients"
          v-model="selectedPatientId"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:outline-none"
          :class="{ 'border-red-500 focus:border-red-500 focus:shadow-red-500': validationErrors.patientId }"
          :aria-invalid="!!validationErrors.patientId"
          :aria-describedby="validationErrors.patientId ? 'patient-error' : 'patient-help'"
          ref="patientSelectRef"
          @keydown.enter.prevent="nextStep"
        >
          <option value="" disabled>Select a patient</option>
          <option v-for="patient in patientsList" :key="patient.id" :value="patient.id">
            {{ patient.name }} - {{ patient.email }}
          </option>
        </select>
        <div id="patient-help" class="mt-1 text-xs text-gray-500">
          Use arrow keys to navigate options, Enter to proceed
        </div>
        <p 
          v-if="validationErrors.patientId" 
          id="patient-error"
          class="mt-2 text-sm text-red-600" 
          role="alert"
          aria-live="polite"
        >
          {{ validationErrors.patientId }}
        </p>
      </div>
      
      <!-- New Patient Form -->
      <form v-else class="space-y-4" role="region" aria-labelledby="new-patient-section" @keydown="handleFormKeydown">
        <h3 id="new-patient-section" class="sr-only">New Patient Information Form</h3>
        
        <!-- Quick Fill Button for New Patient -->
        <div class="mb-4 flex justify-end">
          <button 
            @click="fillSamplePatient" 
            @keydown.alt.s.prevent="fillSamplePatient"
            type="button"
            class="inline-flex items-center px-3 py-2 border border-purple-300 shadow-sm text-xs font-medium rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none"
            aria-describedby="sample-help"
            ref="sampleButton"
          >
            <svg class="h-3 w-3 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Fill Sample Patient
            <span class="sr-only">(Alt+S)</span>
          </button>
          <div id="sample-help" class="sr-only">
            Click to automatically fill the form with sample patient data for testing purposes
          </div>
        </div>

        <div class="form-field">
          <label for="name" class="block mb-2 text-sm font-medium text-gray-900">
            Full Name 
            <span class="text-red-500" aria-label="required">*</span>
          </label>
          <input 
            type="text" 
            id="name" 
            v-model="patientData.name" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:outline-none"
            :class="{ 'border-red-500 focus:border-red-500 focus:shadow-red-500': validationErrors.name }"
            maxlength="255"
            required
            :aria-invalid="!!validationErrors.name"
            :aria-describedby="validationErrors.name ? 'name-error' : 'name-help'"
            ref="nameInputRef"
            @keydown.enter.prevent="focusNextField"
            autocomplete="name"
          />
          <div id="name-help" class="mt-1 text-xs text-gray-500">
            Enter the patient's full legal name
          </div>
          <p 
            v-if="validationErrors.name" 
            id="name-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.name }}
          </p>
        </div>
        
        <div class="form-field">
          <label for="email" class="block mb-2 text-sm font-medium text-gray-900">
            Email Address 
            <span class="text-red-500" aria-label="required">*</span>
          </label>
          <input 
            type="email" 
            id="email" 
            v-model="patientData.email" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:outline-none"
            :class="{ 'border-red-500 focus:border-red-500 focus:shadow-red-500': validationErrors.email }"
            maxlength="255"
            required
            :aria-invalid="!!validationErrors.email"
            :aria-describedby="validationErrors.email ? 'email-error' : 'email-help'"
            @keydown.enter.prevent="focusNextField"
            autocomplete="email"
          />
          <div id="email-help" class="mt-1 text-xs text-gray-500">
            A valid email address for communication
          </div>
          <p 
            v-if="validationErrors.email" 
            id="email-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.email }}
          </p>
        </div>
        
        <div class="form-field">
          <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">
            Phone Number
            <span class="text-sm text-gray-500">(optional)</span>
          </label>
          <input 
            type="tel" 
            id="phone" 
            v-model="patientData.phone" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:outline-none"
            :class="{ 'border-red-500 focus:border-red-500 focus:shadow-red-500': validationErrors.phone }"
            maxlength="20"
            placeholder="Optional"
            :aria-invalid="!!validationErrors.phone"
            :aria-describedby="validationErrors.phone ? 'phone-error' : 'phone-help'"
            @keydown.enter.prevent="focusNextField"
            autocomplete="tel"
          />
          <div id="phone-help" class="mt-1 text-xs text-gray-500">
            Contact phone number (optional)
          </div>
          <p 
            v-if="validationErrors.phone" 
            id="phone-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.phone }}
          </p>
        </div>
        
        <div class="form-field">
          <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900">
            Birth Date 
            <span class="text-red-500" aria-label="required">*</span>
          </label>
          <input 
            type="date" 
            id="birth_date" 
            v-model="patientData.birth_date" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:outline-none"
            :class="{ 'border-red-500 focus:border-red-500 focus:shadow-red-500': validationErrors.birth_date }"
            required
            :aria-invalid="!!validationErrors.birth_date"
            :aria-describedby="validationErrors.birth_date ? 'birth-date-error' : 'birth-date-help'"
            @keydown.enter.prevent="focusNextField"
            autocomplete="bday"
          />
          <div id="birth-date-help" class="mt-1 text-xs text-gray-500">
            Patient's date of birth for age calculation
          </div>
          <p 
            v-if="validationErrors.birth_date" 
            id="birth-date-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.birth_date }}
          </p>
        </div>
        
        <div class="form-field">
          <label for="address" class="block mb-2 text-sm font-medium text-gray-900">
            Address
            <span class="text-sm text-gray-500">(optional)</span>
          </label>
          <textarea 
            id="address" 
            v-model="patientData.address" 
            rows="3" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:outline-none"
            :class="{ 'border-red-500 focus:border-red-500 focus:shadow-red-500': validationErrors.address }"
            maxlength="255"
            placeholder="Optional"
            :aria-invalid="!!validationErrors.address"
            :aria-describedby="validationErrors.address ? 'address-error' : 'address-help'"
            @keydown.ctrl.enter.prevent="nextStep"
            autocomplete="street-address"
          ></textarea>
          <div id="address-help" class="mt-1 text-xs text-gray-500">
            Patient's residential address (optional). Press Ctrl+Enter to proceed to next step.
          </div>
          <p 
            v-if="validationErrors.address" 
            id="address-error"
            class="mt-2 text-sm text-red-600" 
            role="alert"
            aria-live="polite"
          >
            {{ validationErrors.address }}
          </p>
        </div>
      </form>
    </div>
    
    <div class="flex justify-end mt-6">
      <button 
        @click="nextStep" 
        @keydown.alt.enter.prevent="nextStep"
        class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="isSubmitting"
        :aria-describedby="'next-button-help'"
        ref="nextButtonRef"
      >
        <span v-if="isSubmitting" role="status" aria-live="polite">
          <span class="sr-only">Processing, please wait...</span>
          Processing...
        </span>
        <span v-else>
          Next Step
          <span class="sr-only">(Alt+Enter)</span>
        </span>
      </button>
      <div id="next-button-help" class="sr-only">
        Proceed to the next step in the medical record creation process
      </div>
    </div>
    
    <!-- Loading announcement for screen readers -->
    <div v-if="isSubmitting" aria-live="assertive" class="sr-only">
      Processing patient information. Please wait.
    </div>
    
    <!-- Success announcement for screen readers -->
    <div id="step-completion-status" aria-live="polite" class="sr-only"></div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'
import { validatePatientSelection } from '@/Utils/validation'
import { router } from '@inertiajs/vue3'

// Props
const props = defineProps({
  patients: {
    type: Array,
    default: () => []
  }
})

const store = useMedicalFormStore()
const validationErrors = ref({})
const isSubmitting = ref(false)

// Template refs for focus management
const stepTitleRef = ref(null)
const existingPatientButton = ref(null)
const newPatientButton = ref(null)
const patientSelectRef = ref(null)
const nameInputRef = ref(null)
const sampleButton = ref(null)
const nextButtonRef = ref(null)

// Computed properties
const isExistingPatient = computed(() => store.isExistingPatient)
const patientData = computed(() => store.patientData)
const patientsList = computed(() => {
  // Ensure it always returns an array, even if store data isn't ready
  const storePatients = store.patientsList || []
  const propsPatients = props.patients || []
  
  // If store has patients, use them, otherwise use props
  return storePatients.length > 0 ? storePatients : propsPatients
})

// Internal state for the component
const selectedPatientId = ref(store.patientId || '')

// Focus management
onMounted(() => {
  nextTick(() => {
    if (stepTitleRef.value) {
      stepTitleRef.value.focus()
    }
  })
})

// Keyboard navigation functions
function handleGlobalKeydown(event) {
  // Global keyboard shortcuts
  if (event.altKey) {
    switch (event.key.toLowerCase()) {
      case 'e':
        event.preventDefault()
        setExistingPatient(true)
        break
      case 'n':
        event.preventDefault()
        setExistingPatient(false)
        break
      case 's':
        if (!isExistingPatient.value) {
          event.preventDefault()
          fillSamplePatient()
        }
        break
      case 'enter':
        event.preventDefault()
        nextStep()
        break
    }
  }
  
  // Escape key handling
  if (event.key === 'Escape') {
    // Clear focus from current element
    if (document.activeElement) {
      document.activeElement.blur()
    }
  }
}

function handlePatientTypeKeydown(event) {
  if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
    event.preventDefault()
    const newValue = event.key === 'ArrowLeft' ? true : false
    setExistingPatient(newValue)
    
    // Focus the appropriate button
    nextTick(() => {
      if (newValue && existingPatientButton.value) {
        existingPatientButton.value.focus()
      } else if (!newValue && newPatientButton.value) {
        newPatientButton.value.focus()
      }
    })
  }
}

function handleFormKeydown(event) {
  // Tab navigation enhancement for form fields
  if (event.key === 'Tab' && !event.shiftKey) {
    // Custom tab order can be implemented here if needed
  }
}

function focusNextField() {
  // Move focus to next form field
  const focusableElements = document.querySelectorAll('input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled])')
  const currentIndex = Array.from(focusableElements).indexOf(document.activeElement)
  
  if (currentIndex !== -1 && currentIndex < focusableElements.length - 1) {
    focusableElements[currentIndex + 1].focus()
  }
}

function announceStepCompletion(message) {
  const statusElement = document.getElementById('step-completion-status')
  if (statusElement) {
    statusElement.textContent = message
  }
}

// Watch for changes to propagate to the store
watch(selectedPatientId, (newValue) => {
  if (newValue) {
    const selectedPatient = patientsList.value.find(p => p && p.id === newValue)
    if (selectedPatient) {
      store.setPatient(selectedPatient)
      announceStepCompletion(`Patient ${selectedPatient.name} selected`)
    }
  }
})

// Watch for props patients and update store if needed
watch(() => props.patients, (newPatients) => {
  if (newPatients && newPatients.length > 0) {
    store.setPatients(newPatients)
  }
}, { immediate: true })

// Set existing patient preference
function setExistingPatient(value) {
  if (value) {
    store.isExistingPatient = true
    announceStepCompletion('Switched to existing patient selection')
    // Focus the select element after switching
    nextTick(() => {
      if (patientSelectRef.value) {
        patientSelectRef.value.focus()
      }
    })
  } else {
    store.setNewPatient()
    announceStepCompletion('Switched to new patient form')
    // Focus the first input after switching
    nextTick(() => {
      if (nameInputRef.value) {
        nameInputRef.value.focus()
      }
    })
  }
  // Clear validation errors when switching
  validationErrors.value = {}
}

// Update patient data in the store
function updatePatientData(field, value) {
  const update = { [field]: value }
  store.updatePatientData(update)
}

// Fill sample patient data
function fillSamplePatient() {
  const samplePatient = {
    name: 'John Doe',
    email: `john.doe.${Date.now()}@example.com`, // Unique email to avoid conflicts
    phone: '+1 (555) 123-4567',
    birth_date: '1985-06-15',
    address: '123 Main Street, Anytown, ST 12345'
  }
  
  store.updatePatientData(samplePatient)
  announceStepCompletion('Sample patient data filled in all fields')
  
  // Clear any validation errors
  validationErrors.value = {}
  
  // Focus the next step button
  nextTick(() => {
    if (nextButtonRef.value) {
      nextButtonRef.value.focus()
    }
  })
}

// Validate and proceed to next step
async function nextStep() {
  try {
    validationErrors.value = {}
    
    // Form validation
    const formData = {
      isExistingPatient: isExistingPatient.value,
      patientId: selectedPatientId.value,
      patientData: patientData.value
    }
    
    const { isValid, errors } = validatePatientSelection(formData)
    
    if (!isValid) {
      validationErrors.value = errors
      announceStepCompletion('Please correct the validation errors before proceeding')
      
      // Focus the first field with an error
      const firstErrorField = Object.keys(errors)[0]
      if (firstErrorField) {
        const errorElement = document.getElementById(firstErrorField.replace('patientId', 'patients'))
        if (errorElement) {
          errorElement.focus()
        }
      }
      return
    }
    
    announceStepCompletion('Validating patient information...')
    
    // If we're creating a new patient, save it using Inertia
    if (!isExistingPatient.value) {
      isSubmitting.value = true
      
      router.post('/patients', patientData.value, {
        preserveScroll: true,
        onStart: () => {
          isSubmitting.value = true
          announceStepCompletion('Creating new patient record...')
        },
        onSuccess: (page) => {
          // Check for flash data with the patient info
          const flashPatient = page.props.flash?.patient
          
          if (flashPatient) {
            // Set the patient in the store
            store.setPatient(flashPatient)
            
            // Save form state
            store.saveState()
            
            // Show success message if needed
            if (page.props.flash?.success) {
              console.log(page.props.flash.success)
            }
            
            announceStepCompletion('Patient created successfully. Moving to next step.')
            
            // Proceed to next step
            store.goToNextStep()
          } else {
            // Fallback - something might have gone wrong but we'll continue
            console.log('Patient created but data not received in response')
            announceStepCompletion('Patient created. Moving to next step.')
            store.goToNextStep()
          }
        },
        onError: (errors) => {
          validationErrors.value = errors
          announceStepCompletion('Error creating patient. Please check the form and try again.')
          
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
        }
      })
      return // Don't proceed until API call completes
    }
    
    // Save form state
    store.saveState()
    
    announceStepCompletion('Patient information validated. Moving to anamnesis step.')
    
    // Go to next step
    store.goToNextStep()
  } catch (error) {
    console.error('Error in patient step:', error)
    isSubmitting.value = false
    announceStepCompletion('An unexpected error occurred. Please try again.')
    
    // Handle validation errors from the server
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors
    }
  }
}
</script>

<style scoped>
.form-field {
  position: relative;
}

/* Removed custom focus indicators to avoid Tailwind v4 compatibility issues */

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border-gray-300 {
    border-color: #111827;
  }
  
  .text-gray-500 {
    color: #111827;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .transition-colors {
    transition: none;
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
</style> 