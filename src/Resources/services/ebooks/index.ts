import {request} from '../api';
import type {APIResponse} from '../types';
import {useAppStore} from '@/stores';
import {
    AccessCodeInfoType,
    EbookAccessInfoType,
    EbookInfoType,
    ProductsInfoType,
    RedeemStatusType
} from '@/types';

async function getEbooksInfo(): Promise<APIResponse<EbookInfoType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<EbookInfoType[]>>('GET', `/alfaomega-ebooks/api/ebooks-info/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<EbookInfoType[]>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

async function getProductsInfo(): Promise<APIResponse<ProductsInfoType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<ProductsInfoType[]>>('GET', `/alfaomega-ebooks/api/products-info/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<ProductsInfoType[]>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

async function getAccessInfo(): Promise<APIResponse<EbookAccessInfoType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<EbookAccessInfoType[]>>('GET', `/alfaomega-ebooks/api/access-info/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<EbookAccessInfoType[]>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}

async function getCodesInfo(): Promise<APIResponse<AccessCodeInfoType | null>>
{
    const appStore = useAppStore();
    appStore.setError(null);
    appStore.setLoading(true);

    const response = await request<APIResponse<AccessCodeInfoType[]>>('GET', `/alfaomega-ebooks/api/codes-info/`);
    appStore.setLoading(false);

    if (response.status == 'success') {
        return response.data as APIResponse<AccessCodeInfoType[]>;
    } else {
        appStore.setError(response.message);
        return null;
    }
}


export default {
    getEbooksInfo,
    getProductsInfo,
    getCodesInfo,
    getAccessInfo,
};
