import { defineStore } from "pinia";
import type { AppInitPayload, State } from '@/services/apps/types';
import { API } from '@/services';
import { eventBus } from '@/events';

export const useAppStore = defineStore('appStore', {
  /**
   * The state of the application store.
   * @type {State}
   * @property {string | undefined} error - The current error message, if any.
   * @property {boolean} loading - Indicates whether the application is currently loading.
   */
  state: (): State => ({
    error: undefined,
    loading: false,
    config: {
      baseURL: window.wpApiSettings.root,
      mode: 'no-cors',
      headers: {
        Authorization: `X-WP-Nonce ${window.wpApiSettings.nonce}`,
        'Access-Control-Allow-Origin': '*',
        'Content-Type': 'application/json;x-www-form-urlencoded',
        'Cache-Control': 'no-cache',
        Accept: 'application/json',
      },
      error: undefined,
      loading: false,
      withCredentials: true,
      credentials: 'same-origin'
    }
  }),

  getters: {
    getError: (state) => state.error,
    getLoading: (state) => state.loading
  },

  actions: {
    /**
     * Initializes the application store.
     * @param {AppInitPayload} data - The data to initialize the store with.
     */
    async checkApi(data: AppInitPayload) {
      await this.dispatchCheckApi();
    },

    /**
     * Checks the API for availability.
     */
    async dispatchCheckApi() {
      const response = await API.app.checkApi();
      if (response.status === 'success' && response.data) {
        this.theme = response.data;
        eventBus.emit('apiSuccess', {});
      }
    },

    /**
     * Notifies the user of validation errors.
     * @param status
     * @param data
     */
    notifyValidationErrors(status: Number, data: any) {
      if (status === 400) {
        const validationErrors = data.data.errors
        for (const errors in validationErrors) {
          for (const message in validationErrors[errors]) {
            eventBus.emit('notification', {
              message: validationErrors[errors][message],
              type: 'error'
            });
          }
        }
      } else {
        eventBus.emit('notification', {
          message: data.message,
          type: 'error'
        });
      }
    },

    /**
     * Sets the loading state of the application store.
     *
     * @param {boolean} loading - The loading state to set.
     */
    setLoading(loading: boolean) {
      this.loading = loading;
    },

    /**
     * Sets the error state of the application store.
     *
     * @param {any} error - The error to set. Can be of any type.
     */
    setError(error: any) {
      this.error = error;
    }
  }
});
