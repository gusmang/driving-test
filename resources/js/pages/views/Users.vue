<template>
    <div class="p-6 bg-white rounded shadow-md">
      <h2 class="text-xl font-bold mb-4">User List</h2>
  
      <!-- Search -->
      <div class="mb-4 flex items-center space-x-2">
        <input
          v-model="search"
          @input="fetchUsers"
          type="text"
          placeholder="Search by email..."
          class="border p-2 rounded flex-1"
        />
        <select v-model="pageSize" @change="fetchUsers" class="border p-2 rounded">
          <option v-for="size in [5,10,20,50]" :key="size" :value="size">{{ size }} / page</option>
        </select>
      </div>
  
      <!-- Table -->
      <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="p-2 border">#</th>
            <th class="p-2 border cursor-pointer" @click="sortBy('first_name')">
              First Name
              <span v-if="sortId==='first_name'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="p-2 border cursor-pointer" @click="sortBy('last_name')">
              Last Name
              <span v-if="sortId==='last_name'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="p-2 border cursor-pointer" @click="sortBy('email')">
              Email
              <span v-if="sortId==='email'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="p-2 border">Role</th>
            <th class="p-2 border">Status</th>
            <th class="p-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in users" :key="user.id" class="hover:bg-gray-50">
            <td class="p-2 border">{{ index + 1 + (currentPage-1)*pageSize }}</td>
            <td class="p-2 border">{{ user.profile.firstName }}</td>
            <td class="p-2 border">{{ user.profile.lastName }}</td>
            <td class="p-2 border">{{ user.email }}</td>
            <td class="p-2 border">{{ user.role }}</td>
            <td class="p-2 border text-center">
                <UserStatusBadge :is-verified="user.is_verified" />
            </td>
            <td class="p-2 border text-center">
                <button
                    @click="deleteUser(user.id)"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 cursor-pointer"
                >
                    Delete
                </button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="6" class="text-center p-4">No users found</td>
          </tr>
        </tbody>
      </table>
  
      <!-- Pagination -->
      <div class="mt-4 flex justify-between items-center">
        <div>
          Page {{ currentPage }} of {{ totalPages }}
        </div>
        <div class="flex space-x-2">
          <button
            class="px-3 py-1 border rounded disabled:opacity-50"
            :disabled="currentPage === 1"
            @click="changePage(currentPage-1)"
          >
            Prev
          </button>
          <button
            class="px-3 py-1 border rounded disabled:opacity-50"
            :disabled="currentPage === totalPages"
            @click="changePage(currentPage+1)"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, watch } from 'vue';
  import UserStatusBadge from '@/components/UserStatusBadge.vue';
  
  const users = ref([]);
  const search = ref('');
  const pageSize = ref(10);
  const currentPage = ref(1);
  const totalPages = ref(1);
  const sortId = ref('email');
  const sortOrder = ref('asc');
  
  const fetchUsers = async () => {
    const token = localStorage.getItem('token');
    const params = new URLSearchParams({
      keyword: search.value,
      pageSize: pageSize.value,
      pageIndex: currentPage.value,
      sortId: sortId.value,
      sortOrder: sortOrder.value
    });
  
    const res = await fetch(`/api/auth/users?${params.toString()}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
  
    const data = await res.json();
    if (data.status === 'SUCCESS') {
      users.value = data.result;
      totalPages.value = data.pagination.TotalPages;
    }
  };
  
  const changePage = (page) => {
    currentPage.value = page;
    fetchUsers();
  };
  
  const sortBy = (field) => {
    if (sortId.value === field) {
      sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
      sortId.value = field;
      sortOrder.value = 'asc';
    }
    fetchUsers();
  };

  const deleteUser = async (id) => {
        if (!confirm("Are you sure you want to delete this user?")) return;

        const token = localStorage.getItem('token');
        const res = await fetch(`/api/auth/users/${id}`, {
            method: 'DELETE',
            headers: { Authorization: `Bearer ${token}` }
        });

        const data = await res.json();
        if (data.status === 'SUCCESS') {
            fetchUsers(); // refresh list
        } else {
            alert("Failed to delete user");
        }
  };
  
  onMounted(fetchUsers);
  </script>