<script setup lang="ts">
  import {ref} from 'vue';
  import { aoButton, aoAccessDetails } from '@/components';

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
      <ao-button
          icon="fa-file-pdf"
          :caption="$t('download')"
          :disabled="false"
          @click="() => console.log('click download')"
      />

      <ao-button
          icon="fa-wifi"
          :caption="$t('read_online')"
          :disabled="false"
          @click="() => console.log('click read online')"
      />
    </div>

    <ao-access-details
        type="purchase"
        status="active"
        added_at="01/01/2024"
        valid_until="31/12/2024"
        book_url="https://alfaomegaportal.test/producto/tecnologia-de-las-maquinas-herramienta-6a-edicion/"
    />
  </BOffcanvas>
  <BButton @click="click" class="m-2">Show end</BButton>
</template>

<style scoped>

</style>
