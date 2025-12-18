import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import PlaceOrder from '../views/PlaceOrder.vue'
import Orders from '../views/Orders.vue'
import { isAuthenticated } from '../composables/useAuth'

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

router.beforeEach((to, from, next) => {
    if (to.path !== '/login' && !isAuthenticated.value) {
        next('/login')
    } else {
        next()
    }
})

export default router