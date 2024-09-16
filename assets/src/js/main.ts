import { createApp } from 'vue';
import {createBootstrap} from 'bootstrap-vue-next'
import App from '@/components/App.vue';

// Import Bootstrap and BootstrapVueNext CSS
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';

// Optionally import your custom CSS
import '../css/custom.css';

// Create the Vue app and mount it
const app = createApp(App)
app.use(createBootstrap()) // Important
app.mount('#ao-my-ebooks-app');
