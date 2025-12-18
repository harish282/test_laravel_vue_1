<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Balances -->
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-medium mb-4">Balances</h3>
        <p>USD: {{ profile.usd_balance }}</p>
        <div v-for="asset in profile.assets" :key="asset.symbol">
          <p>
            {{ asset.symbol }}: {{ asset.amount }} (Locked:
            {{ asset.locked_amount }})
          </p>
        </div>
      </div>

      <!-- Orderbook -->
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-medium mb-4">Orderbook</h3>
        <select
          v-model="selectedSymbol"
          @change="loadOrders"
          class="mb-4 p-2 border rounded"
        >
          <option value="BTC">BTC</option>
          <option value="ETH">ETH</option>
        </select>
        <div>
          <h4>Buy Orders</h4>
          <ul>
            <li v-for="order in buyOrders" :key="order.id">
              Price: {{ order.price }}, Amount: {{ order.amount }}
            </li>
          </ul>
          <h4>Sell Orders</h4>
          <ul>
            <li v-for="order in sellOrders" :key="order.id">
              Price: {{ order.price }}, Amount: {{ order.amount }}
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Orders List -->
    <div class="mt-10 bg-white p-6 rounded shadow">
      <h3 class="text-lg font-medium mb-4">Your Orders</h3>
      <table class="w-full">
        <thead>
          <tr>
            <th class="text-left">Symbol</th>
            <th class="text-left">Side</th>
            <th class="text-left">Price</th>
            <th class="text-left">Amount</th>
            <th class="text-left">Status</th>
            <th class="text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in userOrders" :key="order.id">
            <td>{{ order.symbol }}</td>
            <td>{{ order.side }}</td>
            <td>{{ order.price }}</td>
            <td>{{ order.amount }}</td>
            <td>{{ order.status }}</td>
            <td>
              <button
                v-if="order.status === 1"
                @click="cancelOrder(order.id)"
                class="text-red-500"
              >
                Cancel
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";

const profile = ref({ usd_balance: 0, assets: [] });
const selectedSymbol = ref("BTC");
const orders = ref([]);
const userOrders = ref([]);

const buyOrders = computed(() => orders.value.filter((o) => o.side === "buy"));
const sellOrders = computed(() =>
  orders.value.filter((o) => o.side === "sell")
);

const loadProfile = async () => {
  // API call to /api/profile
  profile.value = {
    usd_balance: 1000,
    assets: [{ symbol: "BTC", amount: 0.5, locked_amount: 0 }],
  };
};

const loadOrders = async () => {
  // API call to /api/orders?symbol=selectedSymbol
  orders.value = [
    { id: 1, symbol: "BTC", side: "buy", price: 50000, amount: 0.01 },
    { id: 2, symbol: "BTC", side: "sell", price: 51000, amount: 0.01 },
  ];
};

const loadUserOrders = async () => {
  // API call to /api/orders (all user orders)
  userOrders.value = [
    {
      id: 1,
      symbol: "BTC",
      side: "buy",
      price: 50000,
      amount: 0.01,
      status: 1,
    },
  ];
};

const cancelOrder = async (id) => {
  // API call to /api/orders/{id}/cancel
  console.log("Cancel order", id);
};

onMounted(() => {
  loadProfile();
  loadOrders();
  loadUserOrders();
});
</script>
