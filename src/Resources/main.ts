import { createApp } from 'vue';
import {createBootstrap} from 'bootstrap-vue-next'
import App from '@/components/App.vue';
import i18n from '@/i18n';
import { createPinia } from 'pinia';

// Import Bootstrap and BootstrapVueNext CSS
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';

// Optionally import your custom CSS
import '@/assets/css/custom.css';

// Create the Vue app and mount it
const app = createApp(App)
const pinia = createPinia();

app.use(i18n);
app.use(createBootstrap());
app.use(pinia);

app.mount('#ao-my-ebooks-app');
