<script setup lang="ts">
  import {ColorVariantType} from '@/types';
  import {ref, watch} from 'vue';

  const props = defineProps({
    active: Boolean,
    title: {type: String, default: ''},
    variant: { type: String as () => ColorVariantType, default: 'success' },
    content: {type: String, default: 'A message here'}
  });

  const showToast = ref(false);
  const toast = ref(null);

  watch(() => props.active, (newVal) => {
    showToast.value = newVal;
  });

</script>

<template>
  <Teleport to="body">
    <div class="top-0 end-0 toast-container position-fixed p-3 ao-toast">
      <BToast
          v-model="showToast"
          :variant="variant"
      >
        <h2 class="fs-6" v-if="title"> {{ title }}: </h2>
        {{ content }}
      </BToast>
    </div>
  </Teleport>
</template>

<style scoped>

</style>
