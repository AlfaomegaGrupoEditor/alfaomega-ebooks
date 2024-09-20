<script setup lang="ts">
  import { ref } from 'vue';
  import { useAppStore } from '@/stores/appStore';
  import { aoButton } from '@/components';

  const appStore = useAppStore();
  const code = ref('');
  const test = () => { appStore.testLoading() };
</script>

<template>
  <b-card>
    <b-card-title>
      <span class="fs-8">
        {{ $t('add_ebooks') }}
      </span>
    </b-card-title>

    <b-card-text class="fs-8">
      {{ $t('add_sample_text') }}
      {{ $t('purchase_note') }}
    </b-card-text>

    <div class="mb-4">
      <BFormInput
          class="form-control-sm"
          v-model="code"
          placeholder="XXXX-XXXX-XXXX"
          type="text"
      />
    </div>

    <div class="row">
      <div class="col">
        <ao-button
            class="float-end"
            icon="fa-key"
            :caption="$t('apply_btn')"
            :disabled="false"
            size="sm"
            @click="() => console.log('click apply')"
        />
      </div>
    </div>

    <div class="card-body fs-8 text-muted d-none">
      <span class="fw-bold">{{ $t('note') }}: </span>
      <span>{{ $t('purchase_note') }}</span>
    </div>
  </b-card>
</template>

<style scoped>

</style>
