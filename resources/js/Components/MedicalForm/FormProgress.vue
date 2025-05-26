<template>
  <ol class="flex items-center w-full mb-4">
    <li v-for="(step, index) in steps" :key="step.id" 
        class="flex items-center" 
        :class="[index !== steps.length - 1 ? 'w-full' : '', 
               currentStep >= step.id ? 'text-blue-600' : 'text-gray-500']">
      <button
        @click="navigateToStep(step.id)"
        :disabled="!canNavigateToStep(step.id)"
        class="flex items-center justify-center w-8 h-8 rounded-full transition-colors duration-200"
        :class="[
          currentStep > step.id ? 'bg-blue-100 hover:bg-blue-200 cursor-pointer' : '',
          currentStep === step.id ? 'bg-blue-600 text-white cursor-default' : '',
          currentStep < step.id ? 'bg-gray-100 cursor-not-allowed' : '',
          canNavigateToStep(step.id) && currentStep !== step.id ? 'hover:bg-blue-50' : ''
        ]"
        :title="getStepTooltip(step)"
      >
        <span v-if="currentStep <= step.id">{{ step.id }}</span>
        <svg v-else class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
        </svg>
      </button>
      <span 
        class="ml-2 select-none"
        :class="canNavigateToStep(step.id) && currentStep !== step.id ? 'cursor-pointer' : ''"
        @click="navigateToStep(step.id)"
      >
        {{ step.label }}
      </span>
      <div v-if="index !== steps.length - 1" class="w-full bg-gray-200 h-0.5 mx-2">
        <div class="bg-blue-600 h-0.5 transition-all duration-300" :style="{width: getProgressWidth(step.id)}"></div>
      </div>
    </li>
  </ol>
</template>

<script setup>
const props = defineProps({
  currentStep: {
    type: Number,
    required: true
  },
  progress: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['navigate-to-step'])

const steps = [
  { 
    id: 1, 
    label: 'Patient'
  },
  { 
    id: 2, 
    label: 'Anamnesis'
  },
  { 
    id: 3, 
    label: 'Medical Record'
  }
]

function getProgressWidth(stepId) {
  if (props.currentStep > stepId) {
    return '100%'
  } else if (props.currentStep === stepId) {
    return `${props.progress}%`
  }
  return '0%'
}

function canNavigateToStep(stepId) {
  // Can navigate to completed steps and current step
  return stepId <= props.currentStep
}

function navigateToStep(stepId) {
  if (canNavigateToStep(stepId) && stepId !== props.currentStep) {
    emit('navigate-to-step', stepId)
  }
}

function getStepTooltip(step) {
  if (step.id < props.currentStep) {
    return `Go back to ${step.label}`
  } else if (step.id === props.currentStep) {
    return `Current step: ${step.label}`
  } else {
    return `Complete previous steps to access ${step.label}`
  }
}
</script> 