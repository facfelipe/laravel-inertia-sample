<template>
  <MainLayout title="Edit Medical Record">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Header -->
      <div class="flex justify-between items-center py-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Medical Record</h1>
        <div class="flex space-x-3">
          <Link 
            :href="`/medical-records/${record.id}`"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Cancel
          </Link>
          <button 
            @click="saveRecord"
            :disabled="form.processing"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>

      <!-- Error Display -->
      <div v-if="form.hasErrors" class="mb-6 rounded-md bg-red-50 p-4">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <div class="mt-2 text-sm text-red-700">
              <ul class="list-disc pl-5 space-y-1">
                <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Medical Record Details</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Update the medical record information below.</p>
        </div>
        
        <form @submit.prevent="saveRecord" class="px-6 py-5 space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Symptoms -->
            <div class="col-span-2">
              <label for="symptoms" class="block text-sm font-medium text-gray-700">Symptoms</label>
              <textarea 
                v-model="form.symptoms" 
                id="symptoms"
                rows="3" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.symptoms }"
              ></textarea>
              <p v-if="form.errors.symptoms" class="mt-1 text-sm text-red-600">{{ form.errors.symptoms }}</p>
            </div>
            
            <!-- Diagnosis -->
            <div class="col-span-2">
              <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
              <textarea 
                v-model="form.diagnosis" 
                id="diagnosis"
                rows="3" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.diagnosis }"
              ></textarea>
              <p v-if="form.errors.diagnosis" class="mt-1 text-sm text-red-600">{{ form.errors.diagnosis }}</p>
            </div>
            
            <!-- Treatment -->
            <div class="col-span-2">
              <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment</label>
              <textarea 
                v-model="form.treatment" 
                id="treatment"
                rows="3" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.treatment }"
              ></textarea>
              <p v-if="form.errors.treatment" class="mt-1 text-sm text-red-600">{{ form.errors.treatment }}</p>
            </div>
            
            <!-- Notes -->
            <div class="col-span-2">
              <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
              <textarea 
                v-model="form.notes" 
                id="notes"
                rows="3" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.notes }"
              ></textarea>
              <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
            </div>
          </div>
          
          <!-- Anamnesis Section -->
          <div v-if="anamnesis" class="mt-8 border-t pt-6">
            <h4 class="text-lg font-medium text-gray-700 mb-4">Anamnesis Information</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Blood Pressure -->
              <div>
                <label for="blood_pressure" class="block text-sm font-medium text-gray-700">Blood Pressure</label>
                <input 
                  v-model="anamnesisForm.blood_pressure" 
                  type="text" 
                  id="blood_pressure" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                />
              </div>
              
              <!-- Temperature -->
              <div>
                <label for="temperature" class="block text-sm font-medium text-gray-700">Temperature (°C)</label>
                <input 
                  v-model="anamnesisForm.temperature" 
                  type="number" 
                  step="0.1" 
                  id="temperature" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                />
              </div>
              
              <!-- Heart Rate -->
              <div>
                <label for="heart_rate" class="block text-sm font-medium text-gray-700">Heart Rate (bpm)</label>
                <input 
                  v-model="anamnesisForm.heart_rate" 
                  type="number" 
                  id="heart_rate" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                />
              </div>
              
              <!-- Respiratory Rate -->
              <div>
                <label for="respiratory_rate" class="block text-sm font-medium text-gray-700">Respiratory Rate (brpm)</label>
                <input 
                  v-model="anamnesisForm.respiratory_rate" 
                  type="number" 
                  id="respiratory_rate" 
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                />
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

// Get data from Inertia props
const props = defineProps({
  record: {
    type: Object,
    required: true
  },
  patient: {
    type: Object,
    default: null
  },
  anamnesis: {
    type: Object,
    default: null
  }
});

// Set up form for updating medical record
const form = useForm({
  symptoms: props.record?.symptoms || '',
  diagnosis: props.record?.diagnosis || '',
  treatment: props.record?.treatment || '',
  notes: props.record?.notes || '',
  patient_id: props.record?.patient_id || props.patient?.id || null
});

// If anamnesis exists, set up form for updating it (not implemented in this fix)
const anamnesisForm = useForm({
  blood_pressure: props.anamnesis?.blood_pressure || '',
  temperature: props.anamnesis?.temperature || '',
  heart_rate: props.anamnesis?.heart_rate || '',
  respiratory_rate: props.anamnesis?.respiratory_rate || ''
});

// Handle form submission
const saveRecord = () => {
  form.put(`/medical-records/${props.record.id}`, {
    onSuccess: () => {
      // Redirect handled by controller
    },
    onError: (errors) => {
      console.error('Update failed:', errors);
    }
  });
};
</script> 