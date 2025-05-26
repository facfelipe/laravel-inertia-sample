<template>
  <div 
    role="main" 
    aria-labelledby="anamnesis-step-title"
    @keydown="handleGlobalKeydown"
  >
    <h2 
      id="anamnesis-step-title" 
      class="text-xl font-medium mb-4"
      tabindex="-1"
      ref="stepTitleRef"
    >
      Step 2: Anamnesis
    </h2>
    
    <!-- Keyboard shortcuts help -->
    <div class="sr-only" aria-live="polite" id="anamnesis-keyboard-help">
      Press Tab to navigate between fields, F to fill standard values, Alt+P for previous step, Alt+N for next step, Ctrl+Enter to proceed
    </div>
    
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6" role="banner" aria-labelledby="patient-info">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p id="patient-info" class="text-sm text-blue-700">
            Creating anamnesis for patient: <strong>{{ patientName }}</strong>
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Fill Button -->
    <div class="mb-6 flex justify-end">
      <button 
        @click="fillStandardValues" 
        @keydown.f.prevent="fillStandardValues"
        type="button"
        class="inline-flex items-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
        aria-describedby="fill-standard-help"
        ref="fillStandardButton"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Fill with Standard Values
        <span class="sr-only">(Press F)</span>
      </button>
      <div id="fill-standard-help" class="sr-only">
        Click to fill all vital signs fields with typical healthy adult values for testing purposes
      </div>
    </div>
    
    <form class="space-y-6" role="form" aria-labelledby="anamnesis-form-title" @keydown="handleFormKeydown">
      <h3 id="anamnesis-form-title" class="sr-only">Patient Vital Signs and Medical History Form</h3>
      
      <!-- Vital Signs Section -->
      <fieldset class="bg-gray-50 p-4 rounded-md">
        <legend class="text-lg font-medium mb-4">Vital Signs</legend>
        
        <div class="grid grid-cols-2 gap-6" role="group" aria-labelledby="vital-signs-group">
          <div id="vital-signs-group" class="sr-only">Vital signs measurements</div>
          
          <div class="form-field">
            <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-1">
              Blood Pressure
              <span class="sr-only">(format: systolic/diastolic, e.g., 120/80)</span>
            </label>
            <input
              id="blood_pressure"
              v-model="anamnesisData.blood_pressure"
              type="text"
              placeholder="e.g., 120/80"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.blood_pressure }"
              :aria-invalid="!!validationErrors.blood_pressure"
              :aria-describedby="validationErrors.blood_pressure ? 'blood-pressure-error' : 'blood-pressure-help'"
              @keydown.enter.prevent="focusNextField"
              autocomplete="off"
              ref="firstInputRef"
            />
            <div id="blood-pressure-help" class="mt-1 text-xs text-gray-500">
              Enter as systolic/diastolic (e.g., 120/80 mmHg)
            </div>
            <p 
              v-if="validationErrors.blood_pressure" 
              id="blood-pressure-error"
              class="mt-1 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.blood_pressure }}
            </p>
          </div>
          
          <div class="form-field">
            <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-1">
              Heart Rate (bpm)
              <span class="sr-only">(beats per minute, typical range 60-100)</span>
            </label>
            <input
              id="heart_rate"
              v-model="anamnesisData.heart_rate"
              type="number"
              min="30"
              max="250"
              placeholder="e.g., 72"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.heart_rate }"
              :aria-invalid="!!validationErrors.heart_rate"
              :aria-describedby="validationErrors.heart_rate ? 'heart-rate-error' : 'heart-rate-help'"
              @keydown.enter.prevent="focusNextField"
              autocomplete="off"
            />
            <div id="heart-rate-help" class="mt-1 text-xs text-gray-500">
              Normal resting rate: 60-100 bpm
            </div>
            <p 
              v-if="validationErrors.heart_rate" 
              id="heart-rate-error"
              class="mt-1 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.heart_rate }}
            </p>
          </div>
          
          <div class="form-field">
            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">
              Temperature (°C)
              <span class="sr-only">(body temperature in Celsius, normal around 36.5-37.5)</span>
            </label>
            <input
              id="temperature"
              v-model="anamnesisData.temperature"
              type="number"
              step="0.1"
              min="30"
              max="45"
              placeholder="e.g., 36.5"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.temperature }"
              :aria-invalid="!!validationErrors.temperature"
              :aria-describedby="validationErrors.temperature ? 'temperature-error' : 'temperature-help'"
              @keydown.enter.prevent="focusNextField"
              autocomplete="off"
            />
            <div id="temperature-help" class="mt-1 text-xs text-gray-500">
              Normal body temperature: 36.5-37.5°C
            </div>
            <p 
              v-if="validationErrors.temperature" 
              id="temperature-error"
              class="mt-1 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.temperature }}
            </p>
          </div>
          
          <div class="form-field">
            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
              Weight (kg)
              <span class="sr-only">(patient weight in kilograms)</span>
            </label>
            <input
              id="weight"
              v-model="anamnesisData.weight"
              type="number"
              step="0.1"
              min="1"
              max="500"
              placeholder="e.g., 70"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.weight }"
              :aria-invalid="!!validationErrors.weight"
              :aria-describedby="validationErrors.weight ? 'weight-error' : 'weight-help'"
              @keydown.enter.prevent="focusNextField"
              autocomplete="off"
            />
            <div id="weight-help" class="mt-1 text-xs text-gray-500">
              Patient weight in kilograms
            </div>
            <p 
              v-if="validationErrors.weight" 
              id="weight-error"
              class="mt-1 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.weight }}
            </p>
          </div>
        </div>
      </fieldset>
      
      <!-- Physical Measurements -->
      <fieldset class="bg-gray-50 p-4 rounded-md">
        <legend class="text-lg font-medium mb-4">Physical Measurements</legend>
        
        <div class="grid grid-cols-2 gap-6" role="group" aria-labelledby="measurements-group">
          <div id="measurements-group" class="sr-only">Physical body measurements</div>
          
          <div class="form-field">
            <label for="height" class="block text-sm font-medium text-gray-700">
              Height (cm)
              <span class="sr-only">(patient height in centimeters)</span>
            </label>
            <input 
              type="number" 
              id="height" 
              v-model="anamnesisData.height" 
              placeholder="170"
              min="50"
              max="250"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.height }"
              :aria-invalid="!!validationErrors.height"
              :aria-describedby="validationErrors.height ? 'height-error' : 'height-help'"
              @keydown.enter.prevent="focusNextField"
              autocomplete="off"
            />
            <div id="height-help" class="mt-1 text-xs text-gray-500">
              Patient height in centimeters
            </div>
            <p 
              v-if="validationErrors.height" 
              id="height-error"
              class="mt-2 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.height }}
            </p>
          </div>
        </div>
      </fieldset>

      <!-- Medical History -->
      <fieldset class="bg-gray-50 p-4 rounded-md">
        <legend class="text-lg font-medium mb-4">Medical History</legend>
        
        <div class="grid grid-cols-2 gap-6" role="group" aria-labelledby="history-group">
          <div id="history-group" class="sr-only">Patient medical history information</div>
          
          <div class="form-field">
            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-1">
              Allergies
              <span class="text-sm text-gray-500">(optional)</span>
            </label>
            <textarea
              id="allergies"
              v-model="anamnesisData.allergies"
              rows="3"
              placeholder="List any known allergies..."
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.allergies }"
              :aria-invalid="!!validationErrors.allergies"
              :aria-describedby="validationErrors.allergies ? 'allergies-error' : 'allergies-help'"
              @keydown.ctrl.enter.prevent="nextStep"
              maxlength="500"
            ></textarea>
            <div id="allergies-help" class="mt-1 text-xs text-gray-500">
              Include food, drug, and environmental allergies. Press Ctrl+Enter to proceed.
            </div>
            <p 
              v-if="validationErrors.allergies" 
              id="allergies-error"
              class="mt-1 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.allergies }}
            </p>
          </div>

          <div class="form-field">
            <label for="medications" class="block text-sm font-medium text-gray-700 mb-1">
              Current Medications
              <span class="text-sm text-gray-500">(optional)</span>
            </label>
            <textarea
              id="medications"
              v-model="anamnesisData.medications"
              rows="3"
              placeholder="List current medications and dosages..."
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
              :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': validationErrors.medications }"
              :aria-invalid="!!validationErrors.medications"
              :aria-describedby="validationErrors.medications ? 'medications-error' : 'medications-help'"
              @keydown.ctrl.enter.prevent="nextStep"
              maxlength="500"
            ></textarea>
            <div id="medications-help" class="mt-1 text-xs text-gray-500">
              Include prescription drugs, over-the-counter medications, and supplements
            </div>
            <p 
              v-if="validationErrors.medications" 
              id="medications-error"
              class="mt-1 text-sm text-red-600" 
              role="alert"
              aria-live="polite"
            >
              {{ validationErrors.medications }}
            </p>
          </div>
        </div>
      </fieldset>
    </form>
    
    <nav class="flex justify-between mt-8" role="navigation" aria-label="Step navigation">
      <button 
        @click="prevStep" 
        @keydown.alt.p.prevent="prevStep"
        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        aria-describedby="prev-button-help"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Previous Step
        <span class="sr-only">(Alt+P)</span>
      </button>
      <div id="prev-button-help" class="sr-only">
        Go back to the patient information step
      </div>
      
      <button 
        @click="nextStep" 
        @keydown.alt.n.prevent="nextStep"
        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="isSubmitting"
        aria-describedby="next-button-help"
        ref="nextButtonRef"
      >
        <span v-if="isSubmitting" role="status" aria-live="polite">
          <span class="sr-only">Saving anamnesis data, please wait...</span>
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Saving...
        </span>
        <span v-else>
          <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
          </svg>
          Next Step
          <span class="sr-only">(Alt+N)</span>
        </span>
      </button>
      <div id="next-button-help" class="sr-only">
        Save anamnesis data and proceed to medical record step
      </div>
    </nav>
    
    <!-- Loading announcement for screen readers -->
    <div v-if="isSubmitting" aria-live="assertive" class="sr-only">
      Saving anamnesis information. Please wait.
    </div>
    
    <!-- Status announcements for screen readers -->
    <div id="anamnesis-status" aria-live="polite" class="sr-only"></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'
import { validateAnamnesis } from '@/Utils/validation'
import { router } from '@inertiajs/vue3'

const store = useMedicalFormStore()
const isSubmitting = ref(false)
const validationErrors = ref({})

// Template refs for focus management
const stepTitleRef = ref(null)
const fillStandardButton = ref(null)
const firstInputRef = ref(null)
const nextButtonRef = ref(null)

// Computed properties from store
const patientId = computed(() => store.patientId)
const patientName = computed(() => store.patientName)
const anamnesisData = computed(() => store.anamnesisData)

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
      case 'p':
        event.preventDefault()
        prevStep()
        break
      case 'n':
        event.preventDefault()
        nextStep()
        break
    }
  }
  
  // F key for fill standard values
  if (event.key.toLowerCase() === 'f' && !event.ctrlKey && !event.altKey) {
    event.preventDefault()
    fillStandardValues()
  }
  
  // Ctrl+Enter to proceed
  if (event.ctrlKey && event.key === 'Enter') {
    event.preventDefault()
    nextStep()
  }
  
  // Escape key handling
  if (event.key === 'Escape') {
    if (document.activeElement) {
      document.activeElement.blur()
    }
  }
}

function handleFormKeydown(event) {
  // Custom form navigation can be implemented here
  if (event.key === 'Tab' && !event.shiftKey) {
    // Tab navigation enhancement
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

function announceStatus(message) {
  const statusElement = document.getElementById('anamnesis-status')
  if (statusElement) {
    statusElement.textContent = message
  }
}

// Fill form with standard/typical medical values
function fillStandardValues() {
  const standardValues = {
    blood_pressure: '120/80',
    temperature: '36.5',
    heart_rate: '72',
    weight: '70',
    height: '170'
  }
  
  store.updateAnamnesisData(standardValues)
  announceStatus('Standard vital signs values filled in all fields')
  
  // Clear any validation errors
  validationErrors.value = {}
  
  // Focus the next step button
  nextTick(() => {
    if (nextButtonRef.value) {
      nextButtonRef.value.focus()
    }
  })
}

// Go back to previous step
function prevStep() {
  announceStatus('Returning to patient information step')
  store.goToPrevStep()
}

// Update anamnesis data in store
function updateAnamnesisField(field, value) {
  const update = { [field]: value }
  store.updateAnamnesisData(update)
}

// Validate and proceed to next step
async function nextStep() {
  try {
    validationErrors.value = {}
    announceStatus('Validating anamnesis data...')
    
    // Form validation
    const { isValid, errors } = validateAnamnesis(anamnesisData.value)
    
    if (!isValid) {
      validationErrors.value = errors
      announceStatus('Please correct the validation errors before proceeding')
      
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
    
    // Submit to the server with Inertia
    isSubmitting.value = true
    
    router.post(`/patients/${patientId.value}/anamneses`, anamnesisData.value, {
      preserveScroll: true,
      onStart: () => {
        isSubmitting.value = true
        announceStatus('Saving anamnesis data...')
      },
      onSuccess: (page) => {
        // Check for flash data with the anamnesis info
        const flashAnamnesis = page.props.flash?.anamnesis
        
        if (flashAnamnesis) {
          // Store the anamnesis data
          store.updateAnamnesisData(flashAnamnesis)
          
          // Save form state
          store.saveState()
          
          // Show success message if needed
          if (page.props.flash?.success) {
            console.log(page.props.flash.success)
          }
          
          announceStatus('Anamnesis saved successfully. Moving to medical record step.')
          
          // Proceed to next step
          store.goToNextStep()
        } else {
          // Fallback - something might have gone wrong but we'll continue
          console.log('Anamnesis created but data not received in response')
          announceStatus('Anamnesis saved. Moving to medical record step.')
          store.goToNextStep()
        }
      },
      onError: (errors) => {
        validationErrors.value = errors
        announceStatus('Error saving anamnesis. Please check the form and try again.')
        
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
    
  } catch (error) {
    console.error('Error in anamnesis step:', error)
    isSubmitting.value = false
    announceStatus('An unexpected error occurred. Please try again.')
    
    // Handle validation errors from the server
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors
    }
  }
}
</script>

<style scoped>
.form-field {
  @apply relative;
}

/* Focus indicators */
button:focus,
input:focus,
select:focus,
textarea:focus {
  @apply ring-2 ring-blue-500 ring-offset-2;
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
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .transition-colors {
    transition: none;
  }
  
  .animate-spin {
    animation: none;
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
  @apply ring-2 ring-blue-500 ring-offset-2 rounded-md;
}
</style> 