import {request} from '../api';
import type {APIResponse} from '../types';
import {useAppStore} from '@/stores';
import {
    AsyncProcessType,
    ProcessType
} from '@/types';

async function getProcessStatus(process: ProcessType): Promise<APIResponse<AsyncProcessType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AsyncProcessType>>('GET', `/alfaomega-ebooks/api/process-info/${process}`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<AsyncProcessType>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

export default {
    getProcessStatus
};
