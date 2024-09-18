<template>
  <b-card :title="$t('add_ebooks')">
    <b-card-text>
      {{ $t('add_sample_text') }}
    </b-card-text>

    <div class="mb-4">
      <b-form-input
          v-model="code"
          placeholder="XXXX-XXXX-XXXX"
          type="text"
      ></b-form-input>
    </div>

    <b-button
        variant="primary"
        size="sm"
        @click="test"
        class="float-end"
    >
      {{ $t('apply_btn') }}
    </b-button>
  </b-card>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  import { useAppStore } from '@/stores/appStore';

  const appStore = useAppStore();
  const code = ref('');
  const test = () => { appStore.testLoading() };
</script>
