import {request} from '../api';
import type {APIResponse} from '../types';
import {useAppStore} from '@/stores';
import {BooksQueryType, BookType, CatalogType, RedeemStatusType} from '@/types';

/**
 * Checks the API status.
 */
async function getBooks(query: BooksQueryType): Promise<APIResponse<BookType[] | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<BookType[]>>('POST', `/alfaomega-ebooks/api/books/`, query);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<BookType[]>;
    } else {
        appStore.setError(response.message);
        return { status: 'fail', data: null, message: response.message } as APIResponse<null>;
    }
}

/**
 * Loads the user catalog.
 */
async function loadCatalog(): Promise<APIResponse<CatalogType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<CatalogType>>('GET', `/alfaomega-ebooks/api/catalog/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<CatalogType>;
    } else {
        appStore.setError(response.message);
        return { status: 'fail', data: null, message: response.message } as APIResponse<null>;
    }
}

/**
 * Applies a code to the user's account.
 *
 * @param {string} code - The code to be applied.
 * @returns {Promise<APIResponse>} - The API response.
 */
async function applyCode(code: string): Promise<APIResponse<RedeemStatusType|null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<RedeemStatusType>>('POST', `/alfaomega-ebooks/api/redeem`, { code: code });
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<RedeemStatusType>;
    } else {
        appStore.setError(response.message);
        return { status: 'fail', data: null, message: response.message } as APIResponse<null>;
    }
}

export default {
    getBooks,
    loadCatalog,
    applyCode
};
