<script setup lang="ts">
  import { BookType } from '@/types';
  import { ref } from 'vue';

  const props = defineProps({
    data: { type: Object as () => BookType | null, default: null }
  });

  const emit = defineEmits<{ open: (payload: BookType) => void }>();

  const hover = ref(false);

  const handleClick = () => {
    emit('open', props.data);
  };
</script>

<template>
  <div class="col">
    <BCard
        class="px-1 py-1 border-2"
        :class="hover ? 'shadow-lg border-primary' : ''"
        v-if="data !== null"
        style="cursor: pointer;"
        :img-src="data.cover"
        :img-alt="data.title"
        no-body
        @click="handleClick"
        @mouseover="hover = true"
        @mouseleave="hover = false"
    />
  </div>
</template>

<style scoped>

</style>
