<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow">
    <!-- Header -->
    <div class="p-4 bg-blue-600 text-white rounded-t-lg">
      <h2 class="text-xl font-bold">Create Medical Record</h2>
      <p class="text-sm mt-1">Fill in the form below to create a new medical record for a patient.</p>
    </div>
    
    <!-- Form Progress Indicator -->
    <div class="p-4 border-b border-gray-200">
      <FormProgress 
        :current-step="currentStep" 
        :progress="stepProgress"
        @navigate-to-step="handleStepNavigation"
      />
    </div>
    
    <!-- Loading Spinner -->
    <div v-if="isFormLoading" class="flex justify-center items-center p-10">
      <div class="w-10 h-10 animate-spin rounded-full border-4 border-blue-600 border-r-transparent"></div>
      <p class="ml-4 text-gray-600">Processing...</p>
    </div>
    
    <!-- Form Steps -->
    <div v-else class="p-4">
      <div v-if="currentStep === 1">
        <PatientStep :patients="patients" />
      </div>
      
      <div v-else-if="currentStep === 2">
        <AnamnesisStep />
      </div>
      
      <div v-else-if="currentStep === 3">
        <MedicalRecordStep />
      </div>
    </div>
    
    <!-- Error Banner -->
    <div 
      v-if="formStatus.error" 
      class="flex p-4 mx-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50"
      role="alert"
    >
      <svg class="w-5 h-5 mr-2" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
      </svg>
      <span class="font-medium">{{ formStatus.error }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'
import FormProgress from './FormProgress.vue'
import PatientStep from './PatientStep.vue'
import AnamnesisStep from './AnamnesisStep.vue'
import MedicalRecordStep from './MedicalRecordStep.vue'

// Props
const props = defineProps({
  patients: {
    type: Array,
    default: () => []
  }
})

// Initialize store
const store = useMedicalFormStore()

// Computed properties
const currentStep = computed(() => store.currentStep)
const stepProgress = computed(() => store.stepProgress)
const isFormLoading = computed(() => store.formStatus.isSubmitting)
const formStatus = computed(() => store.formStatus)

// Save state only when explicitly required (on specific step changes)
// No need to automatically save state on every step change
watch(() => store.currentStep, (newStep) => {
  // Only save state when moving forward in the form
  if (newStep > 1) {
    store.saveState()
  }
})

// Don't load state from localStorage automatically
// The parent component will decide when to reset or preserve the form
onMounted(() => {
  // Only set patients data from props without loading saved state
  if (props.patients && Array.isArray(props.patients) && props.patients.length > 0) {
    store.setPatients(props.patients)
  }
})

// Add handleStepNavigation function
const handleStepNavigation = (step) => {
  store.goToStep(step)
}
</script> 