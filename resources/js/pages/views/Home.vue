<template>
    <div class="bg-white p-6 rounded shadow-md max-w-md">
      <h2 class="text-xl font-semibold mb-2">Your Profile</h2>
      <p><strong>Role:</strong> {{ user?.role }}</p>
      <p><strong>First Name:</strong> {{ user?.profile?.firstName }}</p>
      <p><strong>Last Name:</strong> {{ user?.profile?.lastName }}</p>
      <p><strong>Email:</strong> {{ user?.email }}</p>
    </div>
  </template>
  
  <script setup>
  import { inject } from 'vue';
  const user = inject('user');
  </script>