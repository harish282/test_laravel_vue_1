<script setup>
import { onMounted } from "vue";
import { RouterView } from "vue-router";
import { isAuthenticated } from "./composables/useAuth";

const logout = () => {
  isAuthenticated.value = false;
  localStorage.removeItem("token");
};

onMounted(() => {
  if (localStorage.getItem("token")) {
    isAuthenticated.value = true;
  }
});
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
            <router-link
              v-if="isAuthenticated"
              to="/orders"
              class="text-gray-700 hover:text-gray-900"
              >Orders</router-link
            >
            <router-link
              v-if="isAuthenticated"
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
