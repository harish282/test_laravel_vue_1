<script setup>
import { ref } from "vue";
import { RouterView } from "vue-router";

const isAuthenticated = ref(false); // Simple auth state, in real app use proper auth
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-bold text-gray-900">Trading App</h1>
          </div>
          <div class="flex items-center space-x-4">
            <router-link to="/orders" class="text-gray-700 hover:text-gray-900"
              >Orders</router-link
            >
            <router-link
              to="/place-order"
              class="text-gray-700 hover:text-gray-900"
              >Place Order</router-link
            >
            <button
              v-if="isAuthenticated"
              @click="logout"
              class="text-gray-700 hover:text-gray-900"
            >
              Logout
            </button>
            <router-link
              v-else
              to="/login"
              class="text-gray-700 hover:text-gray-900"
              >Login</router-link
            >
          </div>
        </div>
      </div>
    </header>
    <main>
      <RouterView />
    </main>
  </div>
</template>

<script>
export default {
  methods: {
    logout() {
      // Implement logout
      this.isAuthenticated = false;
    },
  },
};
</script>
