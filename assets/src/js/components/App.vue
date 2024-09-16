<template>
  <b-container fluid>
    <b-row>
      <b-col>
        <BAlert variant="success" :model-value="true">
          <h4 class="alert-heading">Well done!</h4>

          <p>
            Aww yeah, you successfully read this important alert message. This example text is going to
            run a bit longer so that you can see how spacing within an alert works with this kind of
            content.
          </p>

          <hr>

          <p class="mb-0">
            Whenever you need to, be sure to use margin utilities to keep things nice and tidy.
          </p>
        </BAlert>
      </b-col>
    </b-row>
    <b-row>
      <b-col>
        <b-button variant="primary" @click="showAlert">Click me</b-button>
      </b-col>
      <b-col cols="9">
        <h2>{{ $t('welcome') }}</h2>
      </b-col>
    </b-row>
  </b-container>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'App',
  setup() {
    const showAlert = () => {
      alert('Hello, World!');
    };

    return { showAlert };
  }
});
</script>

<style scoped>
h2 {
  color: #007bff;
}
</style>
