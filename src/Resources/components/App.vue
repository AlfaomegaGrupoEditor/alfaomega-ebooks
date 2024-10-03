<script setup lang="ts">
    import {useAppStore} from '@/stores';
    import {computed, onMounted, ref} from 'vue';
    import {useI18n} from 'vue-i18n';
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
    import {getValue} from '@/services/Helper';

    const {t} = useI18n();
    const appStore = useAppStore();
    const isLoading = computed(() => appStore.isLoading);
    const header = ref<string>(t('welcome'));
    const searchQuery = ref<BooksQueryType>(null);
    const toast = ref<ToastType>();
    const toastActive = ref(false);
    const showSidebar = ref(false);

    const accessTypeValue = (pCategory: string | null = null) => {
        const urlParams = new URLSearchParams(window.location.search);
        const accessType = getValue(urlParams.get('accessType'));
        let category = getValue(urlParams.get('category'));

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
        notification: (event: NotificationEvent) => notificationHandler(event),
        apiSuccess: (event: ApiCheckEvent) => { /*console.log('API is responding!', event);*/ }
    });

    const init = () => {
        appStore.checkApi();

        const sessionMessage = JSON.parse(sessionStorage.getItem('alfaomega_ebooks_msg') || '{}');
        if (sessionMessage && sessionMessage.type && sessionMessage.message) {
            notificationHandler({
                type: sessionMessage.type,
                message: sessionMessage.message
            });
            console.error(sessionMessage);
            sessionStorage.removeItem('alfaomega_ebooks_msg');
        }

        const urlParams = new URLSearchParams(window.location.search);
        searchQuery.value = {
            filter: {
                category: getValue(urlParams.get('category')),
                accessType: accessTypeValue(),
                accessStatus: getValue(urlParams.get('accessStatus')),
                searchKey: getValue(urlParams.get('searchKey')),
                perPage: parseInt(getValue(urlParams.get('per_page'), 8)),
                currentPage: getValue(urlParams.get('currentPage'), 1),
                order: {
                    field: getValue(urlParams.get('order_by'), 'title'),
                    direction: getValue(urlParams.get('order_direction'), 'asc')
                } as OrderType
            } as BooksFilterType
        };
        console.log(searchQuery.value.filter.currentPage);
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
    };

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
            ...{
                filter: {
                    ...filter,
                    ...{category: searchQuery.value.filter.category}
                }
            }
        };
    };

    const handleSelected = (node) => {
        header.value = node.text;
        if (!searchQuery.value) {
            return;
        }

        searchQuery.value = {
            ...searchQuery.value,
            ...{
                filter: {
                    ...searchQuery.value.filter,
                    ...{
                        category: node.categories,
                        accessType: accessTypeValue(node.id),
                        currentPage: 1
                    }
                }
            }
        };
        eventBus.emit('catalogSelected', {catalog_id: node.id});
        showSidebar.value = false;
    };

    const handleApply = (payload: ToastType) => {
        // TODO: actually apply the code
        showToast(payload);
    };

    onMounted(() => {
        init();

        if (window.innerWidth < 768) {
            showSidebar.value = true;
        }
    });
</script>

<template>
    <b-container fluid class="ff-body ao-ebooks" style="min-height: 700px">
        <b-row>
            <b-col class="col-12 col-md-8 offset-md-2">
                <ao-alert />
            </b-col>
        </b-row>
        <b-row>
            <!-- Left panel-->
            <b-col class="col-12 col-md-3 d-none d-md-block">
                <ao-user-catalog @selected="handleSelected" />
                <ao-sample-input @apply="handleApply" />
            </b-col>

            <BOffcanvas
                v-model="showSidebar"
                class="ao-sidebar d-md-none"
                placement="start"
                :hide-backdrop="false"
                :header="false"
                :shadow="true"
                style="z-index: 9999999991"
            >
                <div>
                    <ao-user-catalog @selected="handleSelected" />
                    <ao-sample-input @apply="handleApply" />
                </div>
            </BOffcanvas>

            <!-- Main content-->
            <b-col class="col-12 col-md-9">
                <!--  Books selected-->
                <div class="d-flex align-items-center">
                    <b-button class="d-md-none" @click="showSidebar = true">
                        <i class="fas fa-bars"></i>
                    </b-button>
                    <h4 class="text-primary ms-2">{{ header }}</h4>
                </div>
                <ao-filter-bar @filter="handleFiltered" />
                <ao-library :query="searchQuery" />
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
