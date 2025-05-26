<template>
  <div>
    <h2 class="text-xl font-medium mb-4">Step 1: Patient Information</h2>
    
    <div class="space-y-4">
      <!-- Toggle between existing and new patient -->
      <div class="mb-4">
        <div class="inline-flex rounded-md shadow-sm">
          <button 
            @click="setExistingPatient(true)" 
            class="px-3 py-2 text-sm font-medium rounded-l-lg border border-gray-200"
            :class="isExistingPatient ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 hover:bg-gray-100'"
          >
            Existing Patient
          </button>
          <button 
            @click="setExistingPatient(false)" 
            class="px-3 py-2 text-sm font-medium rounded-r-lg border border-gray-200"
            :class="!isExistingPatient ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 hover:bg-gray-100'"
          >
            New Patient
          </button>
        </div>
      </div>
      
      <!-- Existing Patient Selection -->
      <div v-if="isExistingPatient">
        <label for="patients" class="block mb-2 text-sm font-medium text-gray-900">Select a Patient</label>
        <select 
          id="patients"
          v-model="selectedPatientId"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
          :class="{ 'border-red-500': validationErrors.patientId }"
        >
          <option value="" disabled>Select a patient</option>
          <option v-for="patient in patientsList" :key="patient.id" :value="patient.id">
            {{ patient.name }} - {{ patient.email }}
          </option>
        </select>
        <p v-if="validationErrors.patientId" class="mt-2 text-sm text-red-600">{{ validationErrors.patientId }}</p>
      </div>
      
      <!-- New Patient Form -->
      <form v-else class="space-y-4">
        <!-- Quick Fill Button for New Patient -->
        <div class="mb-4 flex justify-end">
          <button 
            @click="fillSamplePatient" 
            type="button"
            class="inline-flex items-center px-3 py-2 border border-purple-300 shadow-sm text-xs font-medium rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
          >
            <svg class="h-3 w-3 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Fill Sample Patient
          </button>
        </div>

        <div>
          <label for="name" class="block mb-2 text-sm font-medium text-gray-900">
            Full Name <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            id="name" 
            v-model="patientData.name" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
            :class="{ 'border-red-500': validationErrors.name }"
            maxlength="255"
          />
          <p v-if="validationErrors.name" class="mt-2 text-sm text-red-600">{{ validationErrors.name }}</p>
        </div>
        
        <div>
          <label for="email" class="block mb-2 text-sm font-medium text-gray-900">
            Email Address <span class="text-red-500">*</span>
          </label>
          <input 
            type="email" 
            id="email" 
            v-model="patientData.email" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
            :class="{ 'border-red-500': validationErrors.email }"
            maxlength="255"
          />
          <p v-if="validationErrors.email" class="mt-2 text-sm text-red-600">{{ validationErrors.email }}</p>
        </div>
        
        <div>
          <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">
            Phone Number
          </label>
          <input 
            type="tel" 
            id="phone" 
            v-model="patientData.phone" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
            :class="{ 'border-red-500': validationErrors.phone }"
            maxlength="20"
            placeholder="Optional"
          />
          <p v-if="validationErrors.phone" class="mt-2 text-sm text-red-600">{{ validationErrors.phone }}</p>
        </div>
        
        <div>
          <label for="birth_date" class="block mb-2 text-sm font-medium text-gray-900">
            Birth Date <span class="text-red-500">*</span>
          </label>
          <input 
            type="date" 
            id="birth_date" 
            v-model="patientData.birth_date" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
            :class="{ 'border-red-500': validationErrors.birth_date }"
          />
          <p v-if="validationErrors.birth_date" class="mt-2 text-sm text-red-600">{{ validationErrors.birth_date }}</p>
        </div>
        
        <div>
          <label for="address" class="block mb-2 text-sm font-medium text-gray-900">
            Address
          </label>
          <textarea 
            id="address" 
            v-model="patientData.address" 
            rows="3" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
            :class="{ 'border-red-500': validationErrors.address }"
            maxlength="255"
            placeholder="Optional"
          ></textarea>
          <p v-if="validationErrors.address" class="mt-2 text-sm text-red-600">{{ validationErrors.address }}</p>
        </div>
      </form>
    </div>
    
    <div class="flex justify-end mt-6">
      <button 
        @click="nextStep" 
        class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2"
        :disabled="isSubmitting"
      >
        <span v-if="isSubmitting">Processing...</span>
        <span v-else>Next Step</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
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

// Watch for changes to propagate to the store
watch(selectedPatientId, (newValue) => {
  if (newValue) {
    const selectedPatient = patientsList.value.find(p => p && p.id === newValue)
    if (selectedPatient) {
      store.setPatient(selectedPatient)
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
  } else {
    store.setNewPatient()
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
  
  // Clear any validation errors
  validationErrors.value = {}
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
      return
    }
    
    // If we're creating a new patient, save it using Inertia
    if (!isExistingPatient.value) {
      isSubmitting.value = true
      
      router.post('/patients', patientData.value, {
        preserveScroll: true,
        onStart: () => {
          isSubmitting.value = true
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
            
            // Proceed to next step
            store.goToNextStep()
          } else {
            // Fallback - something might have gone wrong but we'll continue
            console.log('Patient created but data not received in response')
            store.goToNextStep()
          }
        },
        onError: (errors) => {
          validationErrors.value = errors
        },
        onFinish: () => {
          isSubmitting.value = false
        }
      })
      return // Don't proceed until API call completes
    }
    
    // Save form state
    store.saveState()
    
    // Go to next step
    store.goToNextStep()
  } catch (error) {
    console.error('Error in patient step:', error)
    isSubmitting.value = false
    
    // Handle validation errors from the server
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors
    }
  }
}
</script> 