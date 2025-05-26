<template>
  <MainLayout title="Create Patient">
    <div class="max-w-4xl mx-auto py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Patient</h1>
        <Link 
          href="/patients"
          class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Back to Patients
        </Link>
      </div>

      <!-- Form -->
      <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Patient Information</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Enter the patient's personal and contact information.</p>
        </div>
        
        <form @submit.prevent="submitForm" class="px-6 py-5 space-y-6">
          <!-- Basic Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
              <input 
                v-model="form.name" 
                type="text" 
                id="name"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.name }"
              />
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>
            
            <!-- Date of Birth -->
            <div>
              <label for="birth_date" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
              <input 
                v-model="form.birth_date" 
                type="date" 
                id="birth_date"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.birth_date }"
              />
              <p v-if="form.errors.birth_date" class="mt-1 text-sm text-red-600">{{ form.errors.birth_date }}</p>
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input 
                v-model="form.email" 
                type="email" 
                id="email"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.email }"
              />
              <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <!-- Phone -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
              <input 
                v-model="form.phone" 
                type="tel" 
                id="phone"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                :class="{ 'border-red-300': form.errors.phone }"
              />
              <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
            </div>
          </div>

          <!-- Address -->
          <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea 
              v-model="form.address" 
              id="address"
              rows="3"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
              :class="{ 'border-red-300': form.errors.address }"
            ></textarea>
            <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
          </div>

          <!-- Emergency Contact -->
          <div class="border-t pt-6">
            <h4 class="text-lg font-medium text-gray-700 mb-4">Emergency Contact (Optional)</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Emergency Contact Name -->
              <div>
                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                <input 
                  v-model="form.emergency_contact_name" 
                  type="text" 
                  id="emergency_contact_name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                  :class="{ 'border-red-300': form.errors.emergency_contact_name }"
                />
                <p v-if="form.errors.emergency_contact_name" class="mt-1 text-sm text-red-600">{{ form.errors.emergency_contact_name }}</p>
              </div>

              <!-- Emergency Contact Phone -->
              <div>
                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                <input 
                  v-model="form.emergency_contact_phone" 
                  type="tel" 
                  id="emergency_contact_phone"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                  :class="{ 'border-red-300': form.errors.emergency_contact_phone }"
                />
                <p v-if="form.errors.emergency_contact_phone" class="mt-1 text-sm text-red-600">{{ form.errors.emergency_contact_phone }}</p>
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex justify-end space-x-3 pt-6 border-t">
            <Link 
              href="/patients"
              class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancel
            </Link>
            <button 
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              {{ form.processing ? 'Creating...' : 'Create Patient' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

// Set up form using Inertia's useForm composable
const form = useForm({
  name: '',
  birth_date: '',
  email: '',
  phone: '',
  address: '',
  emergency_contact_name: '',
  emergency_contact_phone: ''
})

// Handle form submission
const submitForm = () => {
  form.post('/patients', {
    onSuccess: () => {
      // Redirect will be handled by the controller
    }
  })
}
</script> 