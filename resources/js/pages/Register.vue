<template>
    <div class="h-screen flex justify-center items-center bg-gray-100">
      <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-xl mb-4 font-bold">Register</h2>
  
        <!-- Pesan Error -->
        <div v-if="errorMessage" class="bg-red-100 text-red-700 p-2 mb-3 rounded">
          {{ errorMessage }}
        </div>
  
        <form @submit.prevent="register">
          <select v-model="role" class="w-full p-2 mb-4 border rounded" required>
            <option value="">- Select Role -</option>
            <option value="student">Student</option>
            <option value="partner">Partner</option>
          </select>
  
          <input v-if="role" v-model="first_name" type="text" placeholder="First Name"
            class="w-full p-2 mb-2 border rounded" required />
  
          <input v-if="role" v-model="last_name" type="text" placeholder="Last Name"
            class="w-full p-2 mb-2 border rounded" required />
  
          <input v-if="role" v-model="email" type="email" placeholder="Email"
            class="w-full p-2 mb-2 border rounded" required />
  
          <input v-if="role === 'partner'" v-model="password" type="password" placeholder="Password"
            class="w-full p-2 mb-4 border rounded" required />
  
            <button :disabled="isLoading"
            class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 disabled:opacity-50 flex justify-center items-center gap-2">
            <Spinner v-if="isLoading" />
            <span>{{ isLoading ? 'Loading...' : 'Register' }}</span>
            </button>
        </form>
  
        <router-link to="/login" class="text-blue-500 mt-2 block text-center">
          Already have an account? Login
        </router-link>
      </div>
    </div>
  </template>
  
<script setup>
import Spinner from "@/components/Spinner.vue";
import { ref } from "vue";
import { useRouter } from "vue-router";

const email = ref("");
const password = ref("");
const role = ref("");
const first_name = ref("");
const last_name = ref("");
const errorMessage = ref("");
const isLoading = ref(false);

const router = useRouter();

async function register() {
  errorMessage.value = "";
  isLoading.value = true;

  const payload =
    role.value === "student"
      ? { email: email.value, role: role.value, first_name: first_name.value, last_name: last_name.value }
      : { email: email.value, password: password.value, role: role.value, first_name: first_name.value, last_name: last_name.value };

  try {
    const res = await fetch("/api/auth/register", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });

    const data = await res.json();

    if (data.status === "SUCCESS") {
      alert("Register berhasil! Silakan login.");
      router.push("/login");
    } else {
      errorMessage.value = data.message || "Register gagal";
    }
  } catch (err) {
    errorMessage.value = "Terjadi kesalahan jaringan";
  } finally {
    isLoading.value = false;
  }
}
</script>