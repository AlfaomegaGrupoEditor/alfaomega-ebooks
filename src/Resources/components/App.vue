<script setup lang="ts">
  import { useAppStore } from '@/stores/appStore';
  import {computed, onMounted, ref} from 'vue';
  import { useI18n } from "vue-i18n";
  import {
    aoSampleInput,
    aoSidebar,
    aoTreeview,
    aoAlert,
    aoFilterBar,
    aoBooks
  } from '@/components';
  import {EbooksQuery, EbooksFilter, Order} from '@/types';

  const { t } = useI18n();
  const appStore = useAppStore();
  const isLoading = computed(() => appStore.isLoading);
  const header = ref<string>(t('welcome'));
  const ebooksQuery = ref<EbooksQuery>(null);

  const test = () => { appStore.testLoading() };

  const handleSelected = (node) => {
    ebooksQuery.value = {...ebooksQuery.value, ...{category: node.id}};
    header.value = node.text;
    console.log(ebooksQuery.value);
  };

  const handleFiltered = (filter) => {
    ebooksQuery.value = {...ebooksQuery.value, ...{filter: filter}};
    console.log(ebooksQuery.value);
  };

  onMounted(() => {
    ebooksQuery.value = {
      'category': null,
      'filter': {
        'accessType': null,
        'accessStatus': null,
        'search': null
      } as EbooksFilter,
      'page': 0,
      'pageSize': 12,
      'userId': null,
      'order': {
        'field': 'title',
        'direction': 'asc'
      } as Order
    };
  });
</script>

<template>
  <b-container fluid class="ff-body ao-ebooks" style="min-height: 700px">
    <b-row>
      <b-col class="col-8 offset-2">
        <ao-alert />
      </b-col>
    </b-row>
    <b-row>
      <!-- Left panel-->
      <b-col>
        <ao-treeview @selected="handleSelected"/>
        <ao-sample-input />
      </b-col>

      <!-- Main content-->
      <b-col cols="9">
        <!--  Books selected-->
        <h4 class="text-primary">{{ header }}</h4>
        <ao-filter-bar @filter="handleFiltered"/>
        <ao-books />
      </b-col>
    </b-row>

    <div>
      <ao-sidebar />
    </div>
  </b-container>
</template>

<style scoped>
</style>
