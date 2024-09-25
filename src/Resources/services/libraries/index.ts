import {request} from '../api';
import type {APIResponse} from '../types';
import {useAppStore} from '@/stores';
import {BooksQueryType, BookType, CatalogType} from '@/types';

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
        return null;
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
        return null;
    }
}

export default {
    getBooks,
    loadCatalog
};
