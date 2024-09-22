import axios, { type AxiosError } from 'axios'
import { useAppStore } from '@/stores/app'
import { computed } from 'vue'
import { eventBus } from '@/events'
import type { RequestMethod } from '@/services/types'
import { empty } from '@/services/Helper'

/**
 * The axios instance.
 */
const instance = axios.create({
  baseURL: window.wpApiSettings.root,
});

/**
 * Returns an instance of axios with the app configuration.
 */
export default instance;

/**
 * Returns an instance of axios with the app configuration.
 */
export const getInstance = () => {
  const appStore = useAppStore();
  const appConfig = computed(() => appStore.getConfig);
  const config = appConfig.value;

  if (config === undefined) {
    return instance;
  }

  return axios.create(config);
};

/**
 * Sends a request to the API.
 * @param method - The HTTP method to use.
 * @param url - The URL to send the request to.
 * @param data - The data to send with the request.
 * @returns A Promise resolving to the response data.
 */
export const request = async <T extends { data?: any }>(method: RequestMethod , url: string, data: any = []) => {
  try {
    const http = getInstance();
    let response;
    switch (method) {
      case 'GET':
        response = await http.get<T>(url);
        break;
      case 'POST':
        response = await http.post<T>(url, data);
        break;
      case 'PUT':
        response = await http.put<T>(url, data);
        break;
      case 'DELETE':
        response = await http.delete<T>(url);
        break;
      case 'PATCH':
        response = await http.patch<T>(url, data);
        break;
      default:
        response = await http.get<T>(url);
        break;
    }
    if (response.status === 200 && !empty(response.data)) {
      return {
        status: 'success',
        code: 200,
        data: response.data,
        message: 'Request successful.'
      };
    } else {
      const error = empty(response.data)
        ? 'No data found!'
        : (response.data.data?.message || 'An error occurred while fetching the data.')

      eventBus.emit('notification', {
        message: error,
        type: 'error'
      })
      return {
        status: 'fail',
        code: 400,
        data: null,
        message: empty(response.data) ? 'No data found!' : error,
      };
    }
  } catch (error) {
    const _error = error as AxiosError<string>;
    /*eventBus.emit('notification', {
      message: _error?.message || 'An error occurred while fetching the data.',
      type: 'error'
    })*/
    return {
      status: 'fail',
      code: 400,
      data: null,
      message: error,
    };
  }
}
