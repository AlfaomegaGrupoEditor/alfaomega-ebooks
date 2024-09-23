<script setup lang="ts">
  import { ref, watch } from 'vue';
  import { aoButton, aoAccessDetails } from '@/components';
  import { BookType } from '@/types';

  const props = defineProps({
    show: Boolean,
    data: {type: Object as () => BookType, required: true}
  });

  const emit = defineEmits(['update:show']);
  const show = ref(props.show);

  const handleClose = () => {
    emit('update:show', !show.value);
  };

  watch(() => props.show, (newVal) => {
    show.value = newVal;
  });

  watch(() => props.data, (newVal) => {
    /*console.log(props.data);*/
  });
</script>

<template>
  <BOffcanvas
      v-if="data"
      v-model="show"
      class="ao-sidebar"
      placement="end"
      style="z-index: 200000"
      :hide-backdrop="false"
      :header="false"
      :shadow="true"
      @hide="handleClose"
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
</template>

<style scoped>

</style>
