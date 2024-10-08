import {request} from '../api';
import type {APIResponse} from '../types';
import type {CheckApiType} from './types';
import {useAppStore} from '@/stores';

/**
 * Checks the API status.
 */
async function checkApi(): Promise<APIResponse<CheckApiType>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<CheckApiType>>('GET', `/alfaomega-ebooks/api/check/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<CheckApiType>;
    } else {
        appStore.setError(response.message);
        return response as APIResponse<CheckApiType>;
    }
}

export default {
    checkApi
};
