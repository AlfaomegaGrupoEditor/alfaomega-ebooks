/**
 * `SearchResultType` is a type that represents the search result of a paginated list.
 */
type SearchResultType = {
  total: number;
  current_page: number;
  pages: number;
}

/**
 * `APIResponse` is a generic type that represents the response of an API request.
 */
type APIResponse<T> = {
  status: 'success' | 'fail';
  code: number;
  data: T | null;
  meta?: SearchResultType | null;
  message?: string;
}

/**
 * Http request method.
 */
type RequestMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';

export {
  APIResponse,
  SearchResultType,
  RequestMethod,
}
