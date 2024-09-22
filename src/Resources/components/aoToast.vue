<script setup lang="ts">
  import {ColorVariantType} from '@/types';
  import {ref, watch, computed} from 'vue';

  const props = defineProps({
    active: Boolean,
    title: {type: String, default: ''},
    variant: { type: String as () => ColorVariantType, default: 'success' },
    content: {type: String, default: 'A message here'}
  });

  const showToast = ref(false);
  const toast = ref(null);
  const icon = computed(() => {
    switch (props.variant) {
      case 'success':
        return 'fa-check-circle';
      case 'primary':
        return 'exclamation-circle';
      case 'warning':
        return 'fa-exclamation-triangle';
      default:
        return 'fa-info-circle';
    }
  });

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
        <div class="row">
          <div class="col-1">
            <i class="fas fs-4" :class="icon"></i>
          </div>
          <div class="col">
            <div class="fs-8 fw-bold" v-if="title"> {{ title }} </div>
            <span class="fs-8">
              {{ content }}
            </span>
          </div>
        </div>
      </BToast>
    </div>
  </Teleport>
</template>

<style scoped>

</style>
