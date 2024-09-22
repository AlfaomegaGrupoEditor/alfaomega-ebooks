/**
 * `APIResponse` is a generic type that represents the response of an API request.
 */
export type APIResponse<T> = {
  status: 'success' | 'fail';
  code: number;
  data: T | null;
  message?: string;
}

/**
 * Http request method.
 */
export type RequestMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
