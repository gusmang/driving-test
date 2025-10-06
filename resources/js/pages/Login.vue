<template>
    <div class="h-screen flex justify-center items-center bg-gray-100">
      <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-xl mb-4 font-bold">Login</h2>
        <form @submit.prevent="login">
          <select v-model="role" class="w-full p-2 mb-4 border rounded" required>
            <option value="" selected>- Select Role -</option>
            <option value="admin">Admin</option>
            <option value="partner">Partner</option>
            <option value="student">Student</option>
          </select>
  
          <input v-model="pin" v-if="role === 'student'" type="text" placeholder="PIN" class="w-full p-2 mb-2 border rounded" required />
          <input v-model="email" v-if="role === 'partner' || role === 'admin'" type="email" placeholder="Email" class="w-full p-2 mb-2 border rounded" required />
          <input v-model="password" v-if="role === 'partner' || role === 'admin'" type="password" placeholder="Password" class="w-full p-2 mb-2 border rounded" required />
  
          <button :disabled="isLoading"
                  class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50 flex justify-center items-center gap-2">
            <Spinner v-if="isLoading" />
            <span>{{ isLoading ? 'Loading...' : 'Login' }}</span>
          </button>
        </form>
  
        <!-- Error message -->
        <p v-if="errorMessage" class="text-red-500 mt-2 text-center">{{ errorMessage }}</p>
  
        <div class="mt-4 text-center flex flex-col space-y-1">
          <router-link to="/register" class="text-blue-500">Register</router-link>
          <router-link to="/forgot-password" class="text-gray-600 hover:text-gray-800 text-sm">Forgot Password?</router-link>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import Spinner from "@/components/Spinner.vue";
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  const email = ref('');
  const role = ref('');
  const password = ref('');
  const pin = ref('');
  const errorMessage = ref('');
  const isLoading = ref(false);
  const router = useRouter();
  
  async function login() {
    errorMessage.value = '';
    isLoading.value = true;
  
    const payload =
      role.value === "student"
        ? { role: role.value, pin: pin.value }
        : { email: email.value, password: password.value, role: role.value };
  
    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
  
      const data = await res.json();
  
      if (data.status === 'SUCCESS') {
        localStorage.setItem('token', data.result.access_token);
        router.push('/dashboard');
      } else {
        errorMessage.value = data.message || 'Login gagal';
      }
    } catch (err) {
      errorMessage.value = 'Terjadi kesalahan. Silakan coba lagi.';
    } finally {
      isLoading.value = false;
    }
  }
  </script>