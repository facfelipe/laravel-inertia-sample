<script setup>
import { Link } from '@inertiajs/vue3';
import UserRoleSelector from '@/Components/UserRoleSelector.vue';

const props = defineProps({
  title: String
});
</script>

<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Navbar -->
    <nav class="bg-blue-600 shadow">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <Link href="/" class="text-2xl font-semibold text-white">Health Records</Link>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/" class="text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors">Home</Link>
            <Link href="/patients" class="text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors">Patients</Link>
            <Link href="/medical-records" class="text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors">Medical Records</Link>
            <Link href="/medical-form" class="bg-green-600 text-white px-3 py-2 rounded-md hover:bg-green-700 transition-colors">New Record</Link>
            
            <!-- User Role Selector -->
            <UserRoleSelector />
          </div>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow">
      <slot></slot>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800">
      <div class="max-w-7xl mx-auto py-4 px-4">
        <div class="flex justify-between items-center">
          <span class="text-white text-sm">© 2023 Health Records System</span>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template> 