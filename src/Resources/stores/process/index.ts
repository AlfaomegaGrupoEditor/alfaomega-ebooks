import {defineStore} from 'pinia';
import type {State} from '@/services/processes/types';
import {API} from '@/services';
import {
    ActionType,
    ProcessItem,
    ProcessStatusType,
    ProcessType,
    SetupPriceFactorType
} from '@/types';
import {getProcess} from '@/services/Helper';
import {eventBus} from '@/events';

export const useProcessStore = defineStore('processStore', {
    state: (): State => (
        {
            importNewEbooks: {
                status: 'idle',
                completed: 0,
                processing: 0,
                pending: 0,
                failed: 0,
                excluded: 0
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
         * Dispatches the retrieval of the queue status for a specified process.
         *
         * @param {ProcessType} process - The type of process for which the queue status is being retrieved.
         * @return {Promise<void>} A promise that resolves when the queue status has been successfully updated.
         */
        async dispatchRetrieveQueueStatus(process: ProcessType)
        {
            if (process === 'import-new-ebooks') {
                this.queueStatus.processing = 0;
                this.queueStatus.pending = 0;
                this.queueStatus.completed = 0;
                this.queueStatus.failed = 0;
                this.queueStatus.excluded = 0;
            }
            const response = await API.process.getProcessStatus(process);
            if (response?.status === 'success' && response.data) {
                this[getProcess(process)] = response.data;
                this.queueStatus.processing += response.data.processing;
                this.queueStatus.pending += response.data.pending;
                this.queueStatus.completed += response.data.completed;
                this.queueStatus.failed += response.data.failed;
                if (process === 'import-new-ebooks') {
                    this.queueStatus.excluded += response.data.excluded;
                }
            }
        },

        /**
         * Dispatches a request to retrieve process data and updates the processData property with the retrieved actions and metadata.
         *
         * @param {ProcessType} process - The type of the process to retrieve data for.
         * @param {ProcessStatusType} status - The status filter for the process data.
         * @param {number} page - The page number for the paginated results.
         * @param {number} perPage - The number of items per page for the paginated results.
         * @return {Promise<void>} - A promise that resolves when the process data has been successfully retrieved and updated.
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
         * Asynchronously clears the queue for a given process and updates the process state.
         *
         * @param {ProcessType} process - The process for which the queue should be cleared.
         * @return {Promise<void>} - A promise that resolves when the queue has been cleared and the state is updated.
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
         * Dispatches a delete action to the specified process for the given IDs.
         * This function handles two types of deletions: 'action' and 'import' for the 'import-new-ebooks' process,
         * and a generic deletion for other processes.
         *
         * @param {ProcessType} process - The process to delete actions from.
         * @param {number[]} ids - Array of IDs that need to be deleted.
         * @return {Promise<void>} - A promise that resolves when the delete actions are completed.
         */
        async dispatchDeleteAction(process: ProcessType, ids: number[]) {
            if (process === 'import-new-ebooks') {
                // delete the actions
                const actionIds = this.filterActions(ids, 'action');
                if (actionIds.length) {
                    const responseAction = await API.process.deleteAction(process, actionIds, 'action');
                    if (responseAction.status === 'success') {
                        this.importNewEbooks = responseAction.data;
                    }
                }

                // delete the imports
                const importIds = this.filterActions(ids, 'import');
                if (importIds.length) {
                    const responseImport = await API.process.deleteAction(process, importIds, 'import');
                    if (responseImport.status === 'success') {
                        this.importNewEbooks = responseImport.data;
                    }
                }
            } else {
                const response = await API.process.deleteAction(process, ids);
                if (response.status === 'success') {
                    this[getProcess(process)] = response.data;
                }
            }
        },

        /**
         * Retries the specified actions for a given process, such as 'import-new-ebooks'.
         * It filters the action and import IDs and dispatches the appropriate retry actions.
         *
         * @param {string} process - The type of the process to retry actions for.
         * @param {number[]} ids - The list of IDs representing the actions to be retried.
         * @return {Promise<void>} - A promise that resolves when the retry actions are completed.
         */
        async dispatchRetryAction(process: ProcessType, ids: number[]) {
            if (process === 'import-new-ebooks') {
                // retry the actions
                const actionIds = this.filterActions(ids, 'action');
                if (actionIds.length) {
                    const responseAction = await API.process.retryAction(process, actionIds, 'action');
                    if (responseAction.status === 'success') {
                        this.importNewEbooks = responseAction.data;
                    }
                }

                // retry the imports
                const importIds = this.filterActions(ids, 'import');
                if (importIds.length) {
                    const responseImport = await API.process.retryAction(process, importIds, 'import');
                    if (responseImport.status === 'success') {
                        this.importNewEbooks = responseImport.data;
                        await this.dispatchImportNewEbooks();
                    }
                }
            } else {
                const response = await API.process.retryAction(process, ids);
                if (response.status === 'success') {
                    this[getProcess(process)] = response.data;
                }
            }
        },

        /**
         * Dispatches an action to exclude specified items based on the given process type.
         *
         * @param {string} process The process type to determine the action exclusion logic.
         * @param {number[]} ids The IDs of the items to be excluded from the specified process.
         * @return {Promise<void>} A promise that resolves when the action is completed.
         */
        async dispatchExcludeAction(process: ProcessType, ids: number[]) {
            if (process === 'import-new-ebooks') {
                // exclude from import
                const importIds = this.filterActions(ids, 'import');
                if (importIds.length) {
                    const responseImport = await API.process.excludeAction(process, importIds);
                    if (responseImport.status === 'success') {
                        this.importNewEbooks = responseImport.data;
                    }
                } else {
                    eventBus.emit('notification', {
                        message: 'action_exclude_not_available',
                        type: 'warning'
                    });
                }
            } else {
                eventBus.emit('notification', {
                    message: 'action_exclude_not_available',
                    type: 'warning'
                });
            }
        },

        /**
         * Initiates the import of new eBooks and updates the process data with the actions if the import is successful.
         *
         * @return {Promise<void>} A promise that resolves when the process is complete.
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
         * Dispatches the update of eBooks by first importing new eBooks and then retrieving the updated process actions.
         * This method updates the local process data state with the retrieved actions and metadata upon successful responses.
         *
         * @return {Promise<void>} A promise that resolves when the process is completed.
         */
        async dispatchUpdateEbooks() {
            const response = await API.process.updateEbooks();
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
         * Asynchronously dispatches the linking of products by importing new ebooks and then
         * fetching the process actions for linking products. Updates the processData with the
         * actions and metadata if the responses are successful.
         *
         * @return {Promise<void>} A promise that resolves when the operations are complete.
         */
        async dispatchLinkProducts() {
            const response = await API.process.linkProducts();
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

        /**
         * Asynchronously dispatches the setup of ebook prices and updates the process data if the setup is successful.
         *
         * @param {SetupPriceFactorType} factor - The factor based on which the ebook price should be set up.
         * @param {number} value - The value used for setting up the ebook price.
         * @return {Promise<void>} A promise that resolves once the price setup and process data update (if applicable) are completed.
         */
        async dispatchSetupEbooksPrice(factor: SetupPriceFactorType, value: number) {
            const response = await API.process.setupEbooksPrice(factor, value);
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

        /**
         * Filters actions based on the provided IDs and action type.
         *
         * @param {Number[]} ids - An array of action IDs to filter.
         * @param {ActionType} [type='action'] - The type of actions to filter. Default is 'action'.
         * @return {Number[]} - An array of action IDs that match the specified type and are included in the provided IDs.
         */
        filterActions(ids: Number[], type: ActionType = 'action') {
            return this.processData.actions
                .filter((action: ProcessItem) => action.type === type && ids.includes(action.id))
                .map((action: ProcessItem) => type === 'action' ? action.id : action.isbn);
        }
    },
});
