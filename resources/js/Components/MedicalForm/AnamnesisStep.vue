<template>
  <div>
    <h2 class="text-xl font-medium mb-4">Step 2: Anamnesis</h2>
    
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-blue-700">
            Creating anamnesis for patient: <strong>{{ patientName }}</strong>
          </p>
        </div>
      </div>
    </div>

    <!-- Quick Fill Button -->
    <div class="mb-6 flex justify-end">
      <button 
        @click="fillStandardValues" 
        type="button"
        class="inline-flex items-center px-4 py-2 border border-blue-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
      >
        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Fill with Standard Values
      </button>
    </div>
    
    <form class="space-y-6">
      <!-- Vital Signs Section -->
      <div class="bg-gray-50 p-4 rounded-md">
        <h3 class="text-lg font-medium mb-4">Vital Signs</h3>
        
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-1">
              Blood Pressure
            </label>
            <input
              id="blood_pressure"
              v-model="anamnesisData.blood_pressure"
              type="text"
              placeholder="e.g., 120/80"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="validationErrors.blood_pressure" class="mt-1 text-sm text-red-600">
              {{ validationErrors.blood_pressure }}
            </p>
          </div>
          
          <div>
            <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-1">
              Heart Rate (bpm)
            </label>
            <input
              id="heart_rate"
              v-model="anamnesisData.heart_rate"
              type="number"
              placeholder="e.g., 72"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="validationErrors.heart_rate" class="mt-1 text-sm text-red-600">
              {{ validationErrors.heart_rate }}
            </p>
          </div>
          
          <div>
            <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">
              Temperature (°C)
            </label>
            <input
              id="temperature"
              v-model="anamnesisData.temperature"
              type="number"
              step="0.1"
              placeholder="e.g., 36.5"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="validationErrors.temperature" class="mt-1 text-sm text-red-600">
              {{ validationErrors.temperature }}
            </p>
          </div>
          
          <div>
            <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">
              Weight (kg)
            </label>
            <input
              id="weight"
              v-model="anamnesisData.weight"
              type="number"
              step="0.1"
              placeholder="e.g., 70"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="validationErrors.weight" class="mt-1 text-sm text-red-600">
              {{ validationErrors.weight }}
            </p>
          </div>
        </div>
      </div>
      
      <!-- Physical Measurements -->
      <div class="bg-gray-50 p-4 rounded-md">
        <h3 class="text-lg font-medium mb-4">Physical Measurements</h3>
        
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label for="height" class="block text-sm font-medium text-gray-700">Height (cm)</label>
            <input 
              type="number" 
              id="height" 
              v-model="anamnesisData.height" 
              placeholder="170"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              :class="{ 'border-red-500': validationErrors.height }"
            />
            <p v-if="validationErrors.height" class="mt-2 text-sm text-red-600">{{ validationErrors.height }}</p>
          </div>
        </div>
      </div>

      <!-- Medical History -->
      <div class="bg-gray-50 p-4 rounded-md">
        <h3 class="text-lg font-medium mb-4">Medical History</h3>
        
        <div class="grid grid-cols-2 gap-6">
          <div>
            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-1">
              Allergies
            </label>
            <textarea
              id="allergies"
              v-model="anamnesisData.allergies"
              rows="3"
              placeholder="List any known allergies..."
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
            <p v-if="validationErrors.allergies" class="mt-1 text-sm text-red-600">
              {{ validationErrors.allergies }}
            </p>
          </div>

          <div>
            <label for="medications" class="block text-sm font-medium text-gray-700 mb-1">
              Current Medications
            </label>
            <textarea
              id="medications"
              v-model="anamnesisData.medications"
              rows="3"
              placeholder="List current medications and dosages..."
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
            ></textarea>
            <p v-if="validationErrors.medications" class="mt-1 text-sm text-red-600">
              {{ validationErrors.medications }}
            </p>
          </div>
        </div>
      </div>
    </form>
    
    <div class="flex justify-between mt-8">
      <button 
        @click="prevStep" 
        class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Previous Step
      </button>
      
      <button 
        @click="nextStep" 
        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        :disabled="isSubmitting"
      >
        {{ isSubmitting ? 'Saving...' : 'Next Step' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'
import { validateAnamnesis } from '@/Utils/validation'
import { router } from '@inertiajs/vue3'

const store = useMedicalFormStore()
const isSubmitting = ref(false)
const validationErrors = ref({})

// Computed properties from store
const patientId = computed(() => store.patientId)
const patientName = computed(() => store.patientName)
const anamnesisData = computed(() => store.anamnesisData)

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
  
  // Clear any validation errors
  validationErrors.value = {}
}

// Go back to previous step
function prevStep() {
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
    
    // Form validation
    const { isValid, errors } = validateAnamnesis(anamnesisData.value)
    
    if (!isValid) {
      validationErrors.value = errors
      return
    }
    
    // Submit to the server with Inertia
    isSubmitting.value = true
    
    router.post(`/patients/${patientId.value}/anamneses`, anamnesisData.value, {
      preserveScroll: true,
      onStart: () => {
        isSubmitting.value = true
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
          
          // Proceed to next step
          store.goToNextStep()
        } else {
          // Fallback - something might have gone wrong but we'll continue
          console.log('Anamnesis created but data not received in response')
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
    
  } catch (error) {
    console.error('Error in anamnesis step:', error)
    isSubmitting.value = false
    
    // Handle validation errors from the server
    if (error.response && error.response.status === 422) {
      validationErrors.value = error.response.data.errors
    }
  }
}
</script> 