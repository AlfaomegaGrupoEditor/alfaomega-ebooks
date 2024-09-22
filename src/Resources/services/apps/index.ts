import { request } from '../api'
import type { APIResponse } from "../types";
import type { CheckApiType } from "./types";
import { useAppStore } from '@/stores'

async function checkApi(): Promise<APIResponse<CheckApiType>>
{
  const appStore = useAppStore();
  appStore.setError(null);
  appStore.setLoading(true);

  const response = await request<APIResponse<CheckApiType>>('GET', `/ebook-access/check`);
  appStore.setLoading(false);

  if (response.status == 'success') {
    return {...response, ...{data: content}} as APIResponse<CheckApiType>;
  } else {
    appStore.setError(response.message);
    return response as APIResponse<CheckApiType>;
  }
}

export default {
  checkApi,
};
