import { defineStore } from "pinia";
import type { AppInitPayload, State } from '@/services/apps/types';
import { API } from '@/services';
import { eventBus } from '@/events';

/**
 * The application store.
 * @function useAppStore
 * @returns {useAppStore} The application store.
 * @property {function(): State} state - A function that returns the state of the application store.
 * @property {object} getters - The getters of the application store.
 * @property {object} actions - The actions of the application store.
 */
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
  }),

  getters: {
    getError: (state) => state.error,
    getLoading: (state) => state.loading
  },

  actions: {
    /**
     * Initializes the application with the provided data.
     * Sets the configuration of the application store with the provided data.
     * Fetches the theme with the provided name.
     *
     * @async
     * @param {AppInitPayload} data - The data to initialize the application with.
     * @property {string} data.librarySrc - The source of the library.
     * @property {string} data.token - The token for authorization.
     * @property {string} data.teacher - The status of the teacher feature ('enabled' or 'disabled').
     * @property {string} data.referrer - The referrer of the request.
     * @property {string} data.evaluations - The status of the evaluations feature ('active' or 'inactive').
     * @property {string} data.video - The ID of the YouTube video to embed.
     * @property {string} data.themeName - The name of the theme to fetch.
     * @throws Will throw an error if the theme fetch fails.
     */
    async initApp(data: AppInitPayload) {
      const coverPath = import.meta.env.VITE_APP_ASSETS;

      this.config = {
        library: data.librarySrc,
        coverPath: coverPath + '/covers/',
        token: data.token,
        baseURL: data.librarySrc + '/api/',
        showTeacher: data.teacher === 'enabled',
        mode: 'no-cors',
        headers: {
          Authorization: 'Bearer ' + data.token,
          'Access-Control-Allow-Origin': '*',
          'Content-Type': 'application/json;x-www-form-urlencoded',
          'Cache-Control': 'no-cache',
          Accept: '/'
        },
        withCredentials: true,
        credentials: 'same-origin',
        referrer: data.referrer,
        evaluations: data.evaluations === 'active',
        video: 'https://www.youtube.com/embed/' + data.video
      }

      await this.dispatchGetTheme(data.themeName);
    },

    /**
     * Fetches the theme from the API using the provided theme name.
     * Updates the store's state with the fetched theme.
     * Emits an event 'themeLoaded' after successful fetch and update.
     *
     * @async
     * @param {string} theme - The name of the theme to fetch.
     * @throws Will throw an error if the API call fails.
     */
    async dispatchGetTheme(theme: string) {
      const response = await API.app.getTheme(theme);
      if (response.status === 'success' && response.data) {
        this.theme = response.data;
        eventBus.emit('themeLoaded', {});
      }
    },

    /**
     * Handles validation errors from API responses.
     * If the status is 400, it iterates over the validation errors and emits a notification for each error message.
     * If the status is not 400, it emits a notification with the response message.
     *
     * @param {Number} status - The status code of the API response.
     * @param {any} data - The data from the API response.
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
