<template>
    <div class="p-8">
      <h1 class="text-2xl font-bold">Welcome to Dashboard</h1>
      <p class="mt-2">You are logged in!</p>
  
      <button @click="logout" class="bg-red-500 text-white px-4 py-2 rounded mt-4">Logout</button>
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router';
  const router = useRouter();
  
  function logout() {
    localStorage.removeItem('token');
    router.push('/login');
  }
  </script>