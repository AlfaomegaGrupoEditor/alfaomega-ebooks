import {defineStore} from 'pinia';
import type {State} from '@/services/processes/types';
import {API} from '@/services';
import {ProcessType} from '@/types';

export const useProcessStore = defineStore('processStore', {
    state: (): State => (
        {
            importNewEbooks: {
                status: 'idle',
                completed: 0,
                processing: 0,
                pending: 0,
                failed: 0
            },
            updateEbooks: {
                status: 'idle',
                completed: 0,
                processing: 0,
                pending: 0,
                failed: 0
            },
            linkProducts: {
                status: 'idle',
                completed: 0,
                processing: 0,
                pending: 0,
                failed: 0
            },
            setupPrices: {
                status: 'idle',
                completed: 0,
                processing: 0,
                pending: 0,
                failed: 0
            }
        }
    ),

    getters: {
        getImportNewEbooks: (state) => state.importNewEbooks,
        getUpdateEbooks: (state) => state.updateEbooks,
        getLinkProducts: (state) => state.linkProducts,
        getSetupPrices: (state) => state.setupPrices,
    },

    actions: {
        /**
         * Retrieve queue process status.
         */
        async dispatchRetrieveQueueStatus(process: ProcessType)
        {
            const response = await API.process.getProcessStatus(process);
            if (response?.status === 'success' && response.data) {
                switch (process) {
                    case 'import-new-ebooks':
                        this.importNewEbooks = response.data;
                        break;
                    case 'update-ebooks':
                        this.updateEbooks = response.data;
                        break;
                    case 'link-products':
                        this.linkProducts = response.data;
                        break;
                    case 'setup-prices':
                        this.setupPrices = response.data;
                        break;
                }
            }
        },
    },
});
