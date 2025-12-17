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

const symbol = ref("BTC");
const side = ref("buy");
const price = ref("");
const amount = ref("");

const placeOrder = async () => {
  // API call to /api/orders
  console.log({
    symbol: symbol.value,
    side: side.value,
    price: price.value,
    amount: amount.value,
  });
  // Reset form
  price.value = "";
  amount.value = "";
};
</script>
