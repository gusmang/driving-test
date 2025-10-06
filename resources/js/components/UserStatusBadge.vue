<template>
    <span
      :class="[
        'px-2 py-1 rounded text-sm font-semibold',
        isVerified ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
      ]"
    >
      {{ isVerified ? 'Verified' : 'Not Verified' }}
    </span>
  </template>
  
  <script setup>
  defineProps({
    isVerified: {
      type: Boolean,
      required: true
    }
  });
  </script>