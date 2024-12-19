import {createRouter, createWebHashHistory} from 'vue-router';
import {
    DashboardView,
    ImportNewEbooksView,
    UpdateEbooksView,
    LinkProductsView,
    SetupPricesView,
    AboutView
} from '@/components/views';

const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        {
            path: '/',
            name: 'dashboard',
            component: DashboardView
        },
        {
            path: '/import_ebooks',
            name: 'import_ebooks',
            component: ImportNewEbooksView
        },
        {
            path: '/update_ebooks',
            name: 'update_ebooks',
            component: UpdateEbooksView
        },
        {
            path: '/link_products',
            name: 'link_products',
            component: LinkProductsView
        },
        {
            path: '/setup_prices',
            name: 'setup_prices',
            component: SetupPricesView
        },
        {
            path: '/about',
            name: 'about',
            component: AboutView
        }
    ]
});

export default router;
