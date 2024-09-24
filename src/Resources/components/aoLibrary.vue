<script setup lang="ts">
  import {ref, computed, watch} from 'vue';
  import {BookType, BooksQueryType} from '@/types';
  import {
    aoBook,
    aoSidebar,
    aoEmptyState,
    aoBooksSkeleton
  } from '@/components';
  import { useLibraryStore } from '@/stores'

  const props = defineProps({
    query: { type: Object as () => BooksQueryType | null, default: null }
  });

  const libraryStore = useLibraryStore();
  const books = computed(() => libraryStore.getBooks);
  const meta = computed(() => libraryStore.getMeta);
  const showSidebar = ref(false);
  const book = ref<BookType | null>(null);

  const currentPage = ref(meta.page);

  const toggleSidebar = (selectedBook: BookType) => {
    showSidebar.value = !showSidebar.value;
    book.value = selectedBook;
  };

  watch(() => props.query, async (newVal) => {
    await libraryStore.dispatchSearchBooks(newVal);
  });
</script>

<template>
  <div v-if="books">
    <div v-if="books.length > 0">
      <div class="row row-cols-1 row-cols-md-4 g-4 mt-3">
        <ao-book
            v-for="book in books"
            :key="book.id"
            :data="book"
            @open="()=> toggleSidebar(book)"
        />
      </div>
      <div class="mt-5 d-flex flex-row justify-content-center">
        <BPagination
            v-if="meta.pages > 1"
            v-model:currentPage="currentPage"
            :totalRows="meta.total"
            :perPage="meta.perPage"
            limit="3"
            :prev-text="$t('previous')"
            :next-text="$t('next')"
            :hide-goto-end-buttons="true"
            :hide-goto-start-buttons="true"
            pills
            @change="onPageChange"
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
  <div v-else>
    <ao-books-skeleton />
  </div>
</template>

<style scoped>
</style>
