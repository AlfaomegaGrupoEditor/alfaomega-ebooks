<script setup lang="ts">
    import {AsyncProcessStatusType, ProcessNameType} from '@/types';
    import {computed, ref} from 'vue';
    import AoProcessingActions from '@/components/aoProcessingActions.vue';

    const props = defineProps({
        action: {type: String as () => ProcessNameType , default: 'import'},
        status: {type: String as () => AsyncProcessStatusType , default: 'idle'},
        completed: { type: Number, default: 0 },
        processing: { type: Number, default: 0 },
        pending: { type: Number, default: 0 },
        failed: { type: Number, default: 0 },
    });

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
    const activeTab = ref('failed');
    const navigateHandle = (page, event: Event) => {
        event.preventDefault();
        activeTab.value = page;
    };

    interface SortPerson {
        first_name: string
        last_name: string
        age: number
        isActive: boolean
    }

    const sortItems: TableItem<Person>[] = [
        {isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald'},
        {isActive: true, age: 45, first_name: 'Zelda', last_name: 'Macdonald'},
        {isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw'},
        {isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson'},
        {isActive: false, age: 89, first_name: 'Gary', last_name: 'Wilson'},
        {isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney'},
    ]

    const sortFields: Exclude<TableFieldRaw<SortPerson>, string>[] = [
        {key: 'last_name', sortable: true},
        {key: 'first_name', sortable: true},
        {key: 'age', sortable: true},
        {key: 'isActive', sortable: false},
    ]

    const currentPage = ref(1)
    const rows = ref(100)
    const pageSize = ref(10)
    const pageOptions = [
        {value: 10, text: 10},
        {value: 25, text: 25},
        {value: 60, text: 60},
    ]

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
            <BTable
                :sort-by="[{key: 'first_name', order: 'desc'}]"
                :items="sortItems"
                :fields="sortFields"
            />
        </BCard>

        <div class="row mt-3">
            <div class="col">
                <BFormSelect
                    class="float-end"
                    v-model="pageSize"
                    :options="pageOptions"
                    size="sm"
                    style="max-width: 100px;"
                />
            </div>
            <div class="col-3">
                <BPagination
                    class="info-variant"
                    v-model="currentPage"
                    :total-rows="rows"
                    hide-goto-end-buttons
                    align="end"
                />
            </div>
        </div>
    </BCard>
</template>

<style>
    .border-light{
        border-color: var(--bs-light-border-subtle) !important;
    }
    .status-tab .card-header {
        padding-bottom: 1px;
        border-color: var(--bs-light-border-subtle) !important;
    }
    .info-variant {
        margin-bottom: 0;
    }
    .info-variant .page-link {
        color: #2171b1;
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
</style>
