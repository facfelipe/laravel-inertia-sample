// Form validation utility

// Step 1 - Patient Selection/Creation validation
export const validatePatientSelection = (formData) => {
  const errors = {}
  
  if (formData.isExistingPatient) {
    if (!formData.patientId) {
      errors.patientId = 'Please select a patient'
    }
  } else {
    // New patient validation
    if (!formData.patientData.name.trim()) {
      errors.name = 'Patient name is required'
    } else if (formData.patientData.name.length > 255) {
      errors.name = 'Name cannot exceed 255 characters'
    }
    
    if (!formData.patientData.email.trim()) {
      errors.email = 'Email is required'
    } else if (!isValidEmail(formData.patientData.email)) {
      errors.email = 'Please enter a valid email address'
    } else if (formData.patientData.email.length > 255) {
      errors.email = 'Email cannot exceed 255 characters'
    }
    
    // Phone is optional in the backend
    if (formData.patientData.phone && formData.patientData.phone.length > 20) {
      errors.phone = 'Phone number cannot exceed 20 characters'
    }
    
    if (!formData.patientData.birth_date) {
      errors.birth_date = 'Birth date is required'
    }
    
    // Address is optional in the backend
    if (formData.patientData.address && formData.patientData.address.length > 255) {
      errors.address = 'Address cannot exceed 255 characters'
    }
  }
  
  return { isValid: Object.keys(errors).length === 0, errors }
}

// Step 2 - Anamnesis validation
export const validateAnamnesis = (anamnesisData) => {
  const errors = {}
  
  // Blood pressure format: systolic/diastolic (e.g., 120/80)
  if (anamnesisData.blood_pressure && typeof anamnesisData.blood_pressure === 'string' && 
      anamnesisData.blood_pressure.trim() && !isValidBloodPressure(anamnesisData.blood_pressure)) {
    errors.blood_pressure = 'Blood pressure should be in format: 120/80'
  }
  
  // Temperature must be a number between 35-42°C
  if (anamnesisData.temperature !== null && anamnesisData.temperature !== undefined && 
      anamnesisData.temperature !== '' && (isNaN(anamnesisData.temperature) || 
      anamnesisData.temperature < 35 || anamnesisData.temperature > 42)) {
    errors.temperature = 'Temperature should be between 35-42°C'
  }
  
  // Heart rate must be a number between 40-200 bpm
  if (anamnesisData.heart_rate !== null && anamnesisData.heart_rate !== undefined && 
      anamnesisData.heart_rate !== '' && (isNaN(anamnesisData.heart_rate) || 
      anamnesisData.heart_rate < 40 || anamnesisData.heart_rate > 200)) {
    errors.heart_rate = 'Heart rate should be between 40-200 bpm'
  }
  
  // Respiratory rate must be a number between 8-40 breaths per minute
  if (anamnesisData.respiratory_rate !== null && anamnesisData.respiratory_rate !== undefined && 
      anamnesisData.respiratory_rate !== '' && (isNaN(anamnesisData.respiratory_rate) || 
      anamnesisData.respiratory_rate < 8 || anamnesisData.respiratory_rate > 40)) {
    errors.respiratory_rate = 'Respiratory rate should be between 8-40 breaths per minute'
  }
  
  // Weight must be a number (in kg)
  if (anamnesisData.weight !== null && anamnesisData.weight !== undefined && 
      anamnesisData.weight !== '' && (isNaN(anamnesisData.weight) || 
      anamnesisData.weight < 0 || anamnesisData.weight > 500)) {
    errors.weight = 'Weight should be between 0-500 kg'
  }
  
  // Height must be a number (in cm)
  if (anamnesisData.height !== null && anamnesisData.height !== undefined && 
      anamnesisData.height !== '' && (isNaN(anamnesisData.height) || 
      anamnesisData.height < 50 || anamnesisData.height > 250)) {
    errors.height = 'Height should be between 50-250 cm'
  }
  
  return { isValid: Object.keys(errors).length === 0, errors }
}

// Step 3 - Medical record validation
export const validateMedicalRecord = (medicalRecordData) => {
  const errors = {}
  
  if (!medicalRecordData.symptoms) {
    errors.symptoms = 'Symptoms are required'
  }
  
  return {
    isValid: Object.keys(errors).length === 0,
    errors
  }
}

// Helper functions
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

function isValidBloodPressure(bp) {
  // Check format: numbers/numbers
  const re = /^\d+\/\d+$/
  if (!re.test(bp)) return false
  
  // Check ranges
  const [systolic, diastolic] = bp.split('/').map(Number)
  return systolic >= 70 && systolic <= 220 && diastolic >= 40 && diastolic <= 130
} 