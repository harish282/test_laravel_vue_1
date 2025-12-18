import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/app.css'

// Setup Echo for real-time
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 'local',
    wsHost: 'localhost',
    wsPort: 8081,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws'],
    authEndpoint: 'http://localhost:8000/broadcasting/auth',
    auth: {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
        },
    },
});

createApp(App).use(router).mount('#app')
