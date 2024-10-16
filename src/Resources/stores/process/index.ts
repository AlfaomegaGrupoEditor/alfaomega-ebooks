import {defineStore} from 'pinia';
import type {State} from '@/services/processes/types';
import {API} from '@/services';
import {ProcessStatusType, ProcessType} from '@/types';
import {getProcess} from '@/services/Helper';

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
            },
            queueStatus: {
                status: 'idle',
                completed: 0,
                processing: 0,
                pending: 0,
                failed: 0
            },
            processData: {
                actions: [],
                meta: {
                    total: 0,
                    current_page: 0,
                    pages: 0
                }
            }
        }
    ),

    getters: {
        getImportNewEbooks: (state) => state.importNewEbooks,
        getUpdateEbooks: (state) => state.updateEbooks,
        getLinkProducts: (state) => state.linkProducts,
        getSetupPrices: (state) => state.setupPrices,
        getProcessData: (state) => state.processData,
        getQueueStatus: (state) => state.queueStatus
    },

    actions: {
        /**
         * Retrieve queue process status.
         */
        async dispatchRetrieveQueueStatus(process: ProcessType)
        {
            if (process === 'import-new-ebooks') {
                this.queueStatus.processing = 0;
                this.queueStatus.pending = 0;
                this.queueStatus.completed = 0;
                this.queueStatus.failed = 0;
            }
            const response = await API.process.getProcessStatus(process);
            if (response?.status === 'success' && response.data) {
                this[getProcess(process)] = response.data;
                this.queueStatus.processing += response.data.processing;
                this.queueStatus.pending += response.data.pending;
                this.queueStatus.completed += response.data.completed;
                this.queueStatus.failed += response.data.failed;
            }
        },

        /**
         * Retrieve process data.
         */
        async dispatchRetrieveProcessData(process: ProcessType,
                                          status: ProcessStatusType,
                                          page: number,
                                          perPage: number
        ) {
            const response = await API.process.getProcessActions(process, status, page, perPage);
            if (response.status === 'success' && response.data) {
                this.processData.actions = response.data;
                this.processData.meta = response.meta;
            }
        },

        /**
         * Clear queue.
         */
        async dispatchClearQueue(process: ProcessType) {
            const response = await API.process.clearQueue(process);
            if (response.status === 'success' && response.data) {
                if (response?.status === 'success' && response.data) {
                    this[getProcess(process)] = response.data;
                }
            }
        },

        /**
         * Delete action.
         */
        async dispatchDeleteAction(process: ProcessType, ids: number[]) {
            const response = await API.process.deleteAction(process, ids);
            if (response.status === 'success') {
                this[getProcess(process)] = response.data;
            }
        },

        /**
         * Retry action.
         */
        async dispatchRetryAction(process: ProcessType, ids: number[]) {
            const response = await API.process.retryAction(process, ids);
            if (response.status === 'success') {
                this[getProcess(process)] = response.data;
            }
        },

        /**
         * Import new ebooks.
         */
        async dispatchImportNewEbooks() {
            const response = await API.process.importNewEbooks();
            if (response.status === 'success') {
                const response = await API.process.getProcessActions(
                    'import-new-ebooks',
                    'processing',
                    1,
                    state.processData.meta.per_page
                );
                if (response.status === 'success' && response.data) {
                    this.processData = {
                        actions: response.data,
                        meta: response.meta
                    };
                }
            }
        },

        /**
         * Update ebooks.
         */
        async dispatchUpdateEbooks() {
            const response = await API.process.importNewEbooks();
            if (response.status === 'success') {
                const response = await API.process.getProcessActions(
                    'update-ebooks',
                    'processing',
                    1,
                    state.processData.meta.per_page
                );
                if (response.status === 'success' && response.data) {
                    this.processData = {
                        actions: response.data,
                        meta: response.meta
                    };
                }
            }
        },

        /**
         * Link products.
         */
        async dispatchLinkProducts() {
            const response = await API.process.importNewEbooks();
            if (response.status === 'success') {
                const response = await API.process.getProcessActions(
                    'link-products',
                    'processing',
                    1,
                    state.processData.meta.per_page
                );
                if (response.status === 'success' && response.data) {
                    this.processData = {
                        actions: response.data,
                        meta: response.meta
                    };
                }
            }
        },
    },
});
