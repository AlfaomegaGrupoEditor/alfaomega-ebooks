import {defineStore} from 'pinia';
import type {State} from '@/services/libraries/types';
import {API} from '@/services';
import {BooksQueryType} from '@/types';

export const useLibraryStore = defineStore('libraryStore', {
    state: (): State => (
        {
            books: null,
            query: {
                category: null,
                filter: {
                    searchKey: null,
                    accessType: null,
                    accessStatus: null,
                    order: {
                        field: 'title',
                        direction: 'asc'
                    },
                    perPage: 8
                },
                page: 1,
                userId: null
            },
            meta: {
                total: 0,
                pages: 0,
                current_page: 1
            },
            catalog: []
        }
    ),

    getters: {
        getBooks: (state) => state.books,
        getQuery: (state) => state.query,
        getMeta: (state) => state.meta,
        getCatalog: (state) => useLibraryStore().buildTree(state.catalog)
    },

    actions: {
        /**
         * Dispatches a search request to the API.
         * @param query
         */
        async dispatchSearchBooks(query: BooksQueryType)
        {
            this.query = query;
            this.books = null; // Reset the books list
            const response = await API.library.getBooks(query);
            if (response.status === 'success' && response.data) {
                this.books = response.data;
                this.meta = response.meta || {
                    total: 0,
                    pages: 0,
                    current_page: 1
                };
            }
        },

        /**
         * Loads the catalog from the API.
         */
        async dispatchLoadCatalog()
        {
            const response = await API.library.loadCatalog();
            if (response.status === 'success' && response.data) {
                this.catalog = response.data;
            }
        },

        /**
         * Builds the catalog tree.
         * @param catalog
         */
        buildTree(catalog: any)
        {
            const traverse = (node: any) => {
                console.log(node);
                return Object.keys(node).reduce((acc, key) => {
                    const item = node[key];
                    acc[key] = {
                        text: item.title,
                        children: traverse(item.children)
                    };
                    return acc;
                }, {});
            };

            const catalogTree = traverse(catalog);
            console.log(catalogTree);
            return {
                all_ebooks: {
                    text: 'all_ebooks',
                    state: {
                        opened: true,
                        checked: true,
                    },
                    children: catalogTree
                },
                purchased: {
                    text: 'purchased',
                },
                samples: {
                    text: 'samples',
                },
            };
        }
    },
});
