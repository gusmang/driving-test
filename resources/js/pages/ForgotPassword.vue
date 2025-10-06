<template>
    <div class="h-screen flex justify-center items-center bg-gray-100">
      <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-xl mb-4 font-bold">Forgot Password</h2>
        <form @submit.prevent="submitForgot">
          <input v-model="email" type="email" placeholder="Enter your email" class="w-full p-2 mb-2 border rounded" required />
  
          <button :disabled="isLoading"
                  class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50">
            {{ isLoading ? 'Sending...' : 'Send Reset Link' }}
          </button>
        </form>
  
        <p v-if="message" class="text-green-500 mt-2 text-center">{{ message }}</p>
        <p v-if="errorMessage" class="text-red-500 mt-2 text-center">{{ errorMessage }}</p>
  
        <router-link to="/login" class="text-gray-600 hover:text-gray-800 text-sm block text-center mt-3">
          Back to Login
        </router-link>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const email = ref('');
  const message = ref('');
  const errorMessage = ref('');
  const isLoading = ref(false);
  
  async function submitForgot() {
    message.value = '';
    errorMessage.value = '';
    isLoading.value = true;
  
    try {
      const res = await fetch('/api/auth/forgot', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.value })
      });
  
      const data = await res.json();
  
      if (data.status === 'SUCCESS') {
        message.value = 'Reset link sent to your email!';
      } else {
        errorMessage.value = data.message || 'Request failed';
      }
    } catch {
      errorMessage.value = 'Server error. Try again later.';
    } finally {
      isLoading.value = false;
    }
  }
  </script>