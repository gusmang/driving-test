<template>
    <div class="h-screen flex justify-center items-center bg-gray-100">
      <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-xl mb-4 font-bold">Register</h2>
  
        <form @submit.prevent="register">
          <!-- Pilih Role -->
          <select
            v-model="role"
            class="w-full p-2 mb-4 border rounded"
            required
          >
            <option value="">- Select Role -</option>
            <option value="student">Student</option>
            <option value="partner">Partner</option>
          </select>

          <!-- Input Email selalu tampil -->
          <input
            v-if="role === 'partner' || role === 'student'"
            v-model="first_name"
            type="text"
            placeholder="First Name"
            class="w-full p-2 mb-2 border rounded"
            required
          />

          <input
            v-if="role === 'partner' || role === 'student'"
            v-model="last_name"
            type="text"
            placeholder="Last Name"
            class="w-full p-2 mb-2 border rounded"
            required
          />
  
          <!-- Input Email selalu tampil -->
          <input
            v-if="role === 'partner' || role === 'student'"
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full p-2 mb-2 border rounded"
            required
          />
  
          <!-- Input Password hanya tampil jika role = partner -->
          <input
            v-if="role === 'partner'"
            v-model="password"
            type="password"
            placeholder="Password"
            class="w-full p-2 mb-4 border rounded"
            required
          />
  
          <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
            Register
          </button>
        </form>
  
        <router-link to="/login" class="text-blue-500 mt-2 block text-center">
          Already have an account? Login
        </router-link>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { useRouter } from "vue-router";
  
  const email = ref("");
  const password = ref("");
  const role = ref("");
  const first_name = ref("");
  const last_name = ref("");
  const router = useRouter();
  
  async function register() {
    const payload =
      role.value === "student"
        ? { email: email.value, role: role.value }
        : { email: email.value, password: password.value, role: role.value };
  
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
      alert(data.message || "Register gagal");
    }
  }
  </script>