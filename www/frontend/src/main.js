import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/app.css'

// Setup Echo for real-time (assuming laravel-echo and pusher-js are installed)
// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: 'local',
//     wsHost: 'localhost',
//     wsPort: 8080,
//     wssPort: 8080,
//     forceTLS: false,
//     encrypted: false,
//     auth: {
//         headers: {
//             Authorization: 'Bearer ' + localStorage.getItem('token'),
//         },
//     },
// });

createApp(App).use(router).mount('#app')
