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
    import {
        BooksQueryType,
        BooksFilterType,
        OrderType,
        ToastType,
        TreeNodeType,
        CategorySelectedType
    } from '@/types';
    import AoToast from '@/components/aoToast.vue';
    import {eventBus, useMittEvents} from '@/events';
    import {ApiCheckEvent, NotificationEvent} from '@/events/types';
    import {getValue} from '@/services/Helper';
    import { useToast } from '@/composables/useToast';

    const {t} = useI18n();
    const appStore = useAppStore();
    const isLoading = computed(() => appStore.getLoading);
    const header = ref<string>(t('welcome'));
    const searchQuery = ref<BooksQueryType|null>(null);
    const { toast, toastActive, showToast } = useToast();
    const showSidebar = ref(false);
    const hideMigrationNotice = ref(localStorage.getItem('hideMigrationNotice') === 'true');
    const migrationNotice = computed(() => t('migration_notice')
        .replace(':website', `<a class="text-primary" href="${window.wpApiSettings.oldStore}" target="_blank">${window.wpApiSettings.oldStore}</a>`));
    const showMigrationNotice = computed(() => window.wpApiSettings.migration && !hideMigrationNotice.value);

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
     * Handle the filters update
     * @param filter
     */
    const handleFiltered = (filter: BooksFilterType) => {
        searchQuery.value = {
            ...searchQuery.value,
            ...{
                filter: {
                    ...filter,
                    ...{category: searchQuery.value?.filter?.category}
                }
            }
        };
    };

    const handleSelected = (node: CategorySelectedType) => {
        header.value = node.text || '';
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
        showToast(payload);
    };

    /**
     * Hides the migration notice
     */
    const handleHideMigrationAlert = () => {
        localStorage.setItem('hideMigrationNotice', 'true');
        hideMigrationNotice.value = true;
    };

    onMounted(() => {
        init();

        if (window.innerWidth < 768) {
            showSidebar.value = true;
        }
    });
</script>

<template>
    <teleport to="body">
        <div id="ao-container" class="bootstrap-app"></div>
    </teleport>
    <b-container fluid class="ff-body ao-ebooks" style="min-height: 700px">
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
                teleport-to="#ao-container"
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
                    <h4 class="text-primary ms-2 fs-6">{{ header }}</h4>
                </div>
                <ao-alert v-if="showMigrationNotice"
                    :caption="$t('important_info')"
                    :action="$t('dont_show_again')"
                    @action="handleHideMigrationAlert"
                >
                    {{ migrationNotice }}
                </ao-alert>
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
    :root {
        --ribbon-size: 100px;
    }
</style>
