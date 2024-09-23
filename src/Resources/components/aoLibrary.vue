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
  const showSidebar = ref(false);
  const book = ref<BookType | null>(null);

  const currentPage = ref(1);
  const perPage = 8;
  const totalRows = 100;

  const toggleSidebar = (selectedBook: BookType) => {
    showSidebar.value = !showSidebar.value;
    book.value = selectedBook;
  };

  watch(() => props.query, async (newVal) => {
    console.log('library', newVal);
    await libraryStore.dispatchSearchBooks(newVal);
  });

  /*async function fetchData(){
    return new Promise(async (resolve) => {
      const data = [
        {
          id: 1,
          title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
          cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-3.png',
          download: true,
          read: true,
          accessType: 'purchase',
          status: 'active',
          addedAt: '2024-07-01',
          validUntil: null,
          url: 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/'
        },
        {
          id: 2,
          title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
          cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-4.png',
          download: true,
          read: true,
          accessType: 'purchase',
          status: 'active',
          addedAt: '2024-07-01',
          validUntil: null,
          url: 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/'
        },
        {
          id: 3,
          title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
          cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-2.png',
          download: true,
          read: true,
          accessType: 'purchase',
          status: 'active',
          addedAt: '2024-07-01',
          validUntil: null,
          url: 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/'
        },
        {
          id: 4,
          title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
          cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-1.png',
          download: true,
          read: true,
          accessType: 'purchase',
          status: 'active',
          addedAt: '2024-07-01',
          validUntil: null,
          url: 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/'
        },
        {
          id: 5,
          title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
          cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/2-1.png',
          download: true,
          read: true,
          accessType: 'purchase',
          status: 'active',
          addedAt: '2024-07-01',
          validUntil: null,
          url: 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/'
        },
        {
          id: 6,
          title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
          cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/3-1.png',
          download: true,
          read: true,
          accessType: 'purchase',
          status: 'active',
          addedAt: '2024-07-01',
          validUntil: null,
          url: 'https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/'
        }
      ];
      setTimeout(async () => resolve(data), 500);
    });
  }

  watch(() => props.query, async (newVal) => {
    books.value = null;
    books.value = await fetchData(); // fetch data from server
  });

  books.value = await fetchData();*/
</script>

<template>
  <div v-if="books">
    <div v-if="books.length > 0">
      <div class="row row-cols-1 row-cols-md-4 g-4 mt-0">
        <ao-book
            v-for="book in books"
            :key="book.id"
            :data="book"
            @open="()=> toggleSidebar(book)"
        />
      </div>
      <div class="mt-5 d-flex flex-row justify-content-center">
        <BPagination
            v-model:currentPage="currentPage"
            :totalRows="totalRows"
            :perPage="perPage"
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
