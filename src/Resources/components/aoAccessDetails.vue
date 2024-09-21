<script setup lang="ts">
  import { defineProps, computed } from 'vue';
  import { AccessType, StatusType } from '@/types';

  const props = defineProps({
    type: { type: String as AccessType, default: 'purchase' },
    status: { type: String as StatusType, default: 'created' },
    added_at: { type: String, default: '01/01/2024' },
    valid_until: { type: String, default: '31/12/2024' },
    book_url: { type: String, default: '' }
  });

  const typeVariant = computed(() => {
    switch (props.type) {
      case 'purchase':
        return 'danger';
      case 'sample':
        return 'warning';
    }
  });

  const statusVariant = computed(() => {
    switch (props.status) {
      case 'created':
        return 'warning';
      case 'active':
        return 'success';
      case 'expired':
        return 'warning';
      case 'cancelled':
        return 'secondary';
      default:
        return 'danger';
    }
  });
</script>

<template>
  <div class="mt-4 mx-4 fs-8 border-top py-3 px-4 border-bottom">
    <!-- Access type -->
    <div class="row justify-content-start mb-2">
      <div class="col">
        <span class="text-muted">
          {{ $t('access_type') }}:
        </span>
        <BBadge
            class="d-inline-block fs-7"
            :variant="typeVariant"
        >
          {{$t(type)}}
        </BBadge>
      </div>
    </div>

    <!-- Status -->
    <div class="row justify-content-start mb-2">
      <div class="col">
        <span class="text-muted">
          {{ $t('status') }}:
        </span>
        <BBadge
            class="d-inline-block fs-7"
            :variant="statusVariant"
        >
          {{$t(status)}}
        </BBadge>
      </div>
    </div>

    <!-- Added at -->
    <div class="row justify-content-start mb-2">
      <div class="col">
        <span class="text-muted">
          {{ $t('added_at') }}:
        </span>
        <BBadge
            class="d-inline-block fs-7"
            variant="light"
        >
          {{$t(added_at)}}
        </BBadge>
      </div>
    </div>

    <!-- Valid until -->
    <div class="row justify-content-start mb-2">
      <div class="col">
        <span class="text-muted">
          {{ $t('valid_until') }}:
        </span>
        <BBadge
            class="d-inline-block fs-7"
            variant="light"
        >
          {{$t(valid_until)}}
        </BBadge>
      </div>
    </div>
  </div>

  <BLink
      :href="book_url"
      target="_blank"
      underline-opacity="0"
      underline-opacity-hover="100"
      underline-offset="3"
      class="text-primary d-block mt-4 mx-4 fs-8 fw-bold text-uppercase"
  >
    {{ $t('book_details') }}
    <i class="fas fa-external-link-alt"></i>
  </BLink>
</template>

<style scoped>

</style>
