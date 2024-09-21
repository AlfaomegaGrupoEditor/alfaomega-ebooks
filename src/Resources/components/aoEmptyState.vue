<script setup lang="ts">

const publicUrl = import.meta.env.VITE_PUBLIC_URL;
const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  }
});
</script>

<template>
  <div class="col d-flex flex-column justify-content-center align-items-center text-center ">
    <img
        :src="`${publicUrl}/img/empty_state.png`"
        alt="Empty State"
        class="my-4"
        style="max-width: 250px"
    />
    <div class="w-50 mt-2">
      <h2 class="fs-6 text-primary">{{ title }}</h2>
      <p class="fs-7 text-muted">{{ description }}</p>
    </div>
  </div>
</template>

<style scoped>
</style>
