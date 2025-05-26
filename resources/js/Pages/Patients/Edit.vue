<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';

// Receive props from Inertia
const props = defineProps({
  patient: {
    type: Object,
    required: true
  }
});

// Create form using Inertia's useForm
const form = useForm({
  name: props.patient.name || '',
  email: props.patient.email || '',
  phone: props.patient.phone || '',
  date_of_birth: props.patient.date_of_birth || '',
  gender: props.patient.gender || '',
  address: props.patient.address || '',
  emergency_contact: props.patient.emergency_contact || ''
});

const submit = () => {
  form.put(`/patients/${props.patient.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      // Handle success if needed
    }
  });
};
</script>

<template>
  <MainLayout :title="`Edit Patient: ${patient.name}`">
    <div class="py-8">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <!-- Page Header -->
            <div class="flex justify-between items-center mb-6">
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Patient</h1>
                <p class="text-gray-600">Update patient information</p>
              </div>
              <div class="flex space-x-3">
                <Link 
                  :href="`/patients/${patient.id}`"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Back to Patient
                </Link>
                <Link 
                  href="/patients"
                  class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                >
                  All Patients
                </Link>
              </div>
            </div>

            <!-- Edit Form -->
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 gap-6">
                <!-- Basic Information Section -->
                <div class="bg-gray-50 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                  <div class="grid grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                      </label>
                      <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter patient's full name"
                      />
                      <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                    </div>

                    <!-- Email -->
                    <div>
                      <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                      </label>
                      <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="patient@example.com"
                      />
                      <div v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</div>
                    </div>

                    <!-- Phone -->
                    <div>
                      <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number
                      </label>
                      <input
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="(555) 123-4567"
                      />
                      <div v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                      <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth
                      </label>
                      <input
                        id="date_of_birth"
                        v-model="form.date_of_birth"
                        type="date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      />
                      <div v-if="form.errors.date_of_birth" class="mt-1 text-sm text-red-600">{{ form.errors.date_of_birth }}</div>
                    </div>
                  </div>
                </div>

                <!-- Additional Information Section -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                  <div class="grid grid-cols-1 gap-6">
                    <!-- Gender -->
                    <div>
                      <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender
                      </label>
                      <select
                        id="gender"
                        v-model="form.gender"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">Select gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                        <option value="Prefer not to say">Prefer not to say</option>
                      </select>
                      <div v-if="form.errors.gender" class="mt-1 text-sm text-red-600">{{ form.errors.gender }}</div>
                    </div>

                    <!-- Address -->
                    <div>
                      <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address
                      </label>
                      <textarea
                        id="address"
                        v-model="form.address"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter complete address"
                      ></textarea>
                      <div v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</div>
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                      <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Emergency Contact
                      </label>
                      <input
                        id="emergency_contact"
                        v-model="form.emergency_contact"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Emergency contact name and phone"
                      />
                      <div v-if="form.errors.emergency_contact" class="mt-1 text-sm text-red-600">{{ form.errors.emergency_contact }}</div>
                    </div>
                  </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6">
                  <Link
                    :href="`/patients/${patient.id}`"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                  >
                    Cancel
                  </Link>
                  <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                  >
                    <span v-if="form.processing">Updating...</span>
                    <span v-else>Update Patient</span>
                  </button>
                </div>
              </div>
            </form>
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