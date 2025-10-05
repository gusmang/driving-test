import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import '../css/app.css'

createApp(App)
    .use(router)
    .use(createPinia())
    .mount('#app');