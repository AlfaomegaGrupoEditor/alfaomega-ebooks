<script setup lang="ts">
  import { defineProps, defineEmits } from 'vue';
  import { ColorVariant, SizeVariant } from '@/types';

  const props = defineProps({
    caption: { type: String, default: 'caption' },
    icon: { type: String, default: 'fa-user' },
    disabled: { type: Boolean, default: false },
    color: { type: String as () => ColorVariant, default: 'primary' },
    size: { type: String as () => SizeVariant, default: 'sm' }
  });

  const emit = defineEmits(['click']);

  const handleClick = () => {
    emit('click');
  };
</script>

<template>
  <BButton
      :variant="color"
      class="rounded-pill mx-1 px-2"
      :size="size"
      :disabled="disabled"
      @click="handleClick"
  >
    <span class="mx-2 d-inline-block">
      <i class="fs-7 fa ml-2" :class="icon"></i>
      <span class="mx-2 fs-8 fw-bold d-inline-block">{{ $t(caption) }}</span>
    </span>
  </BButton>
</template>

<style scoped>

</style>
