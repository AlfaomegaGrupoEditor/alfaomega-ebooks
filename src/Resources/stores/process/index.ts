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
        getProcessData: (state) => state.processData
    },

    actions: {
        /**
         * Retrieve queue process status.
         */
        async dispatchRetrieveQueueStatus(process: ProcessType)
        {
            const response = await API.process.getProcessStatus(process);
            if (response?.status === 'success' && response.data) {
                this[getProcess(process)] = response.data;
            } else {

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
                this.processData = {
                    actions: response.data,
                    meta: response.meta
                };
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
        }
    },
});
