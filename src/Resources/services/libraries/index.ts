import { request } from '../api'
import type { APIResponse } from "../types";
import type { Book } from './types'
import { useAppStore } from '@/stores'

/**
 * Fetches a book with the provided ISBN from the API.
 * Sets the error state of the application store to null and the loading state to true before making the request.
 * If the request is successful, it flattens the nested data structure, sets the loading state to false, and returns the response.
 * If the request fails, it sets the loading state to false, updates the error state with the response message, notifies validation errors, and returns the response.
 *
 * @async
 * @param {string} isbn - The ISBN of the book to fetch.
 * @returns {Promise<APIResponse<Book>>} A Promise resolving to an APIResponse object.
 * @throws Will throw an error if the API call fails.
 */
async function getBook(isbn: string): Promise<APIResponse<Book>>{
  const appStore = useAppStore();
  appStore.setError(null);
  appStore.setLoading(true);
  const response = await request<APIResponse<Book>>('GET', `/user/book/${isbn}`);
  if (response.status === 'success') {
    const content: any = response.data?.data; // To flat the nested data structure
    appStore.setLoading(false);
    return {
      ...response,
      ... {data: content as Book}
    } as APIResponse<Book>;
  } else {
    appStore.setLoading(false);
    appStore.setError(response.message);
    appStore.notifyValidationErrors(response.code, response.data)
    return response as APIResponse<Book>
  }
}

export default {
  getBook,
};
