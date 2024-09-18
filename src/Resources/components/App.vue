<template>
  <b-container fluid>
    <b-row>
      <!-- Left panel-->
      <b-col>
        <sample-code-box />

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
  </b-container>
</template>

<script setup lang="ts">
  import { useAppStore } from '@/stores/appStore';
  import { computed } from 'vue';
  import SampleCodeBox from '@/components/SampleCodeBox.vue';

  const appStore = useAppStore();
  const isLoading = computed(() => appStore.isLoading);

  const test = () => { appStore.testLoading() };

  const showAlert = () => {
    alert('Hello, World!');
  };
</script>

<style scoped>
h2 {
  color: #007bff;
}
</style>
