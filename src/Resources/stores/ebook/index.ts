import {defineStore} from 'pinia';
import type {State} from '@/services/ebooks/types';
import {API} from '@/services';

export const useEbookStore = defineStore('ebookStore', {
    state: (): State => (
        {
            ebooks: {
                catalog: 0,
                imported: 0,
            },
            products: {
                catalog: 0,
                unlinked: 0,
                linked: 0,
            },
            access: {
                sample: 0,
                purchase: 0,
                created: 0,
                active: 0,
                expired: 0,
                cancelled: 0,
                total: 0,
            },
            codes: {
                samples: 0,
                import: 0,
                created: 0,
                sent: 0,
                redeemed: 0,
                expired: 0,
                cancelled: 0,
                total: 0,
            },
        }
    ),

    getters: {
        getEbooksInfo: (state) => state.ebooks,
        getProductsInfo: (state) => state.products,
        getAccessInfo: (state) => state.access,
        getCodesInfo: (state) => state.codes,
    },

    actions: {
        /**
         * Retrieves eBook information.
         */
        async dispatchRetrieveEbooksInfo()
        {
            const response = await API.ebook.getEbooksInfo();
            if (response.status === 'success' && response.data) {
                this.ebooks = response.data;
            }
        },

        /**
         * Retrieves products information.
         */
        async dispatchRetrieveProductsInfo()
        {
            const response = await API.ebook.getProductsInfo();
            if (response.status === 'success' && response.data) {
                this.products = response.data;
            }
        },

        /**
         * Retrieves ebook access information.
         */
        async dispatchRetrieveAccessInfo()
        {
            const response = await API.ebook.getAccessInfo();
            if (response.status === 'success' && response.data) {
                this.access = response.data;
            }
        },

        /**
         * Retrieves access codes information.
         */
        async dispatchRetrieveCodesInfo()
        {
            const response = await API.ebook.getCodesInfo();
            if (response.status === 'success' && response.data) {
                this.codes = response.data;
            }
        },
    },
});
