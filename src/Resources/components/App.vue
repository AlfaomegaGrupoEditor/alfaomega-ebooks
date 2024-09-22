<script setup lang="ts">
  import { useAppStore } from '@/stores'
  import {computed, onMounted, ref, reactive} from 'vue';
  import { useI18n } from "vue-i18n";
  import {
    aoSampleInput,
    aoTreeview,
    aoAlert,
    aoFilterBar,
    aoBooks,
    aoBooksSkeleton
  } from '@/components';
  import {BooksQueryType, BooksFilterType, OrderType, ToastType} from '@/types';
  import AoToast from '@/components/aoToast.vue';
  import { eventBus, useMittEvents } from '@/events';
  import {ApiCheckEvent, NotificationEvent} from '@/events/types';

  const { t } = useI18n();
  const appStore = useAppStore();
  const isLoading = computed(() => appStore.isLoading);
  const header = ref<string>(t('welcome'));
  const searchQuery = ref<BooksQueryType>(null);
  const toast = ref<ToastType>();
  const toastActive = ref(false);

  /**
   * Registers the event the App will be listing to.
   */
  useMittEvents(eventBus, {
    notification : (event: NotificationEvent) => notificationHandler(event),
    apiSuccess: (event: ApiCheckEvent) => { console.log('API is responding!', event); }
  });

  // TODO: if the API check is successful, load the user ebooks
  //  the default search query will be these merged with the browser query
  //  each time one of the query parameters changes the browser history will be updated
  //  The next step is to implement the libraryStore with the action searchBooks(searchQuery)
  //  then Books will be loaded from the store with a getter

  const init = () => {
    appStore.checkApi();

    // todo: load the user ebooks
    searchQuery.value = {
      'category': null,
      'filter': {
        'accessType': null,
        'accessStatus': null,
        'search': null
      } as BooksFilterType,
      'page': 0,
      'pageSize': 12,
      'userId': null,
      'order': {
        'field': 'title',
        'direction': 'asc'
      } as OrderType
    };
  };

  /**
   * When a notification is sent
   */
  const notificationHandler = (data: NotificationEvent) => {
    if (data.type === 'success') {
      showToast({
        variant: 'success',
        title: t('success'),
        content: t(data.message)
      });
    } else if (data.type === 'error') {
      showToast({
        variant: 'primary',
        title: t('attention'),
        content: t(data.message)
      });
    }
    console.log(data);
  }



  const test = () => { appStore.testLoading() };

  const handleSelected = (node) => {
    searchQuery.value = {...searchQuery.value, ...{category: node.id}};
    header.value = node.text;
    console.log(searchQuery.value);
  };

  const handleFiltered = (filter) => {
    searchQuery.value = {...searchQuery.value, ...{filter: filter}};
    console.log(searchQuery.value);
  };

  const showToast = (newToast: ToastType) => {
    toast.value = newToast;

    toastActive.value = true;
    setTimeout(() => {
      toastActive.value = false;
    }, 5000);
  };

  const handleApply = (payload: ToastType) => {
    // actually apply the code
    showToast(payload);
  };

  onMounted(init);
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
        <ao-sample-input @apply="handleApply"/>
      </b-col>

      <!-- Main content-->
      <b-col cols="9">
        <!--  Books selected-->
        <h4 class="text-primary">{{ header }}</h4>
        <ao-filter-bar @filter="handleFiltered"/>
        <!--  load the books on suspense -->
        <Suspense>
          <ao-books :query="searchQuery"/>
          <template #fallback>
            <ao-books-skeleton />
          </template>
        </Suspense>
      </b-col>
    </b-row>
  </b-container>

  <!--  Toast message-->
  <ao-toast
      :active="toastActive"
      :variant="toast?.variant || 'success'"
      :title="toast?.title || ''"
      :content="toast?.content || 'test'"
  />
</template>

<style scoped>
</style>
