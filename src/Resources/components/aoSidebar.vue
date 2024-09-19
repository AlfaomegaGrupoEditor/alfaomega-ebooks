<template>
  <BOffcanvas
      v-model="show"
      class="ao-sidebar"
      :placement="placement"
      style="z-index: 200000"
      :hide-backdrop="true"
      :header="false"
      :shadow="true"
  >
    <template #title>
      <span class="text-primary">
        {{ data.title }}
      </span>
    </template>

    <div class="mx-4">
      <img
          class="img-thumbnail"
          :src="data.cover"
          :alt="data.title"
      />
    </div>

    <div class="mt-4 d-flex justify-content-center">
      <ao-button icon="fa-file-pdf" :caption="$t('download')"/>
      <ao-button icon="fa-wifi" :caption="$t('read_online')"/>
    </div>

  </BOffcanvas>
  <BButton @click="click" class="m-2">Show end</BButton>
</template>

<script setup lang="ts">
  import {ref} from 'vue';
  import { aoButton } from '@/components';

  const show = ref(false);
  const placement = ref('start');

  const click = (place = 'start') => {
    placement.value = 'end'
    show.value = !show.value
  }

  const data = {
    title: 'TECNOLOGÍA DE LAS MAQUINAS HERRAMIENTA – 6ª Edición',
    cover: 'https://alfaomegaportal.test/wp-content/uploads/2024/07/1-3.png'
  }
</script>

<style>
  div.ao-sidebar .offcanvas-title {
    font-size: 1rem;
    font-weight: bold;
  }

</style>
