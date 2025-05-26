import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

export const useMedicalFormStore = defineStore('medicalForm', () => {
  // Form state with default values
  const currentStep = ref(1)
  const totalSteps = 3
  
  // Step 1: Patient data
  const patientsList = ref([])
  const patientId = ref(null)
  const patientName = ref('')
  const isExistingPatient = ref(true)
  const patientData = ref({
    name: '',
    email: '',
    phone: '',
    birth_date: '',
    address: ''
  })
  
  // Step 2: Anamnesis data
  const anamnesisData = ref({
    blood_pressure: '',
    temperature: '',
    heart_rate: '',
    respiratory_rate: '',
    weight: '',
    height: ''
  })
  
  // Step 3: Medical record data
  const medicalRecordData = ref({
    symptoms: '',
    diagnosis: '',
    treatment: '',
    notes: ''
  })
  
  // Form status
  const formStatus = ref({
    isSubmitting: false,
    success: false,
    error: null
  })
  
  // Computed
  const isFirstStep = computed(() => currentStep.value === 1)
  const isLastStep = computed(() => currentStep.value === totalSteps)
  const stepProgress = computed(() => Math.round((currentStep.value / totalSteps) * 100))
  
  // URL-based step management functions
  function updateStepInUrl(step) {
    const url = new URL(window.location)
    url.searchParams.set('step', step.toString())
    
    router.get(url.pathname + url.search, {}, {
      preserveState: true,
      preserveScroll: true,
      replace: true
    })
  }
  
  function getStepFromUrl() {
    const urlParams = new URLSearchParams(window.location.search)
    const step = parseInt(urlParams.get('step')) || 1
    return Math.max(1, Math.min(totalSteps, step))
  }
  
  function initializeStep(initialStep) {
    currentStep.value = initialStep || getStepFromUrl()
  }
  
  // Actions
  function goToNextStep() {
    if (currentStep.value < totalSteps) {
      const nextStep = currentStep.value + 1
      currentStep.value = nextStep
      updateStepInUrl(nextStep)
    }
  }
  
  function goToPrevStep() {
    if (currentStep.value > 1) {
      const prevStep = currentStep.value - 1
      currentStep.value = prevStep
      updateStepInUrl(prevStep)
    }
  }
  
  function goToStep(step) {
    if (step >= 1 && step <= totalSteps) {
      currentStep.value = step
      updateStepInUrl(step)
    }
  }
  
  function setPatients(patients) {
    patientsList.value = patients
  }
  
  function setPatient(patient) {
    if (!patient) return;
    patientId.value = patient.id
    patientName.value = patient.name || ''
    isExistingPatient.value = true
  }
  
  function setNewPatient() {
    patientId.value = null
    isExistingPatient.value = false
  }
  
  function updatePatientData(data) {
    patientData.value = { ...patientData.value, ...data }
  }
  
  function updateAnamnesisData(data) {
    anamnesisData.value = { ...anamnesisData.value, ...data }
  }
  
  function updateMedicalRecordData(data) {
    medicalRecordData.value = { ...medicalRecordData.value, ...data }
  }
  
  function resetForm() {
    currentStep.value = 1
    patientId.value = null
    patientName.value = ''
    isExistingPatient.value = true
    patientData.value = {
      name: '',
      email: '',
      phone: '',
      birth_date: '',
      address: ''
    }
    anamnesisData.value = {
      blood_pressure: '',
      temperature: '',
      heart_rate: '',
      respiratory_rate: '',
      weight: '',
      height: ''
    }
    medicalRecordData.value = {
      symptoms: '',
      diagnosis: '',
      treatment: '',
      notes: ''
    }
    formStatus.value = {
      isSubmitting: false,
      success: false,
      error: null
    }
    
    // Update URL to step 1
    updateStepInUrl(1)
    
    // Also clear localStorage when resetting the form
    clearState()
  }
  
  function setSubmitting(isSubmitting) {
    formStatus.value.isSubmitting = isSubmitting
  }
  
  function setSuccess(success) {
    formStatus.value.success = success
  }
  
  function setError(error) {
    formStatus.value.error = error
  }

  // Persist state in localStorage to survive page refreshes
  // Load initial state from localStorage if available
  function loadState() {
    try {
      const savedState = localStorage.getItem('medicalFormState')
      if (savedState) {
        const state = JSON.parse(savedState)
        // Don't override currentStep - it should come from URL
        patientId.value = state.patientId || null
        patientName.value = state.patientName || ''
        isExistingPatient.value = state.isExistingPatient !== undefined ? state.isExistingPatient : true
        
        if (state.patientData) patientData.value = state.patientData
        if (state.anamnesisData) anamnesisData.value = state.anamnesisData
        if (state.medicalRecordData) medicalRecordData.value = state.medicalRecordData
        if (state.patientsList && Array.isArray(state.patientsList)) patientsList.value = state.patientsList
      }
    } catch (error) {
      console.error('Error loading form state:', error)
      // In case of errors, reset to defaults for safety
      resetForm()
    }
  }
  
  function saveState() {
    const state = {
      // Don't save currentStep - it's managed by URL
      patientId: patientId.value,
      patientName: patientName.value,
      isExistingPatient: isExistingPatient.value,
      patientData: patientData.value,
      anamnesisData: anamnesisData.value,
      medicalRecordData: medicalRecordData.value,
      patientsList: patientsList.value
    }
    localStorage.setItem('medicalFormState', JSON.stringify(state))
  }
  
  function clearState() {
    localStorage.removeItem('medicalFormState')
  }
  
  // Do NOT auto-load saved state
  // Let components explicitly call loadState() when needed
  
  // Return everything needed outside the store
  return {
    // State
    currentStep,
    patientId,
    patientName,
    isExistingPatient,
    patientData,
    anamnesisData,
    medicalRecordData,
    formStatus,
    patientsList,
    
    // Computed
    isFirstStep,
    isLastStep,
    stepProgress,
    
    // Actions
    initializeStep,
    goToNextStep,
    goToPrevStep,
    goToStep,
    setPatients,
    setPatient,
    setNewPatient,
    updatePatientData,
    updateAnamnesisData,
    updateMedicalRecordData,
    resetForm,
    setSubmitting,
    setSuccess,
    setError,
    loadState,
    saveState,
    clearState
  }
}) 