import { defineStore } from "pinia";
import type { State } from '@/services/libraries/types'
import { API } from '@/services';
import {BooksQueryType} from '@/types';

export const useLibraryStore = defineStore('libraryStore', {
  state: (): State => (
      {
        books: null
      }
  ),

  getters: {
    getBooks: (state) => state.books
  },

  actions: {
    async dispatchSearchBooks(data: BooksQueryType)
    {
      const response = await API.book.getBook(payload.isbn);
      if (response.status === 'success' && response.data) {
        response.data.loaded = false;
        response.data.gotoPage = payload.gotoPage;
        this.current = response.data;
      }
    }
  }
});
