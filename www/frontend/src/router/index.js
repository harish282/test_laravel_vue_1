import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import PlaceOrder from '../views/PlaceOrder.vue'
import Orders from '../views/Orders.vue'

const routes = [
    { path: '/login', component: Login },
    { path: '/place-order', component: PlaceOrder },
    { path: '/orders', component: Orders },
    { path: '/', redirect: '/orders' }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router