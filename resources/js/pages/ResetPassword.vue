<template>
    <div class="h-screen flex justify-center items-center bg-gray-100">
      <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-xl mb-4 font-bold">Reset Password</h2>
  
        <form @submit.prevent="resetPassword">
          <input
            v-model="password"
            type="password"
            placeholder="New Password"
            class="w-full p-2 mb-2 border rounded"
            required
          />
  
          <input
            v-model="confirmPassword"
            type="password"
            placeholder="Confirm New Password"
            class="w-full p-2 mb-4 border rounded"
            required
          />
  
          <button
            :disabled="isLoading"
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
          >
            {{ isLoading ? "Updating..." : "Reset Password" }}
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
  import { ref, onMounted } from "vue";
  import { useRoute, useRouter } from "vue-router";
  
  const route = useRoute();
  const router = useRouter();
  
  const token = ref("");
  const password = ref("");
  const confirmPassword = ref("");
  const message = ref("");
  const errorMessage = ref("");
  const isLoading = ref(false);
  
  onMounted(() => {
    token.value = route.query.token || "";
  
    if (!token.value) {
      errorMessage.value = "Token tidak valid atau sudah kadaluarsa.";
    }
  });
  
  async function resetPassword() {
    if (password.value !== confirmPassword.value) {
      errorMessage.value = "Password tidak sama.";
      return;
    }
  
    isLoading.value = true;
    message.value = "";
    errorMessage.value = "";
  
    try {
      const res = await fetch("/api/auth/reset-password", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          token: token.value,
          password: password.value,
          password_confirmation: confirmPassword.value
        }),
      });
  
      const data = await res.json();
  
      if (data.status === "SUCCESS") {
        message.value = "Password berhasil direset! Silakan login.";
      } else {
        errorMessage.value = data.message || "Gagal reset password";
      }
    } catch (err) {
      errorMessage.value = "Terjadi kesalahan.";
    } finally {
      isLoading.value = false;
    }
  }
  </script>