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
            <td>{{ getStatusString(order.status) }}</td>
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
const userId = ref(null);

const buyOrders = computed(() => orders.value.filter((o) => o.side === "buy"));
const sellOrders = computed(() =>
  orders.value.filter((o) => o.side === "sell")
);

const getStatusString = (status) => {
  switch (status) {
    case 1:
      return "Open";
    case 2:
      return "Filled";
    case 3:
      return "Cancelled";
    default:
      return "Unknown";
  }
};

const loadProfile = async () => {
  const token = localStorage.getItem("token");
  if (!token) return;
  try {
    const response = await fetch("/api/profile", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    if (response.ok) {
      profile.value = await response.json();
      userId.value = profile.value.id;
    }
  } catch (error) {
    console.error(error);
  }
};

const loadOrders = async () => {
  const token = localStorage.getItem("token");
  const headers = { Accept: "application/json" };
  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }
  try {
    const response = await fetch(`/api/orders?symbol=${selectedSymbol.value}`, {
      headers,
    });
    if (response.ok) {
      orders.value = await response.json();
    }
  } catch (error) {
    console.error(error);
  }
};

const loadUserOrders = async () => {
  const token = localStorage.getItem("token");
  if (!token) return;
  try {
    const response = await fetch("/api/my-orders", {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    if (response.ok) {
      userOrders.value = await response.json();
    }
  } catch (error) {
    console.error(error);
  }
};

const cancelOrder = async (id) => {
  const token = localStorage.getItem("token");
  if (!token) return;
  try {
    const response = await fetch(`/api/orders/${id}/cancel`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });
    if (response.ok) {
      loadUserOrders();
    }
  } catch (error) {
    console.error(error);
  }
};

onMounted(async () => {
  await loadProfile();
  loadOrders();
  await loadUserOrders();

  // Listen for OrderMatched event
  if (window.Echo && userId.value) {
    window.Echo.private(`user.${userId.value}`).listen(
      ".order.matched",
      (e) => {
        // Update balance, assets, and orders
        loadProfile();
        loadUserOrders();
      }
    );
  }
});
</script>
