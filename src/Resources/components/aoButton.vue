<template>
  <BButton
      variant="primary"
      class="rounded-pill mx-1 px-2"
      size="sm"
  >
    <span class="mx-2 d-inline-block">
      <i class="fs-5 fa fa-file-pdf ml-2" :class="icon"></i>
      <span class="mx-2 fs-6 d-inline-block">{{ $t(caption) }}</span>
    </span>
  </BButton>
</template>


<script setup lang="ts">
  import { defineProps } from 'vue';

  const props = defineProps< {
    caption: string;
    icon: string;
  }>()

</script>

<style scoped>

</style>
