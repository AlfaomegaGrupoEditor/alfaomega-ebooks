<script setup lang="ts">
    import {ProcessStatusType, ProcessNameType, ProcessItem} from '@/types';
    import {computed, onMounted, ref} from 'vue';
    import AoProcessingActions from '@/components/aoProcessingActions.vue';
    import {useI18n} from 'vue-i18n';
    import {BiTrash3Fill, BiArrowRepeat} from '@/components/icons';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {useProcessStore} from '@/stores';

    const props = defineProps({
        action: {type: String as () => ProcessNameType , default: 'import'},
        status: {type: String as () => ProcessStatusType , default: 'idle'},
        completed: { type: Number, default: 0 },
        processing: { type: Number, default: 0 },
        pending: { type: Number, default: 0 },
        failed: { type: Number, default: 0 },
    });

    const {t} = useI18n();
    const processStore = useProcessStore();
    const modalName = 'action-process-modal';
    const {show} = useModal(modalName);
    const action = ref({ type: '', item: null });
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
    const activeTab = ref('failed'); // initialize with the first tab
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
    const formatDate = (date: string) => {
        return new Date(date).toLocaleString(
            'es-ES',
            {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
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
    const processData = computed(() => processStore.getActions);

    const currentPage = ref(1)
    const pageSize = ref(10)
    const rows = ref(100)
    const sortItems = computed(() => {
        switch (activeTab.value) {
            case 'processing':
                return [
                    {
                        id: 2345,
                        isbn: '9786076221600',
                        title: 'SOLIDWORKS PRACTICO I - Ensamblaje y Dibujo',
                        status: 'processing',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                    {
                        id: 2346,
                        isbn: '9786076221601',
                        title: 'CUANDO LAS PERSONAS SON EL CENTRO - Cómo abordar la gestión de RR.HH. sin medios',
                        status: 'pending',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                    {
                        id: 2347,
                        isbn: '9786076221602',
                        title: 'TERMOTECNIA',
                        status: 'pending',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                    {
                        id: 2348,
                        isbn: '9786076221603',
                        title: 'SISTEMAS SCADA - 3ª Edición',
                        status: 'pending',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                ]
            case 'completed':
                return [
                    {
                        id: 2345,
                        isbn: '9786076221600',
                        title: 'SISTEMAS DE INFORMACIÓN EN LA EMPRESA - El impacto de la nube, la movilidad y los medios sociales',
                        status: 'completed',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                ]
            case 'failed':
                return [
                    {
                        id: 2345,
                        isbn: '9786076221600',
                        title: 'SEGURIDAD E HIGIENE INDUSTRIAL - Gestión de riesgos',
                        status: 'failed',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                    {
                        id: 2346,
                        isbn: '9786076221601',
                        title: 'ROCK MARKETING - Una historia del rock diferente',
                        status: 'failed',
                        schedule_date: '2021-10-01 12:00:00',
                        last_attend_date: '2021-10-01 12:00:00',
                    },
                ]
        }
    });

    const navigateHandle = (page, event: Event) => {
        event.preventDefault();
        activeTab.value = page;
        retrieveProcessData();
    };

    const handleShowDialog = (actionType, item) => {
        action.value = { type: actionType, item: item };
        show();
    }

    const handleAction = () => {
        console.log('Action:', action.value);
    }

    const retrieveProcessData = () => {
        processStore.dispatchRetrieveProcessData(
            props.action,
            activeTab.value,
            currentPage.value,
            pageSize.value
        );
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
                <span>{{ $t('processing_queue_status') }} [ </span>
                <span :class="`text-${variant}`">{{ $t(status) }}</span>
                <span> ]</span>
            </div>
            <ao-processing-actions
                :action="'import'"
                :processing="processing"
                direction="row"
                @action="show"
            />
        </div>

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
                        <BBadge v-if="processing + pending > 0"
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
                        <BBadge v-if="completed > 0"
                                class="fs-7"
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
                        <BBadge v-if="failed > 0"
                                class="fs-7"
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
                    :items="sortItems"
                    :fields="sortFields"
                    :per-page="pageSize"
                    :current-page="currentPage"
                    :small="false"
                    :borderless="false"
                    :bordered="false"
                    :hover="true"
                    :selectable="false"
                    tbody-tr-class="ao-table-row"
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
                            size="sm"
                            class="mx-1"
                            :variant="row.item.status === 'processing' ? 'secondary' : 'primary'"
                            :disabled="row.item.status === 'processing'"
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
                    class="my-0 info-variant"
                    v-model="currentPage"
                    :total-rows="rows"
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
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleAction"
    >
        {{ $t(`${action.type}_process_confirmation`) }}
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
</style>
