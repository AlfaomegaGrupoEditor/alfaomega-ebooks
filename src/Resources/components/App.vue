<template>
  <b-container fluid class="ff-body">
    <b-row>
      <!-- Left panel-->
      <b-col>
        <ao-sample-input />

      </b-col>

      <!-- Main content-->
      <b-col cols="9">
        <!-- Alert test-->
        <BAlert variant="success" :model-value="true" dismissible>
          <h4 class="alert-heading">Well done!</h4>
          <p>
            Aww yeah, you successfully read this important alert message. This example text is going to
            run a bit longer so that you can see how spacing within an alert works with this kind of
            content.
          </p>
        </BAlert>

        <h2>{{ $t('welcome') }}</h2>
      </b-col>
    </b-row>

    <div>
      <BButton @click="click" class="m-2">Show end</BButton>
      <BOffcanvas v-model="show" :placement="placement" />
    </div>
  </b-container>
</template>

<script setup lang="ts">
  import { useAppStore } from '@/stores/appStore';
  import { computed, ref } from 'vue';
  import { aoSampleInput } from '@/components';

  const appStore = useAppStore();
  const isLoading = computed(() => appStore.isLoading);

  const show = ref(false)
  const placement = ref('start')

  const test = () => { appStore.testLoading() };

  const showAlert = () => {
    alert('Hello, World!');
  };

  const click = (place = 'start') => {
    placement.value = 'end'
    show.value = !show.value
  }
</script>

<style scoped>
h2 {
  color: #007bff;
}
</style>
