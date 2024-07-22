<!-- Navbar.vue -->
<template>
    <nav class="bg-gray-900 p-4">
      <div class="max-w-7xl mx-auto flex justify-center space-x-4">
        <router-link to="/" class="text-white">Home</router-link>
        <router-link to="/hotel" class="text-white">Hotels</router-link>
        <router-link to="/room" class="text-white">Rooms</router-link>
        <router-link to="/guest" class="text-white">Guests</router-link>
        <router-link to="/service" class="text-white">Services</router-link>
        <router-link to="/category" class="text-white">Categories</router-link>
        <router-link to="/type" class="text-white">Types</router-link>
        <!-- Añade más elementos según necesites -->
      </div>
    </nav>
</template>
  
<script lang="ts" setup>

</script>