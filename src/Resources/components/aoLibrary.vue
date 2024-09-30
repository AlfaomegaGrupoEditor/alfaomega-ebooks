<script setup lang="ts">
    import {ref, computed, watch, onMounted} from 'vue';
    import {BookType, BooksQueryType} from '@/types';
    import {
        aoBook,
        aoSidebar,
        aoEmptyState,
        aoBooksSkeleton
    } from '@/components';
    import {useLibraryStore} from '@/stores';
    import {getValue, updateHistory} from '@/services/Helper';
    import AoHorizontalLoader from '@/components/aoHorizontalLoader.vue';

    const props = defineProps({
        query: {type: Object as () => BooksQueryType | null, default: null}
    });

    const libraryStore = useLibraryStore();
    const books = computed(() => libraryStore.getBooks);
    const meta = computed(() => libraryStore.getMeta);
    const query = computed(() => libraryStore.getQuery);
    const showSidebar = ref(false);
    const book = ref<BookType | null>(null);
    const currentPage = ref(props.query?.filter.currentPage || 1);
    const processing = ref(false);

    const toggleSidebar = (selectedBook: BookType) => {
        showSidebar.value = !showSidebar.value;
        book.value = selectedBook;
    };

    const onPageChange = async (event, pageNumber) => {
        processing.value = true;
        const newQuery = {
            ...query.value,
            filter: {
                ...query.value.filter,
                currentPage: pageNumber
            }
        };
        updateHistory(newQuery.filter);
        await libraryStore.dispatchSearchBooks(newQuery);
        processing.value = false;
    };

    watch(() => props.query, async (newVal) => {
        processing.value = true;
        await libraryStore.dispatchSearchBooks(newVal);
        processing.value = false;
    });

    onMounted(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const page = parseInt(getValue(urlParams.get('currentPage'), 1));
        if (page !== currentPage.value) {
            currentPage.value = page;
        }
    })
</script>

<template>
    <div class="ps-md-4">
        <div v-if="!books">
            <ao-books-skeleton />
        </div>
        <div v-else-if="books.length > 0">
            <ao-horizontal-loader :show="processing"/>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mt-0">
                <ao-book
                    v-for="book in books"
                    :key="book.id"
                    :data="book"
                    :disabled="processing"
                    @open="()=> toggleSidebar(book)"
                />
            </div>
            <div class="mt-5 d-flex flex-row justify-content-center">
                <BPagination
                    v-if="meta.pages > 1 || processing"
                    v-model="currentPage"
                    :total-rows="meta.total"
                    :per-page="query.filter.perPage"
                    :prev-text="$t('previous')"
                    :next-text="$t('next')"
                    :hide-goto-end-buttons="true"
                    :hide-goto-start-buttons="true"
                    pills
                    @page-click="onPageChange"
                />
            </div>
            <ao-sidebar
                v-model:show="showSidebar"
                :data="book"
            />
        </div>
        <div v-else class="row mt-4">
            <ao-empty-state
                :title="$t('no_books_found')"
                :description="$t('no_books_found_description')"
            />
        </div>
    </div>
</template>

<style scoped>
</style>
