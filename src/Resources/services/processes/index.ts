import {request} from '../api';
import type {APIResponse} from '../types';
import {useAppStore} from '@/stores';
import {
    AsyncProcessType,
    ProcessItem,
    ProcessStatusType,
    ProcessType
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
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

export default {
    getProcessStatus,
    getProcessActions,
    clearQueue
};
