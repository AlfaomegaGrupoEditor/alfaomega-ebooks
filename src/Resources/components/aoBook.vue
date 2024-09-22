<script setup lang="ts">
  import { BookType } from '@/types';
  import {onMounted, ref} from 'vue';

  const props = defineProps({
    data: { type: Object as () => BookType | null, default: null }
  });

  const emit = defineEmits<{ open: (payload: BookType) => void }>();
  const hover = ref(false);
  const built = ref(false);

  const handleClick = () => {
    emit('open', props.data);
  };

  onMounted(() => {
    built.value = true;
  });

</script>

<template>
  <transition name="fade">
    <div class="col" v-if="built">
      <BCard
          class="px-1 py-1 border-2"
          :class="hover ? 'shadow-lg border-primary' : ''"
          v-if="data !== null"
          :img-src="data.cover"
          :img-alt="data.title"
          no-body
          role="button"
          @click="handleClick"
          @mouseover="hover = true"
          @mouseleave="hover = false"
      />
    </div>
  </transition>
</template>

<style scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
    opacity: 0;
  }
</style>
