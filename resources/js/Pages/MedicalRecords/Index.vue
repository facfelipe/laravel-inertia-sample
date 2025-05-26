<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { debounce } from 'lodash';

// Receive props from Inertia
const props = defineProps({
  medicalRecords: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    required: true
  },
  stats: {
    type: Object,
    required: true
  },
  availableStatuses: {
    type: Array,
    required: true
  }
});

// Reactive filter state
const localFilters = ref({
  patient_filter: props.filters.patient_filter || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  status: props.filters.status || '',
  sort_by: props.filters.sort_by || 'updated_at',
  sort_direction: props.filters.sort_direction || 'desc',
  per_page: props.filters.per_page || 10
});

// Loading state
const isLoading = ref(false);

// Computed properties
const hasRecords = computed(() => props.medicalRecords.data && props.medicalRecords.data.length > 0);
const hasFilters = computed(() => {
  return localFilters.value.patient_filter || 
         localFilters.value.date_from ||
         localFilters.value.date_to ||
         localFilters.value.status;
});

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

// Apply filters to the server
const applyFilters = debounce(() => {
  isLoading.value = true;
  
  // Build query parameters
  const params = {};
  
  Object.keys(localFilters.value).forEach(key => {
    if (localFilters.value[key]) {
      params[key] = localFilters.value[key];
    }
  });

  router.get('/medical-records', params, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      isLoading.value = false;
    }
  });
}, 300);

// Handle column sorting
const handleSort = (column) => {
  if (localFilters.value.sort_by === column) {
    // Toggle direction if same column
    localFilters.value.sort_direction = localFilters.value.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    // New column, default to desc
    localFilters.value.sort_by = column;
    localFilters.value.sort_direction = 'desc';
  }
  applyFilters();
};

// Get sort icon for column
const getSortIcon = (column) => {
  if (localFilters.value.sort_by !== column) {
    return '↕️'; // Unsorted
  }
  return localFilters.value.sort_direction === 'asc' ? '↑' : '↓';
};

// Clear all filters
const clearFilters = () => {
  localFilters.value = {
    patient_filter: '',
    date_from: '',
    date_to: '',
    status: '',
    sort_by: 'updated_at',
    sort_direction: 'desc',
    per_page: 10
  };
  applyFilters();
};

// Handle pagination
const goToPage = (page) => {
  if (page !== props.medicalRecords.current_page) {
    router.get('/medical-records', {
      ...localFilters.value,
      page: page
    }, {
      preserveState: true,
      preserveScroll: true
    });
  }
};

// Watch for filter changes and apply them
watch(() => localFilters.value.patient_filter, applyFilters);
watch(() => localFilters.value.date_from, applyFilters);
watch(() => localFilters.value.date_to, applyFilters);
watch(() => localFilters.value.status, applyFilters);
watch(() => localFilters.value.per_page, applyFilters);
</script>

<template>
  <MainLayout title="Medical Records">
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Medical Records</h1>
            <p class="text-gray-600">Manage and view all medical records</p>
          </div>
          <Link 
            href="/medical-form" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Record
          </Link>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
          <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
            <div class="text-center">
              <div class="mx-auto w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Records</p>
              <p class="text-2xl sm:text-4xl font-bold text-gray-900">{{ stats.total_records }}</p>
            </div>
          </div>

          <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
            <div class="text-center">
              <div class="mx-auto w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
              </div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">This Month</p>
              <p class="text-2xl sm:text-4xl font-bold text-gray-900">{{ stats.records_this_month }}</p>
            </div>
          </div>

          <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
            <div class="text-center">
              <div class="mx-auto w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Pending</p>
              <p class="text-2xl sm:text-4xl font-bold text-gray-900">{{ stats.status_counts?.Pending || 0 }}</p>
            </div>
          </div>

          <div class="bg-white p-4 sm:p-6 rounded-lg shadow border border-gray-200">
            <div class="text-center">
              <div class="mx-auto w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-3 sm:mb-4">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </div>
              <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Patients</p>
              <p class="text-2xl sm:text-4xl font-bold text-gray-900">{{ stats.total_patients }}</p>
            </div>
          </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow mb-6">
          <div class="p-6 border-b border-gray-200">
            <div class="flex flex-wrap gap-4 items-end">
              <!-- Patient Filter -->
              <div class="flex-1 min-w-64">
                <label for="patient_filter" class="block text-sm font-medium text-gray-700 mb-2">
                  Patient Name
                </label>
                <input
                  id="patient_filter"
                  v-model="localFilters.patient_filter"
                  type="text"
                  placeholder="Search by patient name..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <!-- Date Range -->
              <div class="min-w-40">
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">
                  Date From
                </label>
                <input
                  id="date_from"
                  v-model="localFilters.date_from"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <div class="min-w-40">
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">
                  Date To
                </label>
                <input
                  id="date_to"
                  v-model="localFilters.date_to"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
              </div>

              <!-- Status Filter -->
              <div class="flex-1 min-w-64">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                  Status
                </label>
                <select
                  id="status"
                  v-model="localFilters.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">All</option>
                  <option v-for="status in availableStatuses" :key="status" :value="status">{{ status }}</option>
                </select>
              </div>

              <!-- Clear Filters -->
              <div v-if="hasFilters">
                <button
                  @click="clearFilters"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                  Clear Filters
                </button>
              </div>
            </div>
          </div>

          <!-- Results Info and Per Page -->
          <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-700">
              Showing {{ medicalRecords.from || 0 }} to {{ medicalRecords.to || 0 }} of {{ medicalRecords.total || 0 }} results
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-700">
              <span>Show:</span>
              <select
                id="per_page"
                v-model="localFilters.per_page"
                class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 w-20"
              >
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
              </select>
              <span>per page</span>
            </div>
          </div>
        </div>

        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span class="text-gray-700">Loading...</span>
          </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div v-if="!hasRecords && !isLoading" class="py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No medical records found</h3>
            <p class="mt-1 text-sm text-gray-500">
              {{ hasFilters ? 'Try adjusting your patient name or date filters.' : 'Get started by creating a new medical record.' }}
            </p>
            <div class="mt-6">
              <Link 
                href="/medical-form" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700"
              >
                New Medical Record
              </Link>
            </div>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th 
                    @click="handleSort('updated_at')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                  >
                    <div class="flex items-center space-x-1">
                      <span>Last Updated</span>
                      <span class="text-gray-400">{{ getSortIcon('updated_at') }}</span>
                    </div>
                  </th>
                  <th 
                    @click="handleSort('patient_name')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                  >
                    <div class="flex items-center space-x-1">
                      <span>Patient</span>
                      <span class="text-gray-400">{{ getSortIcon('patient_name') }}</span>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th 
                    @click="handleSort('diagnosis')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                  >
                    <div class="flex items-center space-x-1">
                      <span>Diagnosis</span>
                      <span class="text-gray-400">{{ getSortIcon('diagnosis') }}</span>
                    </div>
                  </th>
                  <th 
                    @click="handleSort('symptoms')"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                  >
                    <div class="flex items-center space-x-1">
                      <span>Symptoms</span>
                      <span class="text-gray-400">{{ getSortIcon('symptoms') }}</span>
                    </div>
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="record in medicalRecords.data" :key="record.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(record.updated_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ record.patient?.name || 'Unknown' }}</div>
                    <div class="text-sm text-gray-500">{{ record.patient?.email || '' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                          :class="{
                            'bg-yellow-100 text-yellow-800': record.statuses?.[0]?.name === 'Pending',
                            'bg-blue-100 text-blue-800': record.statuses?.[0]?.name === 'Attending',
                            'bg-green-100 text-green-800': record.statuses?.[0]?.name === 'Finalized',
                            'bg-orange-100 text-orange-800': record.statuses?.[0]?.name === 'Needs Follow-up',
                            'bg-gray-100 text-gray-800': !record.statuses?.[0]?.name
                          }">
                      {{ record.statuses?.[0]?.name || 'No Status' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                          :class="record.diagnosis ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">
                      {{ record.diagnosis || 'Not diagnosed' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                    {{ record.symptoms || 'No symptoms recorded' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-3">
                      <Link :href="`/medical-records/${record.id}`" class="text-blue-600 hover:text-blue-900 transition-colors">
                        View
                      </Link>
                      <Link 
                        v-if="record.statuses?.[0]?.name === 'Pending' || !record.statuses?.[0]?.name"
                        :href="`/medical-records/${record.id}/start-consultation`"
                        method="post"
                        as="button"
                        class="text-green-600 hover:text-green-900 transition-colors"
                      >
                        Start Consultation
                      </Link>
                      <Link 
                        v-if="record.statuses?.[0]?.name === 'Attending'"
                        :href="`/medical-records/${record.id}/consultation`"
                        class="text-purple-600 hover:text-purple-900 transition-colors"
                      >
                        Continue Consultation
                      </Link>
                      <Link :href="`/medical-records/${record.id}/edit`" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                        Edit
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="hasRecords && medicalRecords.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <button
                  @click="goToPage(medicalRecords.current_page - 1)"
                  :disabled="!medicalRecords.prev_page_url"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Previous
                </button>
                <button
                  @click="goToPage(medicalRecords.current_page + 1)"
                  :disabled="!medicalRecords.next_page_url"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Next
                </button>
              </div>
              
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing page <span class="font-medium">{{ medicalRecords.current_page }}</span> of 
                    <span class="font-medium">{{ medicalRecords.last_page }}</span>
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <!-- Previous Button -->
                    <button
                      @click="goToPage(medicalRecords.current_page - 1)"
                      :disabled="!medicalRecords.prev_page_url"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>

                    <!-- Page Numbers -->
                    <template v-for="page in medicalRecords.last_page" :key="page">
                      <button
                        v-if="Math.abs(page - medicalRecords.current_page) <= 2 || page === 1 || page === medicalRecords.last_page"
                        @click="goToPage(page)"
                        :class="[
                          'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                          page === medicalRecords.current_page
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        ]"
                      >
                        {{ page }}
                      </button>
                      <span
                        v-else-if="Math.abs(page - medicalRecords.current_page) === 3"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                      >
                        ...
                      </span>
                    </template>

                    <!-- Next Button -->
                    <button
                      @click="goToPage(medicalRecords.current_page + 1)"
                      :disabled="!medicalRecords.next_page_url"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </nav>
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