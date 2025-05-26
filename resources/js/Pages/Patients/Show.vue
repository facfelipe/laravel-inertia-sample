<script setup>
import { Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

// Receive props from Inertia
const props = defineProps({
  patient: {
    type: Object,
    required: true
  }
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <MainLayout :title="`Patient: ${patient.name}`">
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Patient Details</h1>
                <p class="text-gray-600">View detailed patient information</p>
              </div>
              <div class="flex space-x-3">
                <Link 
                  :href="`/patients/${patient.id}/edit`"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Edit Patient
                </Link>
                <Link 
                  href="/patients"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Back to Patients
                </Link>
              </div>
            </div>

            <!-- Patient Information -->
            <div class="grid grid-cols-1 gap-6">
              <!-- Basic Information Card -->
              <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 gap-4">
                  <div class="flex items-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                      <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <div>
                      <h4 class="text-xl font-bold text-gray-900">{{ patient.name }}</h4>
                      <p class="text-gray-600">Patient ID: #{{ patient.id }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Contact Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 gap-4">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-gray-900">Email</p>
                      <p class="text-sm text-gray-600">{{ patient.email || 'Not provided' }}</p>
                    </div>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-gray-900">Phone</p>
                      <p class="text-sm text-gray-600">{{ patient.phone || 'Not provided' }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Additional Information -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Date of Birth</p>
                    <p class="text-sm text-gray-600">{{ formatDate(patient.date_of_birth) }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">Gender</p>
                    <p class="text-sm text-gray-600">{{ patient.gender || 'Not specified' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">Address</p>
                    <p class="text-sm text-gray-600">{{ patient.address || 'Not provided' }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">Emergency Contact</p>
                    <p class="text-sm text-gray-600">{{ patient.emergency_contact || 'Not provided' }}</p>
                  </div>
                </div>
              </div>

              <!-- Record Timestamps -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Record Information</h3>
                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <p class="text-sm font-medium text-gray-900">Created</p>
                    <p class="text-sm text-gray-600">{{ formatDateTime(patient.created_at) }}</p>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">Last Updated</p>
                    <p class="text-sm text-gray-600">{{ formatDateTime(patient.updated_at) }}</p>
                  </div>
                </div>
              </div>

              <!-- Medical Records Summary -->
              <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                  <h3 class="text-lg font-medium text-gray-900">Medical Records</h3>
                  <Link 
                    href="/medical-form"
                    class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                  >
                    Create New Record →
                  </Link>
                </div>
                <div class="text-center py-8">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="mt-2 text-sm text-gray-500">No medical records found for this patient.</p>
                  <p class="text-sm text-gray-500">
                    <Link href="/medical-form" class="text-blue-600 hover:text-blue-800">Create the first medical record</Link>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<style scoped>
/* Add transitions for hover effects */
.transition-colors {
  transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}
</style> 