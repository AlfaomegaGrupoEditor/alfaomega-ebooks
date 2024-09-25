<script setup lang="ts">
  import { useAppStore } from '@/stores'
  import {computed, onMounted, ref} from 'vue';
  import { useI18n } from "vue-i18n";
  import {
    aoSampleInput,
    aoUserCatalog,
    aoAlert,
    aoFilterBar,
    aoLibrary,
    aoBooksSkeleton
  } from '@/components';
  import {BooksQueryType, BooksFilterType, OrderType, ToastType} from '@/types';
  import AoToast from '@/components/aoToast.vue';
  import {eventBus, useMittEvents} from '@/events';
  import {ApiCheckEvent, NotificationEvent} from '@/events/types';

  const { t } = useI18n();
  const appStore = useAppStore();
  const isLoading = computed(() => appStore.isLoading);
  const header = ref<string>(t('welcome'));
  const searchQuery = ref<BooksQueryType>(null);
  const toast = ref<ToastType>();
  const toastActive = ref(false);

  const accessTypeValue = (pCategory: string|null = null) => {
    const urlParams = new URLSearchParams(window.location.search);
    const accessType = urlParams.get('accessType') || null;
    let category = urlParams.get('category') || null;

    if (pCategory !== null) {
      category = pCategory;
    }

    switch (category) {
      case 'purchased':
        return 'purchase';
      case 'samples':
        return 'sample';
    }

    return accessType;
  };

  /**
   * Registers the event the App will be listing to.
   */
  useMittEvents(eventBus, {
    notification : (event: NotificationEvent) => notificationHandler(event),
    apiSuccess: (event: ApiCheckEvent) => { /*console.log('API is responding!', event);*/ }
  });

  const init = () => {
    appStore.checkApi();

    const urlParams = new URLSearchParams(window.location.search);
    searchQuery.value = {
      category: null,
      filter: {
        category: urlParams.get('category') || null,
        accessType: accessTypeValue(),
        accessStatus: urlParams.get('accessStatus') || null,
        searchKey: urlParams.get('searchKey')  || null,
        perPage: parseInt(urlParams.get('per_page')) || 8,
        order: {
          field: urlParams.get('order_by') || 'title',
          direction: urlParams.get('order_direction') || 'asc'
        } as OrderType,
      } as BooksFilterType,
      page: parseInt(urlParams.get('page')) || 1,
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
  }

  /**
   * Show a toast message
   * @param newToast
   */
  const showToast = (newToast: ToastType) => {
    toast.value = newToast;

    toastActive.value = true;
    setTimeout(() => {
      toastActive.value = false;
    }, 5000);
  };

  /**
   * Handle the filters update
   * @param filter
   */
  const handleFiltered = (filter) => {
    searchQuery.value = {
      ...searchQuery.value,
      ...{filter: filter}
    };
  };

  const handleSelected = (node) => {
    if (!searchQuery.value) {
      return;
    }

    searchQuery.value = {
      ...searchQuery.value,
      ...{
        ...searchQuery.value.filter,
        ...{
          category: node.id,
          accessType: accessTypeValue(node.id)
        }
      }
    };
    header.value = node.text;
  };

  const test = () => { appStore.testLoading() };


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
        <ao-user-catalog @selected="handleSelected"/>
        <ao-sample-input @apply="handleApply"/>
      </b-col>

      <!-- Main content-->
      <b-col cols="9">
        <!--  Books selected-->
        <h4 class="text-primary">{{ header }}</h4>
        <ao-filter-bar @filter="handleFiltered"/>
        <!--  load the library books on suspense -->
        <Suspense>
          <ao-library :query="searchQuery"/>
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
