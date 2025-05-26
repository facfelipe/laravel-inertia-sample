<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
  record: {
    type: Object,
    required: true
  },
  patient: {
    type: Object,
    required: true
  },
  anamnesis: {
    type: Object,
    default: null
  },
  statusHistory: {
    type: Array,
    default: () => []
  },
  availableStatuses: {
    type: Array,
    required: true
  }
});

// Form for consultation
const form = useForm({
  diagnosis: props.record.diagnosis || '',
  treatment: props.record.treatment || '',
  notes: props.record.notes || '',
  status: 'Finalized'
});

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Get current status
const currentStatus = computed(() => {
  return props.statusHistory[0]?.name || 'No Status';
});

// Submit consultation
const submitConsultation = () => {
  form.put(`/medical-records/${props.record.id}/consultation`, {
    preserveScroll: true
  });
};
</script>

<template>
  <MainLayout title="Consultation">
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Medical Consultation</h1>
            <p class="text-gray-600">Complete diagnosis and treatment for {{ patient.name }}</p>
          </div>
          <Link 
            :href="`/medical-records/${record.id}`"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-gray-700"
          >
            Back to Record
          </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Patient Information -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>
              <div class="space-y-3">
                <div>
                  <span class="text-sm font-medium text-gray-500">Name:</span>
                  <p class="text-sm text-gray-900">{{ patient.name }}</p>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-500">Email:</span>
                  <p class="text-sm text-gray-900">{{ patient.email }}</p>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-500">Phone:</span>
                  <p class="text-sm text-gray-900">{{ patient.phone || 'N/A' }}</p>
                </div>
                <div>
                  <span class="text-sm font-medium text-gray-500">Date of Birth:</span>
                  <p class="text-sm text-gray-900">{{ formatDate(patient.date_of_birth) }}</p>
                </div>
              </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Current Status</h3>
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
            </div>

            <!-- Status History -->
            <div class="bg-white rounded-lg shadow p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Status History</h3>
              <div class="space-y-3">
                <div v-for="status in statusHistory" :key="status.id" class="flex justify-between items-center">
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
            </div>
          </div>

          <!-- Main Content -->
          <div class="lg:col-span-2">
            <!-- Symptoms and Anamnesis -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Patient Information</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Symptoms</h4>
                  <div class="bg-gray-50 rounded-md p-3">
                    <p class="text-sm text-gray-900">{{ record.symptoms || 'No symptoms recorded' }}</p>
                  </div>
                </div>

                <div v-if="anamnesis">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Medical History</h4>
                  <div class="bg-gray-50 rounded-md p-3 space-y-2">
                    <div v-if="anamnesis.allergies">
                      <span class="text-xs font-medium text-gray-500">Allergies:</span>
                      <p class="text-sm text-gray-900">{{ anamnesis.allergies }}</p>
                    </div>
                    <div v-if="anamnesis.medications">
                      <span class="text-xs font-medium text-gray-500">Current Medications:</span>
                      <p class="text-sm text-gray-900">{{ anamnesis.medications }}</p>
                    </div>
                    <div v-if="anamnesis.medical_history">
                      <span class="text-xs font-medium text-gray-500">Medical History:</span>
                      <p class="text-sm text-gray-900">{{ anamnesis.medical_history }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Consultation Form -->
            <div class="bg-white rounded-lg shadow p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Consultation Details</h3>
              
              <form @submit.prevent="submitConsultation" class="space-y-6">
                <!-- Diagnosis -->
                <div>
                  <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                    Diagnosis <span class="text-red-500">*</span>
                  </label>
                  <textarea
                    id="diagnosis"
                    v-model="form.diagnosis"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.diagnosis }"
                    placeholder="Enter diagnosis..."
                    required
                  ></textarea>
                  <div v-if="form.errors.diagnosis" class="mt-1 text-sm text-red-600">
                    {{ form.errors.diagnosis }}
                  </div>
                </div>

                <!-- Treatment -->
                <div>
                  <label for="treatment" class="block text-sm font-medium text-gray-700 mb-2">
                    Treatment <span class="text-red-500">*</span>
                  </label>
                  <textarea
                    id="treatment"
                    v-model="form.treatment"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.treatment }"
                    placeholder="Enter treatment plan..."
                    required
                  ></textarea>
                  <div v-if="form.errors.treatment" class="mt-1 text-sm text-red-600">
                    {{ form.errors.treatment }}
                  </div>
                </div>

                <!-- Notes -->
                <div>
                  <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional Notes
                  </label>
                  <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter any additional notes..."
                  ></textarea>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Final Status <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.status }"
                    required
                  >
                    <option v-for="status in availableStatuses" :key="status" :value="status">
                      {{ status }}
                    </option>
                  </select>
                  <div v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                    {{ form.errors.status }}
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                  <Link 
                    :href="`/medical-records/${record.id}`"
                    class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Cancel
                  </Link>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                  >
                    <span v-if="form.processing">Saving...</span>
                    <span v-else>Complete Consultation</span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template> 