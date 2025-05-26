<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

// Receive props from Inertia
const props = defineProps({
  patients: {
    type: Array,
    default: () => []
  }
});

const searchQuery = ref('');
const filteredPatients = computed(() => {
  if (!searchQuery.value.trim()) {
    return props.patients;
  }
  
  const query = searchQuery.value.toLowerCase();
  return props.patients.filter(patient => {
    return (
      (patient.name && patient.name.toLowerCase().includes(query)) ||
      (patient.email && patient.email.toLowerCase().includes(query)) ||
      (patient.phone && patient.phone.includes(query))
    );
  });
});

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString();
};

const deletePatient = (patient) => {
  if (confirm(`Are you sure you want to delete ${patient.name}? This action cannot be undone.`)) {
    router.delete(`/patients/${patient.id}`, {
      onSuccess: () => {
        // Success message will be handled by the flash message system
      },
      onError: () => {
        alert('Failed to delete patient. Please try again.');
      }
    });
  }
};
</script>

<template>
  <MainLayout title="Patients">
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-800">All Patients</h2>
          <Link 
            href="/patients/create" 
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Add New Patient
          </Link>
        </div>
        
        <div class="mb-4">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search patients..."
            class="px-4 py-2 border border-gray-300 rounded-md w-full focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
          <div v-if="props.patients.length === 0" class="py-8 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No patients</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a new patient.</p>
            <div class="mt-6">
              <Link 
                href="/patients/create" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Add Patient
              </Link>
            </div>
          </div>
          
          <ul v-else class="divide-y divide-gray-200">
            <li v-for="patient in filteredPatients" :key="patient.id" class="px-6 py-4 hover:bg-gray-50">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-blue-600">{{ patient.name }}</p>
                  <p class="mt-1 text-sm text-gray-500">{{ patient.email }}</p>
                  <p class="mt-1 text-sm text-gray-500">{{ patient.phone }}</p>
                </div>
                <div class="flex space-x-2">
                  <Link :href="`/patients/${patient.id}`" class="text-blue-600 hover:text-blue-800 text-sm">View</Link>
                  <Link :href="`/patients/${patient.id}/edit`" class="text-gray-600 hover:text-gray-800 text-sm">Edit</Link>
                  <button class="text-red-500 hover:underline text-sm" @click="deletePatient(patient)">Delete</button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </MainLayout>
</template> 