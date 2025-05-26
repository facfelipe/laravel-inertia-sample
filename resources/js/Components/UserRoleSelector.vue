<template>
  <div class="relative">
    <!-- Current User Display -->
    <button
      @click="toggleDropdown"
      class="flex items-center space-x-2 text-white bg-blue-700 hover:bg-blue-800 px-3 py-2 rounded-md text-sm font-medium transition-colors"
      :class="{ 'bg-blue-800': isOpen }"
    >
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 rounded-full" :class="currentUser?.role === 'doctor' ? 'bg-green-400' : 'bg-yellow-400'"></div>
        <span>{{ currentUser?.name || 'Loading...' }}</span>
        <span class="text-xs opacity-75">({{ currentUser?.role || '' }})</span>
      </div>
      <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-show="isOpen"
      class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-50 border border-gray-200"
      @click.stop
    >
      <div class="py-1">
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide border-b">
          Switch User Role
        </div>
        
        <div
          v-for="user in availableUsers"
          :key="user.id"
          @click="switchUser(user)"
          class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors"
          :class="{ 'bg-blue-50 border-l-4 border-blue-500': user.id === currentUser?.id }"
        >
          <div class="flex items-center space-x-3 flex-1">
            <div class="w-3 h-3 rounded-full" :class="user.role === 'doctor' ? 'bg-green-500' : 'bg-yellow-500'"></div>
            <div class="flex-1">
              <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              <div class="text-xs text-gray-500">{{ user.email }}</div>
            </div>
            <div class="text-xs font-medium px-2 py-1 rounded-full" 
                 :class="user.role === 'doctor' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
              {{ user.role }}
            </div>
          </div>
          
          <div v-if="user.id === currentUser?.id" class="ml-2">
            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Backdrop -->
    <div
      v-show="isOpen"
      @click="closeDropdown"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isOpen = ref(false);
const currentUser = ref(null);
const availableUsers = ref([]);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
  isOpen.value = false;
};

const switchUser = async (user) => {
  if (user.id === currentUser.value?.id) {
    closeDropdown();
    return;
  }

  try {
    await router.post('/switch-user', {
      user_id: user.id
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        currentUser.value = user;
        closeDropdown();
      }
    });
  } catch (error) {
    console.error('Failed to switch user:', error);
  }
};

const fetchCurrentUser = async () => {
  try {
    const response = await fetch('/current-user');
    const data = await response.json();
    currentUser.value = data.current_user;
    availableUsers.value = data.available_users;
  } catch (error) {
    console.error('Failed to fetch current user:', error);
  }
};

const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    closeDropdown();
  }
};

onMounted(() => {
  fetchCurrentUser();
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script> 