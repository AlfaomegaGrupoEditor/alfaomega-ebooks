import { request } from '../api'
import type { APIResponse } from "../types";
import type { AppTheme } from "./types";
import { useAppStore } from '@/stores'

async function checkApi(): Promise<APIResponse<AppTheme>>{
  const appStore = useAppStore();
  appStore.setError(null);
  appStore.setLoading(true);
  const response = await request<APIResponse<AppTheme>>('GET', `/ebook-access/check`);
  if (response.status === 'success') {
    const content: any = response.data?.data; // To flat the nested data structure
    appStore.setLoading(false);
    return {
      ...response,
      ... {data: content.data as AppTheme}
    } as APIResponse<AppTheme>;
  } else {
    appStore.setLoading(false);
    appStore.setError(response.message);
    return response as APIResponse<AppTheme>
  }
}

export default {
  checkApi,
};
