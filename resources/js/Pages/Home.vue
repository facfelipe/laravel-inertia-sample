<template>
  <MainLayout>
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Hero Section -->
            <div class="mb-8">
              <h1 class="text-2xl font-bold mb-4 text-gray-900">Dashboard</h1>
              <p class="mb-5 text-gray-700">A comprehensive solution for managing patient medical information.</p>
              <Link 
                href="/medical-form" 
                class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
              >
                Create New Record
              </Link>
            </div>

            <!-- Feature Cards - Smaller and side by side -->
            <div class="grid grid-cols-3 gap-6 mb-8">
              <div class="p-4 bg-gradient-to-r from-blue-50 to-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-center mb-2">
                  <div class="flex-shrink-0 w-8 h-8 text-blue-500 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 18a8 8 0 1 1 8-8 8.009 8.009 0 0 1-8 8Z"/>
                      <path d="M10 12.5a1 1 0 0 0-1 1v1a1 1 0 0 0 2 0v-1a1 1 0 0 0-1-1Zm0-1.5a1.5 1.5 0 1 0-1.5-1.5A1.5 1.5 0 0 0 10 11Z"/>
                    </svg>
                  </div>
                  <h5 class="ml-2 text-base font-semibold text-gray-900">Patient Records</h5>
                </div>
                <p class="text-sm text-gray-600">Manage patient health information in one place.</p>
              </div>
              
              <div class="p-4 bg-gradient-to-r from-green-50 to-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-center mb-2">
                  <div class="flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/>
                      <path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/>
                      <path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .49-.263l6.118-6.117a2.884 2.884 0 0 0-4.079-4.078l-6.117 6.117a.96.96 0 0 0-.263.491l-.679 3.4A.961.961 0 0 0 8.961 16Zm7.477-9.8a.958.958 0 0 1 .68-.281.961.961 0 0 1 .682 1.644l-.315.315-1.36-1.36.313-.318Zm-5.911 5.911 4.236-4.236 1.359 1.359-4.236 4.237-1.7.339.341-1.699Z"/>
                    </svg>
                  </div>
                  <h5 class="ml-2 text-base font-semibold text-gray-900">Medical Forms</h5>
                </div>
                <p class="text-sm text-gray-600">Multi-step forms for capturing medical data.</p>
              </div>
              
              <div class="p-4 bg-gradient-to-r from-purple-50 to-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-center mb-2">
                  <div class="flex-shrink-0 w-8 h-8 text-purple-500 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm14-7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm-5-4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm-5-4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4Z"/>
                    </svg>
                  </div>
                  <h5 class="ml-2 text-base font-semibold text-gray-900">Medical History</h5>
                </div>
                <p class="text-sm text-gray-600">Track and review patient medical history.</p>
              </div>
            </div>

            <!-- Medical Records Table -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
              <div class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-blue-100 to-white">
                <h2 class="text-lg font-semibold text-gray-900">Recent Medical Records</h2>
                <Link href="/medical-records" class="text-blue-600 hover:underline">View All</Link>
              </div>
              
              <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3">Last Updated</th>
                    <th scope="col" class="px-6 py-3">Patient</th>
                    <th scope="col" class="px-6 py-3">Diagnosis</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="!medicalRecords || medicalRecords.length === 0" class="bg-white border-b">
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                      No medical records found
                    </td>
                  </tr>
                  <tr v-for="record in displayedRecords" :key="record.id" class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4">{{ formatDate(record.updated_at) }}</td>
                    <td class="px-6 py-4">{{ record.patient?.name || 'Unknown' }}</td>
                    <td class="px-6 py-4">
                      <span :class="getSeverityClass(record)" class="px-2 py-1 text-xs rounded-full">
                        {{ record.diagnosis || 'Not diagnosed' }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex space-x-2">
                        <Link :href="`/medical-records/${record.id}`" class="text-blue-600 hover:underline">View</Link>
                        <Link :href="`/medical-records/${record.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              
              <div class="p-4 border-t bg-gray-50">
                <Link 
                  href="/medical-form" 
                  class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-4 py-2 text-center inline-flex items-center"
                >
                  Create New Medical Record
                  <svg class="w-3.5 h-3.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                  </svg>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import MainLayout from '@/Layouts/MainLayout.vue';

// Define props to receive medical records from the controller
const props = defineProps({
  medicalRecords: {
    type: Array,
    default: () => []
  }
});

// Display only the latest 5 records
const displayedRecords = computed(() => {
  return (props.medicalRecords || [])
    .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at))
    .slice(0, 5);
});

// Helper functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const getSeverityClass = (record) => {
  // Determine severity based on diagnosis or symptoms
  const text = ((record.symptoms || '') + ' ' + (record.diagnosis || '')).toLowerCase();
  
  if (text.includes('emergency') || text.includes('severe') || text.includes('critical')) {
    return 'bg-red-100 text-red-800';
  } else if (text.includes('moderate') || text.includes('concerning')) {
    return 'bg-yellow-100 text-yellow-800';
  } else {
    return 'bg-green-100 text-green-800';
  }
};
</script>