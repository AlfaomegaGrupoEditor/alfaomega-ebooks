<script setup lang="ts">
  import { BookType } from '@/types';

  const props = defineProps({
    book: { type: BookType, default: null }
  });

  const emit = defineEmits<{ open: (payload: BookType) => void }>();

  const handleClick = () => {
    emit('open', props.book);
  };
</script>

<template>
  <BCard
      v-if="book !== null"
      :img-src="book.cover"
      :img-alt="book.title"
  />
</template>

<style scoped>

</style>
