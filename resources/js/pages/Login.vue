<template>
    <div class="h-screen flex justify-center items-center bg-gray-100">
      <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-xl mb-4 font-bold">Login</h2>
        <form @submit.prevent="login">
          <input v-model="email" type="email" placeholder="Email" class="w-full p-2 mb-2 border rounded" required />
          <input v-model="password" type="password" placeholder="Password" class="w-full p-2 mb-2 border rounded" required />
          <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
        </form>
        <router-link to="/register" class="text-blue-500 mt-2 block text-center">Register</router-link>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  
  const email = ref('');
  const password = ref('');
  const router = useRouter();
  
  async function login() {
    const res = await fetch('/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email: email.value, password: password.value })
    });
  
    const data = await res.json();
    if (data.status === 'SUCCESS') {
      localStorage.setItem('token', data.result.access_token);
      router.push('/dashboard');
    } else {
      alert('Login gagal');
    }
  }
  </script>