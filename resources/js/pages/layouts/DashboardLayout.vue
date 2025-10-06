<template>
  <div class="p-6 min-h-screen bg-gray-50">
    <header class="flex items-center justify-between mb-6 px-4">
      <div class="flex items-center space-x-8">
        <h1 class="text-2xl font-bold">Welcome, {{ user?.profile?.firstName || 'User' }}</h1>

        <nav class="flex space-x-6 text-base font-semibold">
          <a
            @click.prevent="goHome"
            href="#"
            :class="route.path === '/dashboard' ? 'text-blue-600 underline' : 'text-gray-700 hover:text-gray-900'"
          >
            Home
          </a>

          <a
            v-if="isAdmin"
            @click.prevent="goToUsers"
            href="#"
            :class="route.path === '/dashboard/users' ? 'text-blue-600 underline' : 'text-gray-700 hover:text-gray-900'"
          >
            List Users
          </a>
        </nav>
      </div>

      <button
        @click="logout"
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 text-sm font-medium"
      >
        Logout
      </button>
    </header>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, provide } from 'vue';
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const user = ref({});

// Provide user ke semua child component
provide('user', user);

const isAdmin = computed(() => user.value.role === 'admin');

async function fetchUser() {
  const token = localStorage.getItem('token');
  if (!token) return router.push('/login');

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
  } catch {
    router.push('/login');
  }
}

function logout() {
  localStorage.removeItem('token');
  router.push('/login');
}

function goHome() {
  router.push('/dashboard');
}

function goToUsers() {
  router.push('/dashboard/users');
}

onMounted(fetchUser);
</script>
