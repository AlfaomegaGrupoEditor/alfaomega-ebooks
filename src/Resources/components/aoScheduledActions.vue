<script setup lang="ts">
    import {
        ProcessStatusType,
        ProcessNameType,
        ProcessItem,
        ProcessDataType,
        QueueType
    } from '@/types';
    import {computed, onMounted, ref} from 'vue';
    import {aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';
    import {BiTrash3Fill, BiArrowRepeat, BiEye} from '@/components/icons';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {useProcessStore, useAppStore } from '@/stores';
    import {RefreshActionsEvent} from '@/events/types';
    import {eventBus, useMittEvents} from '@/events';
    import {formatDate} from '@/services/Helper';

    const props = defineProps({
        action: {type: String as () => ProcessNameType , default: 'import'},
        queue: {type: String as () => QueueType},
        status: {type: String as () => ProcessStatusType , default: 'idle'},
        completed: { type: Number, default: 0 },
        processing: { type: Number, default: 0 },
        pending: { type: Number, default: 0 },
        failed: { type: Number, default: 0 },
    });

    const emit = defineEmits(['action']);
    useMittEvents(eventBus, {
        refreshActions: (event: RefreshActionsEvent) => handleRefreshQueue(),
    });

    const {t} = useI18n();
    const processStore = useProcessStore();
    const appStore = useAppStore();

    const confirmModalName = 'action-process-confirm-modal';
    const dlgModalName = 'action-process-dlg-modal';
    const dlgModalTitle = 'action_details';
    const {show: showConfirm} = useModal(confirmModalName);
    const {show: showDlg} = useModal(dlgModalName);

    const selectedAction = ref({ type: '', item: null });
    const variant = computed(() => {
        switch (props.status) {
            case 'idle':
                return 'warning';
            case 'processing':
                return 'info';
            case 'completed':
                return 'success';
            case 'failed':
                return 'primary';
        }
    });
    const activeTab = ref('processing'); // initialize with the first tab

    const statusVariant = (status: ProcessStatusType) => {
        switch (status) {
            case 'processing':
                return 'info';
            case 'completed':
                return 'success';
            case 'failed':
                return 'primary';
            default:
                return 'warning';
        }
    }
    const sortFields: Exclude<TableFieldRaw<ProcessItem>, string>[] = [
        {key: 'id', sortable: true, label: 'ID'},
        {key: 'isbn', sortable: true, label: 'ISBN'},
        {key: 'title', sortable: true, label: t('title').toUpperCase()},
        {key: 'status', sortable: true, label: t('status').toUpperCase()},
        {
            key: 'schedule_date',
            sortable: true,
            label: t('scheduled').toUpperCase(),
            formatter: (value) => (value ? formatDate(value) : ''),
        },
        {
            key: 'last_attend_date',
            sortable: true,
            label: t('last_attend').toUpperCase(),
            formatter: (value) => (value ? formatDate(value) : ''),
        },
        {key: 'actions', label: '', sortable: false},
    ]
    const pageOptions = [
        {value: 10, text: 10},
        {value: 25, text: 25},
        {value: 60, text: 60},
    ]
    const processData = computed(() => processStore.getProcessData);
    const processBusy = computed(() => appStore.getLoading);
    const actionTitle = computed(() => {
        switch (props.action) {
            case 'import':
                return 'import_ebooks';
            case 'update':
                return 'update_ebooks';
            case 'link':
                return 'link_products';
            case 'setup':
                return 'setup_prices';
        }
    });
    const actionNotice = computed(() => {
        switch (props.action) {
            case 'import':
                return 'import_ebooks_notice';
            case 'update':
                return 'update_ebooks_notice';
            case 'link':
                return 'link_products_notice';
            case 'setup':
                return 'setup_prices_notice';
        }
    });

    const currentPage = ref(1)
    const pageSize = ref(10)

    const navigateHandle = (page, event: Event) => {
        event.preventDefault();
        activeTab.value = page;
        retrieveProcessData();
    };

    const handleShowDialog = (actionType: String, item: ProcessDataType) => {
        selectedAction.value = { type: actionType, item: item };
        actionType === 'view' ? showDlg() : showConfirm();
    }

    const handleRowClick = (item) => {
        selectedAction.value = { type: 'view', item: item };
        showDlg();
    }

    const handleAction = () => {
        switch (selectedAction.value.type) {
            case 'retry':
                processStore.dispatchRetryAction(props.queue, [selectedAction.value.item.id]);
                break;
            case 'delete':
                processStore.dispatchDeleteAction(props.queue, [selectedAction.value.item.id]);
                break;
            default: // primary
                emit('action', props.action);
        }
    }

    const retrieveProcessData = () => {
        processStore.dispatchRetrieveProcessData(
            props.action,
            activeTab.value,
            currentPage.value,
            pageSize.value
        );
    }

    const handleRefreshQueue = () => {
        processStore.dispatchRetrieveQueueStatus(props.queue);
        retrieveProcessData();
    }

    const handleClearQueue = () => {
        processStore.dispatchClearQueue(props.queue);
    }

    onMounted(() => {
        retrieveProcessData();
    });
</script>

<template>
    <BCard no-body class="w-100 border-light"
           style="max-width: 100%; margin-right: 15px;"
    >
        <div class="card-title fw-bold fs-6 px-0 pt-0 pb-2 mb-1 text-muted text-uppercase d-flex justify-content-between align-items-center">
            <div>
                <span>{{ $t(actionTitle) }} [ </span>
                <span :class="`text-${variant}`">{{ $t(status) }}</span>
                <span> ]</span>
            </div>
            <ao-processing-actions
                :action="action"
                :status="activeTab"
                :processing="processing"
                direction="row"
                @action="emit('action')"
                @refresh="handleRefreshQueue"
                @clear="handleClearQueue"
            />
        </div>

        <div class="mb-2" v-html="$t(actionNotice)"></div>

        <BCard
            class="mt-0 status-tab"
            header-tag="nav"
            style="max-width: 100%; margin: 0; padding: 0;"
        >
            <template #header>
                <BNav card-header tabs>
                    <BNavItem
                        :class="activeTab === 'processing' ? 'fw-bold' : ''"
                        :variant="activeTab === 'processing' ? 'info' : 'dark'"
                        :active="activeTab === 'processing'"
                        @click="navigateHandle('processing', $event)"
                    >
                        {{ $t('processing')}}
                        <BBadge
                            class="fs-7"
                            variant="info"
                        >
                            {{ processing + pending }}
                        </BBadge>
                    </BNavItem>
                    <BNavItem
                        :class="activeTab === 'completed' ? 'fw-bold' : ''"
                        :variant="activeTab === 'completed' ? 'info' : 'dark'"
                        :active="activeTab === 'completed'"
                        @click="navigateHandle('completed', $event)"
                        variant="info"
                    >
                        {{ $t('completed')}}
                        <BBadge class="fs-7"
                                variant="success"
                        >
                            {{ completed }}
                        </BBadge>
                    </BNavItem>
                    <BNavItem
                        :class="activeTab === 'failed' ? 'fw-bold' : ''"
                        :variant="activeTab === 'failed' ? 'info' : 'dark'"
                        :active="activeTab === 'failed'"
                        @click="navigateHandle('failed', $event)"
                    >
                        {{ $t('failed')}}
                        <BBadge class="fs-7"
                                variant="primary"
                        >
                            {{ failed }}
                        </BBadge>
                    </BNavItem>
                </BNav>
            </template>
            <div style="min-height: 300px">
                <BTable
                    :sort-by="[{key: 'first_name', order: 'desc'}]"
                    :busy="processBusy"
                    :busy-loading-text="$t('loading')"
                    :show-empty="true"
                    :empty-text="$t('no_data')"
                    :items="processData.actions"
                    :fields="sortFields"
                    :per-page="pageSize"
                    :current-page="currentPage"
                    :small="false"
                    :borderless="false"
                    :bordered="false"
                    :hover="true"
                    :selectable="false"
                    tbody-tr-class="ao-table-row"
                    @row-clicked="handleRowClick"
                >
                    <template #cell(isbn)="row">
                        <BBadge variant="info">{{ row.value }}</BBadge>
                    </template>
                    <template #cell(title)="row">
                        {{ row.value.length > 60 ? row.value.substring(0, 60) + '...' : row.value }}
                    </template>
                    <template #cell(status)="row">
                        <BBadge :variant="statusVariant(row.value)">{{ $t(row.value) }}</BBadge>
                    </template>
                    <template #cell(actions)="row">
                        <BButton
                            size="sm"
                            class="mx-1"
                            variant="secondary"
                            :title="t('view')"
                            @click="handleShowDialog('view', row.item)"
                        >
                            <span v-html="BiEye" />
                        </BButton>
                        <BButton
                            v-if="activeTab === 'failed'"
                            size="sm"
                            class="mx-1"
                            variant="secondary"
                            :title="t('retry')"
                            @click="handleShowDialog('retry', row.item)"
                        >
                            <span v-html="BiArrowRepeat" />
                        </BButton>
                        <BButton
                            v-if="row.item.status !== 'processing'"
                            size="sm"
                            class="mx-1"
                            :variant="row.item.status === 'processing' ? 'secondary' : 'primary'"
                            :title="t('delete')"
                            @click="handleShowDialog('delete', row.item)"
                        >
                            <span v-html="BiTrash3Fill" />
                        </BButton>
                    </template>
                </BTable>
            </div>
        </BCard>

        <div class="row mt-3">
            <div class="col">
                <BPagination
                    v-if="processData.meta.total > pageSize"
                    class="my-0 info-variant"
                    v-model="currentPage"
                    :total-rows="processData.meta.total"
                    :per-page="pageSize"
                    hide-goto-end-buttons
                    align="end"
                    @pageClick="retrieveProcessData"
                />
            </div>
            <div class="col-1">
                <BFormSelect
                    class="float-end fs-8"
                    v-model="pageSize"
                    :options="pageOptions"
                    size="sm"
                    style="max-width: 100px;"
                    @change="retrieveProcessData"
                />
            </div>
        </div>
    </BCard>
    <ao-dialog
        :name="confirmModalName"
        :title="$t('confirmation')"
        @action="handleAction"
    >
        <span v-if="selectedAction.type==='primary'">
            {{ $t('import_ebooks_confirmation') }}
        </span>
        <span v-else>
            {{ $t(`${selectedAction.type}_process_confirmation`) }}
        </span>
    </ao-dialog>
    <ao-dialog
        :name="dlgModalName"
        type="dlg"
        :title="$t(dlgModalTitle) + ' [' + $t(actionTitle) + ']'"
        size="lg"
    >
        <BTabs class="info-variant">
            <BTab :title="$t('details')" active class="mt-2">
                <div class="row py-2 px-3"
                     v-if="selectedAction.item && selectedAction.item.data"
                     v-for="(value, key) in selectedAction.item.data"
                     :key="key"
                >
                    <div class="col-2 fw-bold fs-7 text-end text-uppercase">{{ key }}:</div>
                    <div class="col border px-2 py-2 bg-info-subtle">{{ value }}</div>
                </div>
            </BTab>
            <BTab :title="$t('logs')" class="mt-2">
                <div class="row py-2 px-3"
                     v-if="selectedAction.item && selectedAction.item.logs"
                     v-for="log in selectedAction.item.logs"
                     :key="log.id"
                >
                    <div class="col-3 text-end text-uppercase">
                        <BBadge variant="info">{{ formatDate(log.date) }}</BBadge>
                    </div>
                    <div class="col px-2">{{ log.message }}</div>
                </div>
            </BTab>
        </BTabs>
    </ao-dialog>
</template>

<style>
    .border-light{
        border-color: var(--bs-light-border-subtle) !important;
    }
    .status-tab .card-header {
        padding-bottom: 1px;
        border-color: var(--bs-light-border-subtle) !important;
    }

    .info-variant .page-link {
        color: #2171b1;
        font-size: .8rem;
    }

    .info-variant .page-link:hover {
        border-color: #2171b1;
        background-color: white;
        color: #2171b1;
    }

    .info-variant .page-item.active .page-link {
        background-color: #2171b1;
        border-color: #2171b1;
        color: white;
    }
    .ao-table-row td{
        vertical-align: middle;
    }
    li.nav-item a:focus {
        box-shadow: none;
    }
    .b-table-empty-slot {
        text-align: center;
    }
    .info-variant .nav-item {
        margin-bottom: -1px;
    }
    .info-variant .nav-link {
        color: #2171b1;
    }
    .info-variant .nav-link:hover {
        color: #2171b1;
        border-color: #2171b1;
    }
    .info-variant .tab-content {
        min-height: 380px;
    }
</style>
