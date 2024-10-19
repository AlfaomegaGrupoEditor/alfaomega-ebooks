import {request} from '../api';
import type {APIResponse} from '../types';
import {useAppStore} from '@/stores';
import {
    ActionType,
    AsyncProcessType,
    ProcessItem,
    ProcessStatusType,
    ProcessType,
    SetupPriceFactorType
} from '@/types';
import {eventBus} from '@/events';

/**
 * Retrieve process status.
 * @param process
 */
async function getProcessStatus(process: ProcessType): Promise<APIResponse<AsyncProcessType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('POST', `/alfaomega-ebooks/api/process-info/`, {
        process: process
    });
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Retrieve process actions.
 * @param process
 * @param status
 * @param page
 * @param perPage
 */
async function getProcessActions(process: ProcessType,
                                 status: ProcessStatusType,
                                 page: number = 1,
                                 perPage: number = 10): Promise<APIResponse<ProcessItem[]>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<ProcessItem[]>>('POST', `/alfaomega-ebooks/api/process-actions/`, {
        process: process,
        status: status,
        page: page || 1,
        perPage: perPage || 10
    });
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<ProcessItem[]>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Clear queue.
 * @param process
 */
async function clearQueue(process: ProcessType): Promise<APIResponse<AsyncProcessType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('POST', `/alfaomega-ebooks/api/clear-queue/`, {
        process: process
    });
    appStore.setLoading(false);

    if (response.status == 'success') {
        eventBus.emit('notification', {
            message: 'clear_queue_success',
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Deletes the specified actions.
 *
 * @param {ProcessType} process - The process context in which the actions are to be deleted.
 * @param {Number[]} ids - An array of action IDs to be deleted.
 * @param {ActionType} [type='action'] - The type of action being deleted, defaults to 'action'.
 * @return {Promise<APIResponse<AsyncProcessType | null>>} A promise that resolves to an API response with the result of the delete operation.
 */
async function deleteAction(process: ProcessType,
                            ids: (Number | String)[],
                            type: ActionType = 'action'
): Promise<APIResponse<AsyncProcessType | null>> {
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('POST', `/alfaomega-ebooks/api/delete-action/`, {
        process: process,
        ids: ids,
        type: type,
    });
    appStore.setLoading(false);

    if (response.status == 'success' && response.data.status === 'success') {
        eventBus.emit('notification', {
            message: 'action_deleted_success',
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Sends a POST request to exclude actions based on provided process and IDs.
 *
 * @param {ProcessType} process - The process type for which the action is to be excluded.
 * @param {(Number|String)[]} ids - An array of IDs (either Number or String) representing the actions to be excluded.
 * @return {Promise<APIResponse<AsyncProcessType|null>>} - A promise resolving to the API response containing either the async process type or null.
 */
async function excludeAction(process: ProcessType,
                            ids: (Number | String)[]
): Promise<APIResponse<AsyncProcessType | null>> {
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('POST', `/alfaomega-ebooks/api/exclude-action/`, {
        process: process,
        ids: ids
    });
    appStore.setLoading(false);

    if (response.status == 'success' && response.data.status === 'success') {
        eventBus.emit('notification', {
            message: 'action_exclude_success',
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Retries a specified action for a given process and list of ids.
 *
 * @param {ProcessType} process - The process type to perform the action on.
 * @param {Number[]} ids - An array of ids for which the action should be retried.
 * @param {ActionType} [type='action'] - The type of action to retry.
 * @return {Promise<APIResponse<AsyncProcessType | null>>} A promise that resolves to the result of the retry action or null if it fails.
 */
async function retryAction(process: ProcessType,
                           ids: (Number | String)[],
                           type: ActionType = 'action'
): Promise<APIResponse<AsyncProcessType | null>> {
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('POST', `/alfaomega-ebooks/api/retry-action/`, {
        process: process,
        ids: ids,
        type: type
    });
    appStore.setLoading(false);

    if (response.status == 'success') {
        eventBus.emit('notification', {
            message: 'retry_action_success',
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Initiates the import of new eBooks by making an API request to the specified endpoint.
 *
 * This function sets the application state to loading, performs the API request, handles
 * the response, and accordingly updates the application state and emits events.
 *
 * @return {Promise<APIResponse<AsyncProcessType | null>>} A promise that resolves to
 * the API response containing the asynchronous process type if the request is successful,
 * or null if an error occurs.
 */
async function importNewEbooks(): Promise<APIResponse<AsyncProcessType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('GET', `/alfaomega-ebooks/api/import-new-ebooks/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        eventBus.emit('notification', {
            message: response.message,
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return {} as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Updates the ebooks by sending a request to the '/alfaomega-ebooks/api/update-ebooks/' endpoint.
 * Handles loading state and error state within the application's store.
 * Emits notifications and refreshes actions based on the response.
 *
 * @return {Promise<APIResponse<AsyncProcessType | null>>} A promise that resolves to the API response containing the async process type or null in case of an error.
 */
async function updateEbooks(): Promise<APIResponse<AsyncProcessType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('GET', `/alfaomega-ebooks/api/update-ebooks/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        eventBus.emit('notification', {
            message: response.message,
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return {} as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Initiates a request to link products and updates the application store
 * with loading and error states. Notifies components about the status of
 * the process through an event bus.
 *
 * @return {Promise<APIResponse<AsyncProcessType | null>>} A promise that resolves with the response of the linking process,
 * either an API response object on success or null if an error occurred.
 */
async function linkProducts(): Promise<APIResponse<AsyncProcessType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('GET', `/alfaomega-ebooks/api/link-products/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        eventBus.emit('notification', {
            message: response.message,
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return {} as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

/**
 * Sets up the prices for eBooks according to a specified factor and value.
 *
 * @param {SetupPriceFactorType} factor - The factor to determine the price setup.
 * @param {Number} value - The value associated with the pricing factor.
 * @return {Promise<APIResponse<AsyncProcessType | null>>} A promise that resolves with the API response containing
 * the async process type data or null if an error occurs.
 */
async function setupEbooksPrice(factor: SetupPriceFactorType,
                                value: Number
): Promise<APIResponse<AsyncProcessType | null>> {
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('POST', `/alfaomega-ebooks/api/setup-prices/`, {
        factor: factor,
        value: value
    });

    appStore.setLoading(false);

    if (response.status == 'success') {
        eventBus.emit('notification', {
            message: response.message,
            type: 'success'
        });
        eventBus.emit('refreshActions', {process: process});
        return {} as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

export default {
    getProcessStatus,
    getProcessActions,
    clearQueue,
    deleteAction,
    retryAction,
    excludeAction,
    importNewEbooks,
    updateEbooks,
    linkProducts,
    setupEbooksPrice
};
