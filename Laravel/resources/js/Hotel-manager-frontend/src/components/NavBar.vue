<template>
  <nav class="nav p-4">
    <div class="max-w-7xl mx-auto flex justify-center space-x-4">
      <router-link 
        to="/" 
        class="nav-link"
        active-class="active-link"
      >
        Home
      </router-link>
      <router-link 
        to="/hotel" 
        class="nav-link"
        active-class="active-link"
      >
        Hotels
      </router-link>
      <router-link 
        to="/room" 
        class="nav-link"
        active-class="active-link"
      >
        Rooms
      </router-link>
      <router-link 
        to="/guest" 
        class="nav-link"
        active-class="active-link"
      >
        Guests
      </router-link>
      <router-link 
        to="/service" 
        class="nav-link"
        active-class="active-link"
      >
        Services
      </router-link>
      <router-link 
        to="/category" 
        class="nav-link"
        active-class="active-link"
      >
        Categories
      </router-link>
      <router-link 
        to="/type" 
        class="nav-link"
        active-class="active-link"
      >
        Types
      </router-link>
      <!-- Añade más elementos según necesites -->
    </div>
  </nav>
</template>

<script lang="ts" setup>
</script>

<style scoped>
.nav {
  color: #EEEEEE;
  background-color: #1f2937;
}

.nav-link {
  color: #EEEEEE;
  text-decoration: none;
  padding: 0.5rem 1rem;
  transition: border-bottom 0.3s ease;
}

.nav-link:hover {
  color: #00ADB5;
}

.active-link {
  border-bottom: 2px solid #00ADB5;
}
</style>
