<template>
  <b-container fluid>
    <b-row>
      <b-col>

        <!-- Alert test-->
        <BAlert variant="success" :model-value="true">
          <h4 class="alert-heading">Well done!</h4>
          <p>
            Aww yeah, you successfully read this important alert message. This example text is going to
            run a bit longer so that you can see how spacing within an alert works with this kind of
            content.
          </p>
        </BAlert>

        <!-- Store test-->
        <div>
          <span v-if="isLoading">'Loading'</span>
          <span v-else>'Not loading'</span>
        </div>
      </b-col>
    </b-row>
    <b-row>
      <!-- BoostrapVue component test-->
      <b-col>
        <b-button variant="primary" @click="showAlert">Click me</b-button>
      </b-col>

      <!-- Language test-->
      <b-col cols="9">
        <h2>{{ $t('welcome') }}</h2>
      </b-col>
    </b-row>
  </b-container>
</template>

<script setup lang="ts">
  import { useAppStore } from '@/stores/appStore';
  import { computed } from 'vue';

  const appStore = useAppStore();
  const isLoading = computed(() => appStore.isLoading);

  const showAlert = () => {
    alert('Hello, World!');
  };
</script>

<style scoped>
h2 {
  color: #007bff;
}
</style>
