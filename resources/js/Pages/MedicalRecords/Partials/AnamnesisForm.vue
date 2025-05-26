<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const props = defineProps({
  anamnesis: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['update:anamnesis', 'update:patient-id']);

const patients = ref([]);
const loading = ref(true);

// Load patients on component mount
onMounted(async () => {
  try {
    const response = await axios.get('/api/patients');
    patients.value = response.data.data;
  } catch (error) {
    console.error('Error fetching patients:', error);
  } finally {
    loading.value = false;
  }
});

// Update patient_id in both anamnesis and emit for parent to sync
const updatePatientId = (patientId) => {
  const updatedAnamnesis = { ...props.anamnesis, patient_id: patientId };
  emit('update:anamnesis', updatedAnamnesis);
  emit('update:patient-id', patientId);
};

// Update other anamnesis fields
const updateField = (field, value) => {
  const updatedAnamnesis = { ...props.anamnesis, [field]: value };
  emit('update:anamnesis', updatedAnamnesis);
};

// Calculate BMI if height and weight are provided
const bmi = computed(() => {
  if (props.anamnesis.height && props.anamnesis.weight) {
    const heightInMeters = props.anamnesis.height / 100; // convert cm to m
    return (props.anamnesis.weight / (heightInMeters * heightInMeters)).toFixed(1);
  }
  return null;
});

// Determine BMI category
const bmiCategory = computed(() => {
  if (!bmi.value) return null;
  
  const bmiValue = parseFloat(bmi.value);
  if (bmiValue < 18.5) return { category: 'Underweight', color: 'text-blue-500' };
  if (bmiValue < 25) return { category: 'Normal', color: 'text-green-500' };
  if (bmiValue < 30) return { category: 'Overweight', color: 'text-yellow-500' };
  return { category: 'Obese', color: 'text-red-500' };
});
</script>

<template>
  <div>
    <!-- Patient Selection Section -->
    <div class="mb-6">
      <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Select Patient</label>
      <select
        id="patient_id"
        :value="anamnesis.patient_id"
        @change="updatePatientId($event.target.value)"
        class="w-full border border-gray-300 rounded p-2"
        :disabled="loading"
      >
        <option value="" disabled selected>{{ loading ? 'Loading patients...' : 'Select a patient' }}</option>
        <option v-for="patient in patients" :key="patient.id" :value="patient.id">
          {{ patient.name }}
        </option>
      </select>
    </div>
    
    <!-- Vital Signs Section -->
    <div class="mb-6">
      <h3 class="text-lg font-medium text-gray-700 mb-3">Vital Signs</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="blood_pressure" class="block text-sm font-medium text-gray-700 mb-1">Blood Pressure (mmHg)</label>
          <input
            id="blood_pressure"
            type="text"
            :value="anamnesis.blood_pressure"
            @input="updateField('blood_pressure', $event.target.value)"
            placeholder="e.g., 120/80"
            class="w-full border border-gray-300 rounded p-2"
          />
        </div>
        
        <div>
          <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">Temperature (°C)</label>
          <input
            id="temperature"
            type="number"
            step="0.1"
            :value="anamnesis.temperature"
            @input="updateField('temperature', $event.target.value)"
            placeholder="e.g., 37.0"
            class="w-full border border-gray-300 rounded p-2"
          />
        </div>
        
        <div>
          <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-1">Heart Rate (bpm)</label>
          <input
            id="heart_rate"
            type="number"
            :value="anamnesis.heart_rate"
            @input="updateField('heart_rate', $event.target.value)"
            placeholder="e.g., 75"
            class="w-full border border-gray-300 rounded p-2"
          />
        </div>
        
        <div>
          <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-1">Respiratory Rate (breaths/min)</label>
          <input
            id="respiratory_rate"
            type="number"
            :value="anamnesis.respiratory_rate"
            @input="updateField('respiratory_rate', $event.target.value)"
            placeholder="e.g., 16"
            class="w-full border border-gray-300 rounded p-2"
          />
        </div>
      </div>
    </div>
    
    <!-- Body Measurements Section -->
    <div class="mb-6">
      <h3 class="text-lg font-medium text-gray-700 mb-3">Body Measurements</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
          <input
            id="weight"
            type="number"
            step="0.1"
            :value="anamnesis.weight"
            @input="updateField('weight', $event.target.value)"
            placeholder="e.g., 70.5"
            class="w-full border border-gray-300 rounded p-2"
          />
        </div>
        
        <div>
          <label for="height" class="block text-sm font-medium text-gray-700 mb-1">Height (cm)</label>
          <input
            id="height"
            type="number"
            :value="anamnesis.height"
            @input="updateField('height', $event.target.value)"
            placeholder="e.g., 175"
            class="w-full border border-gray-300 rounded p-2"
          />
        </div>
      </div>
      
      <!-- BMI Calculation -->
      <div v-if="bmi" class="mt-3 p-3 bg-gray-50 rounded">
        <div class="flex justify-between items-center">
          <span class="text-sm font-medium">BMI Calculation:</span>
          <span class="font-bold" :class="bmiCategory.color">{{ bmi }} ({{ bmiCategory.category }})</span>
        </div>
      </div>
    </div>
  </div>
</template> 