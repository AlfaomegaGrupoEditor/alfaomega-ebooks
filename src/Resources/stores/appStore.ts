import { defineStore } from 'pinia';
import axios, { AxiosError } from 'axios';

interface Post {
    id: number;
    title: {
        rendered: string;
    };
}

interface StoreState {
    data: Post[];
    isLoading: boolean;
    error: string | null;
}

export const useAppStore = defineStore('appStore', {
    state: (): StoreState => ({
        data: [],
        isLoading: false,
        error: null,
    }),

    actions: {
        async fetchData(): Promise<void> {
            this.isLoading = true;
            try {
                const response = await axios.get<Post[]>('/wp-json/wp/v2/posts');
                this.data = response.data;
            } catch (error) {
                const axiosError = error as AxiosError;
                this.error = axiosError.message;
            } finally {
                this.isLoading = false;
            }
        },
        async saveData(newData: Post): Promise<void> {
            this.isLoading = true;
            try {
                const response = await axios.post<Post>('/wp-json/wp/v2/posts', newData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': wpApiSettings.nonce, // Assuming wpApiSettings is localized
                    },
                });
                this.data.push(response.data);
            } catch (error) {
                const axiosError = error as AxiosError;
                this.error = axiosError.message;
            } finally {
                this.isLoading = false;
            }
        },
    },
});
