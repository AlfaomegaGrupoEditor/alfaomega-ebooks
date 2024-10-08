import { createApp } from 'vue';
import {createBootstrap} from 'bootstrap-vue-next'
import App from '@/components/App.vue';
import Dashboard from '@/components/Dashboard.vue.vue';
import i18n from '@/i18n';
import { createPinia } from 'pinia';

// Import Bootstrap and BootstrapVueNext CSS
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css';
import 'bootswatch/dist/simplex/bootstrap.min.css'; // using the compiled CSS from Bootswatch


// Optionally import your custom CSS
import '@/assets/css/custom.css';

// Create the Vue app and mount it
const appElement = document.getElementById('ao-my-ebooks-app');
if (appElement) {
    const app = createApp(App)
    const pinia = createPinia();

    app.use(i18n);
    app.use(createBootstrap());
    app.use(pinia);

    app.mount('#ao-my-ebooks-app');
} else {
    const dashboardElement = document.getElementById('ao-dashboard-app');
    if (dashboardElement) {
        const app = createApp(Dashboard)
        const pinia = createPinia();

        app.use(i18n);
        app.use(createBootstrap());
        app.use(pinia);

        app.mount('#ao-dashboard-app');
    }
}
