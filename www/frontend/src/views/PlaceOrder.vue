<template>
  <div class="max-w-md mx-auto mt-10">
    <form @submit.prevent="placeOrder" class="bg-white p-6 rounded shadow">
      <h2 class="text-2xl mb-4">Place Limit Order</h2>
      <div class="mb-4">
        <label class="block text-gray-700">Symbol</label>
        <select v-model="symbol" class="w-full p-2 border rounded">
          <option value="BTC">BTC</option>
          <option value="ETH">ETH</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Side</label>
        <select v-model="side" class="w-full p-2 border rounded">
          <option value="buy">Buy</option>
          <option value="sell">Sell</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Price</label>
        <input
          v-model="price"
          type="number"
          step="0.01"
          class="w-full p-2 border rounded"
          required
        />
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Amount</label>
        <input
          v-model="amount"
          type="number"
          step="0.00000001"
          class="w-full p-2 border rounded"
          required
        />
      </div>
      <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">
        Place Order
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";

const symbol = ref("BTC");
const side = ref("buy");
const price = ref("");
const amount = ref("");
const router = useRouter();

const placeOrder = async () => {
  const token = localStorage.getItem("token");
  if (!token) return;
  try {
    const response = await fetch("/api/orders", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({
        symbol: symbol.value,
        side: side.value,
        price: parseFloat(price.value),
        amount: parseFloat(amount.value),
      }),
    });
    if (response.ok) {
      // Reset form
      price.value = "";
      amount.value = "";
      // Redirect to orders page
      router.push("/orders");
    } else {
      alert("Failed to place order");
    }
  } catch (error) {
    console.error(error);
  }
};
</script>
