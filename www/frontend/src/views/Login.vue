<template>
  <div class="max-w-md mx-auto mt-10">
    <form @submit.prevent="login" class="bg-white p-6 rounded shadow">
      <h2 class="text-2xl mb-4">Login</h2>
      <div class="mb-4">
        <label class="block text-gray-700">Email</label>
        <input
          v-model="email"
          type="email"
          class="w-full p-2 border rounded"
          required
        />
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Password</label>
        <input
          v-model="password"
          type="password"
          class="w-full p-2 border rounded"
          required
        />
      </div>
      <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded">
        Login
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { isAuthenticated } from "../composables/useAuth";

const email = ref("");
const password = ref("");
const router = useRouter();

const login = async () => {
  try {
    const response = await fetch("/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });
    if (response.ok) {
      const data = await response.json();
      localStorage.setItem("token", data.token);
      isAuthenticated.value = true;
      router.push("/orders");
    } else {
      alert("Invalid credentials");
    }
  } catch (error) {
    console.error(error);
  }
};
</script>
