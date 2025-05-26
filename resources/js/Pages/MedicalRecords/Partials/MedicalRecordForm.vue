<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  medicalRecord: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['update:medicalRecord', 'update:patient-id']);

const patients = ref([]);
const loading = ref(true);

// Load patients list on component mount
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

// Update patient_id and emit for parent to sync
const updatePatientId = (patientId) => {
  const updatedRecord = { ...props.medicalRecord, patient_id: patientId };
  emit('update:medicalRecord', updatedRecord);
  emit('update:patient-id', patientId);
};

// Update any field in the medical record
const updateField = (field, value) => {
  const updatedRecord = { ...props.medicalRecord, [field]: value };
  emit('update:medicalRecord', updatedRecord);
};
</script>

<template>
  <div>
    <!-- Patient Selection Section -->
    <div class="mb-6">
      <label for="mr_patient_id" class="block text-sm font-medium text-gray-700 mb-1">Select Patient</label>
      <select
        id="mr_patient_id"
        :value="medicalRecord.patient_id"
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
    
    <!-- Symptoms -->
    <div class="mb-6">
      <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-1">Symptoms</label>
      <textarea
        id="symptoms"
        :value="medicalRecord.symptoms"
        @input="updateField('symptoms', $event.target.value)"
        placeholder="Describe the patient's symptoms"
        rows="3"
        class="w-full border border-gray-300 rounded p-2"
      ></textarea>
    </div>
    
    <!-- Diagnosis -->
    <div class="mb-6">
      <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
      <textarea
        id="diagnosis"
        :value="medicalRecord.diagnosis"
        @input="updateField('diagnosis', $event.target.value)"
        placeholder="Enter the diagnosis"
        rows="3"
        class="w-full border border-gray-300 rounded p-2"
      ></textarea>
    </div>
    
    <!-- Treatment -->
    <div class="mb-6">
      <label for="treatment" class="block text-sm font-medium text-gray-700 mb-1">Treatment Plan</label>
      <textarea
        id="treatment"
        :value="medicalRecord.treatment"
        @input="updateField('treatment', $event.target.value)"
        placeholder="Describe the treatment plan"
        rows="3"
        class="w-full border border-gray-300 rounded p-2"
      ></textarea>
    </div>
    
    <!-- Additional Notes -->
    <div class="mb-6">
      <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
      <textarea
        id="notes"
        :value="medicalRecord.notes"
        @input="updateField('notes', $event.target.value)"
        placeholder="Any additional notes or follow-up instructions"
        rows="3"
        class="w-full border border-gray-300 rounded p-2"
      ></textarea>
    </div>
  </div>
</template> 