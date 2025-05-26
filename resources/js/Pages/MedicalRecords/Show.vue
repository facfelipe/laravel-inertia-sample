<template>
  <MainLayout title="Medical Record Details">
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h2 class="text-xl font-semibold text-gray-800">Medical Record Details</h2>
            <p class="text-gray-600">Viewing record ID: {{ id }}</p>
          </div>
          <Link 
            href="/medical-records" 
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Back to Records
          </Link>
        </div>
        
        <div class="bg-white shadow overflow-hidden rounded-lg">
          <div v-if="!record" class="py-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No medical record found</h3>
            <p class="mt-1 text-sm text-gray-500">The requested record doesn't exist or you don't have permission to view it.</p>
            <div class="mt-6">
              <Link 
                href="/medical-records" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Back to Records
              </Link>
            </div>
          </div>
          
          <div v-else>
            <div class="px-4 py-5 border-b border-gray-200">
              <h3 class="text-lg leading-6 font-medium text-gray-900">
                Medical Record Details
              </h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Patient: {{ patient?.name || 'Unknown' }} | Last Updated: {{ formatDate(record.updated_at) }}
              </p>
            </div>
            
            <dl>
              <div class="bg-gray-50 px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Patient Name</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ patient?.name || 'Unknown' }}</dd>
              </div>
              
              <div class="bg-white px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ formatDate(record.updated_at) }}</dd>
              </div>
              
              <div class="bg-gray-50 px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                <dd class="text-sm text-gray-900 col-span-2">
                  <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full" 
                        :class="{
                          'bg-yellow-100 text-yellow-800': currentStatus === 'Pending',
                          'bg-blue-100 text-blue-800': currentStatus === 'Attending',
                          'bg-green-100 text-green-800': currentStatus === 'Finalized',
                          'bg-orange-100 text-orange-800': currentStatus === 'Needs Follow-up',
                          'bg-gray-100 text-gray-800': currentStatus === 'No Status'
                        }">
                    {{ currentStatus }}
                  </span>
                </dd>
              </div>
              
              <div class="bg-white px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Symptoms</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ record.symptoms }}</dd>
              </div>
              
              <div class="bg-white px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ record.diagnosis || 'Not available' }}</dd>
              </div>
              
              <div class="bg-gray-50 px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ record.treatment || 'Not available' }}</dd>
              </div>
              
              <div class="bg-white px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ record.notes || 'Not available' }}</dd>
              </div>
              
              <div class="bg-gray-50 px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Anamnesis</dt>
                <dd class="text-sm text-gray-900 col-span-2">
                  <div v-if="anamnesis">
                    <p><strong>Blood Pressure:</strong> {{ anamnesis.blood_pressure || 'Not recorded' }}</p>
                    <p><strong>Heart Rate:</strong> {{ anamnesis.heart_rate || 'Not recorded' }} bpm</p>
                    <p><strong>Temperature:</strong> {{ anamnesis.temperature || 'Not recorded' }}°C</p>
                    <p><strong>Weight:</strong> {{ anamnesis.weight || 'Not recorded' }} kg</p>
                    <p><strong>Height:</strong> {{ anamnesis.height || 'Not recorded' }} cm</p>
                  </div>
                  <span v-else class="text-gray-500">No anamnesis data available</span>
                </dd>
              </div>
              
              <div class="bg-white px-4 py-5 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Status History</dt>
                <dd class="text-sm text-gray-900 col-span-2">
                  <div v-if="record.statuses && record.statuses.length > 0" class="space-y-2">
                    <div v-for="status in record.statuses" :key="status.id" class="flex justify-between items-center">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                            :class="{
                              'bg-yellow-100 text-yellow-800': status.name === 'Pending',
                              'bg-blue-100 text-blue-800': status.name === 'Attending',
                              'bg-green-100 text-green-800': status.name === 'Finalized',
                              'bg-orange-100 text-orange-800': status.name === 'Needs Follow-up'
                            }">
                        {{ status.name }}
                      </span>
                      <span class="text-xs text-gray-500">{{ formatDate(status.created_at) }}</span>
                    </div>
                  </div>
                  <span v-else class="text-gray-500">No status history available</span>
                </dd>
              </div>
            </dl>
            
            <div class="px-4 py-4 flex justify-end space-x-3">
              <Link 
                v-if="canStartConsultation"
                :href="`/medical-records/${record.id}/start-consultation`"
                method="post"
                as="button"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                Start Consultation
              </Link>
              <Link 
                v-if="isConsultationInProgress"
                :href="`/medical-records/${record.id}/consultation`"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                Continue Consultation
              </Link>
              <Link 
                :href="`/medical-records/${record.id}/edit`" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Edit Record
              </Link>
              <Link 
                href="/medical-records" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Back to Records
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

// Get data from Inertia props
const props = defineProps({
  id: {
    type: String,
    required: true
  },
  record: {
    type: Object,
    default: null
  },
  patient: {
    type: Object,
    default: null
  },
  anamnesis: {
    type: Object,
    default: null
  },
  permissions: {
    type: Object,
    default: () => ({})
  }
});

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Get current status
const currentStatus = computed(() => {
  return props.record?.statuses?.[0]?.name || 'No Status';
});

// Check if consultation can be started
const canStartConsultation = computed(() => {
  return currentStatus.value === 'Pending' && props.permissions.canStartConsultation;
});

// Check if consultation is in progress
const isConsultationInProgress = computed(() => {
  return currentStatus.value === 'Attending';
});

// Delete confirmation
const confirmDelete = () => {
  if (confirm('Are you sure you want to delete this medical record? This action cannot be undone.')) {
    // In a real app, would use Inertia.delete() here
    alert('Delete functionality to be implemented with Inertia');
  }
}
</script> 