<template>
    <div class="p-8">
      <!-- Header dengan menu inline -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold flex items-center space-x-6">
          <span>Welcome to Dashboard</span>
          
          <!-- Menu inline -->
          <nav class="flex space-x-4 text-sm font-medium">
            <a @click.prevent="goHome" href="#" class="text-gray-700 hover:text-gray-900">Home</a>
            <a v-if="isAdmin" @click.prevent="goToUsers" href="#" class="text-gray-700 hover:text-gray-900">List Users</a>
          </nav>
        </h1>
  
        <!-- Logout kanan -->
        <button @click="logout" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
          Logout
        </button>
      </div>
  
      <!-- Role student / partner -->
      <div class="bg-white p-6 rounded shadow-md max-w-md">
        <h2 class="text-xl font-semibold mb-2">Your Profile</h2>
        <p><strong>Role:</strong> {{ user?.role }}</p>
        <p><strong>First Names:</strong> {{ user?.profile?.firstName }}</p>
        <p><strong>Last Name:</strong> {{ user?.profile?.lastName }}</p>
        <p><strong>Email:</strong> {{ user?.email }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, computed, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  
  const router = useRouter();
  const user = ref({});
  
  // Computed properties untuk role
  const isStudentOrPartner = computed(() => user.value.role === 'student' || user.value.role === 'partner');
  const isAdmin = computed(() => user.value.role === 'admin');
  
  // Fetch user profile dari API me
  async function fetchUser() {
    const token = localStorage.getItem('token');
    if (!token) {
      router.push('/login');
      return;
    }
  
    try {
      const res = await fetch('/api/auth/me', {
        headers: { Authorization: `Bearer ${token}` }
      });
      const data = await res.json();
      if (data.status === 'SUCCESS') {
        user.value = data.result;
      } else {
        router.push('/login');
      }
    } catch (err) {
      console.error(err);
      router.push('/login');
    }
  }
  
  function logout() {
    localStorage.removeItem('token');
    router.push('/login');
  }
  
  function goToUsers() {
    router.push('/admin/users');
  }
  
  function goHome() {
    router.push('/dashboard');
  }
  
  onMounted(fetchUser);
  </script>