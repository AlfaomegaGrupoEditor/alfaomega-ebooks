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
  <div class="empty-state-container">
    <img :src="`${publicUrl}/img/empty_state.png`" alt="Empty State" class="empty-state-image" />
    <h2 class="empty-state-title">{{ title }}</h2>
    <p class="empty-state-description">{{ description }}</p>
  </div>
</template>

<style scoped>
.empty-state-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.empty-state-image {
  max-width: 100%;
  height: auto;
}

.empty-state-title {
  margin-top: 20px;
  font-size: 24px;
  font-weight: bold;
}

.empty-state-description {
  margin-top: 10px;
  font-size: 16px;
  color: #666;
}
</style>
