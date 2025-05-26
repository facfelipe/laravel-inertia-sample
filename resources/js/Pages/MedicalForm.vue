<template>
  <MainLayout title="Create Medical Record">
    <div class="py-8 px-4 max-w-7xl mx-auto">
      <div class="bg-white rounded-lg shadow">
        <div class="p-6">
          <p class="mb-4 text-gray-600">
            Follow the steps below to create a new medical record for a patient.
          </p>
          
          <MultiStepForm :patients="patients" />
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import MultiStepForm from '@/Components/MedicalForm/MultiStepForm.vue'
import { useMedicalFormStore } from '@/Stores/medicalFormStore'

// Receive patients data from Inertia props
const props = defineProps({
  patients: {
    type: Array,
    default: () => []
  },
  initialStep: {
    type: Number,
    default: 1
  }
})

// Initialize the store
const store = useMedicalFormStore()

// Clear form data when this page loads to ensure a fresh start
onMounted(() => {
  // Initialize the step from the URL/props first
  store.initializeStep(props.initialStep)
  
  // Load any saved form data (excluding step)
  store.loadState()
  
  // Then set patients data from props
  if (props.patients && Array.isArray(props.patients) && props.patients.length > 0) {
    store.setPatients(props.patients)
  }
})
</script> 