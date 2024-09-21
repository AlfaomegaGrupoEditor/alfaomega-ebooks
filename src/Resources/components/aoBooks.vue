<script setup lang="ts">
  import {onMounted, ref} from 'vue';
  import { BookType } from '@/types';
  import { aoBook } from '@/components';

  const books = ref<BookType[]>([]);

  onMounted(() => {
    books.value = [
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
      }
    ]
  });
</script>

<template>
  <div class="row mt-5">
    <div class="col">
      <ao-book v-for="book in books" :key="book.id" :book="book" />
    </div>
  </div>
</template>

<style scoped>

</style>
