<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {ref, defineEmits, onMounted, computed, watch} from 'vue';
    import {BooksFilterType, OrderType} from '@/types';
    import {useLibraryStore} from '@/stores';
    import debounce from 'lodash/debounce';
    import {updateHistory, getValue} from '@/services/Helper';
    import {eventBus, useMittEvents} from '@/events';
    import {CatalogSelectedEvent} from '@/events/types';

    const emit = defineEmits<{ filter: (payload: BooksFilterType) => void }>();
    const {t} = useI18n();
    useMittEvents(eventBus, {
        catalogSelected: (event: CatalogSelectedEvent) => catalogSelectedHandler(event)
    });

    const libraryStore = useLibraryStore();
    const meta = computed(() => libraryStore.getMeta);

    const accessType = ref(null);
    const disableAccessType = ref(false);
    const accessTypeOptions = [
        {value: null, text: t('access_type')},
        {value: 'purchase', text: t('purchased')},
        {value: 'sample', text: t('sample')}
    ];
    const accessStatus = ref(null);
    const accessStatusOptions = [
        {value: null, text: t('status')},
        {value: 'created', text: t('created')},
        {value: 'active', text: t('active')},
        {value: 'expired', text: t('expired')},
        {value: 'cancelled', text: t('cancelled')}
    ];
    const searchKey = ref(null);
    const order = ref<OrderType>({
        field: 'title',
        direction: 'asc'
    });
    const orderByOptions = [
        {value: 'title', text: t('title')},
        {value: 'created_at', text: t('created_at')},
        {value: 'status', text: t('status')},
        {value: 'valid_until', text: t('valid_until')},
        {value: 'access_at', text: t('access_at')}
    ];
    const perPage = ref(8);
    const perPageOptions = [
        {value: 8, text: '8'},    // 4x2
        {value: 16, text: '16'},  // 4x4
        {value: 24, text: '24'},  // 4x6
        {value: 32, text: '32'},  // 4x8
        {value: 40, text: '40'}  // 4x10
    ];
    const defaultFilters = computed(() => {
        return accessType.value === null &&
               accessStatus.value === null &&
               searchKey.value === null &&
               order.value.field === 'title' &&
               order.value.direction === 'asc' &&
               perPage.value === 8;
    });

    const toggleOrderDirection = () => {
        order.value.direction = order.value.direction === 'asc' ? 'desc' : 'asc';
        handleFilter();
    };

    const handleFilter = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const filterValue = {
            category: urlParams.get('category'),
            accessType: accessType.value,
            accessStatus: accessStatus.value,
            searchKey: searchKey.value,
            order: order.value,
            perPage: perPage.value
        };

        updateHistory(filterValue);

        emit('filter', filterValue);
    };

    const handleResetFilters = () => {
        accessType.value = null;
        accessStatus.value = null;
        searchKey.value = null;
        order.value.field = 'title';
        order.value.direction = 'asc';
        perPage.value = 8;

        const urlParams = new URLSearchParams(window.location.search);
        const category = getValue(urlParams.get('category'), 'all_ebooks');
        checkAccessType(category);
        handleFilter();
    };

    const catalogSelectedHandler = (data: string) => {
        checkAccessType(data);
    };

    const checkAccessType = (category) => {
        if (category === 'all_ebooks') {
            accessType.value = null;
            disableAccessType.value = false;
        } else if (category === 'purchased') {
            accessType.value = 'purchase';
            disableAccessType.value = true;
        } else if (category === 'samples') {
            accessType.value = 'sample';
            disableAccessType.value = true;
        }
    };

    const debouncedHandleFilter = debounce(handleFilter, 500);

    watch(searchKey, debouncedHandleFilter);

    onMounted(() => {
        const urlParams = new URLSearchParams(window.location.search);
        accessType.value = getValue(urlParams.get('accessType'));
        accessStatus.value = getValue(urlParams.get('accessStatus'));
        searchKey.value = getValue(urlParams.get('searchKey'));
        order.value.field = getValue(urlParams.get('order_by'), 'title');
        order.value.direction = getValue(urlParams.get('order_direction'), 'asc');
        perPage.value = parseInt(getValue(urlParams.get('per_page'), 8));
    });
</script>

<template>
    <div class="row">
        <div class="col">
            <div class="row">
                <div class="col-6 d-flex justify-content-start align-items-center">
                    <BFormLabel
                        for="access-type-select"
                        class="form-label-sm fs-8 mr-2"
                    >
                        {{ $t('filter_by') }}:
                    </BFormLabel>
                    <!--      type-->
                    <BFormSelect
                        class="mx-2"
                        is="access-type-select"
                        v-model="accessType"
                        :options="accessTypeOptions"
                        :disabled="disableAccessType"
                        size="sm"
                        style="max-width: 150px;"
                        @change="handleFilter"
                    />
                    <BFormSelect
                        is="access-status-select"
                        v-model="accessStatus"
                        :options="accessStatusOptions"
                        size="sm"
                        style="max-width: 100px;"
                        @change="handleFilter"
                    />
                </div>
                <!-- search -->
                <div class="col-6">
                    <BInputGroup size="sm">
                        <BFormInput
                            class="form-control-sm"
                            id="search-input"
                            :placeholder="$t('search')"
                            type="text"
                            v-model="searchKey"
                        />
                        <BInputGroupText>
                            <i class="fa fa-search"></i>
                        </BInputGroupText>
                    </BInputGroup>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <!--  Books founds -->
        <div class="col-3">
            <BFormLabel
                for="total-books-label"
                class="form-label-sm"
            >
                <span class="fs-8 fw-bold">{{ $t('books_found') }}:</span>
            </BFormLabel>
            <BBadge>{{ meta.total }}</BBadge>
        </div>
        <!--  order by -->
        <div class="col-9 d-flex justify-content-end align-items-center">
            <BButton
                variant="link"
                class="fs-8"
                underline-opacity="0"
                underline-opacity-hover="100"
                underline-offset="3"
                @click="handleResetFilters"
                :disabled="defaultFilters"
            >
                {{ $t('reset_filters') }}
            </BButton>
            <!--  order by -->
            <BFormLabel
                for="per-page-select"
                class="form-label-sm fs-8 mx-2"
            >
                {{ $t('per_page') }}:
            </BFormLabel>
            <BFormSelect
                is="per-page-select"
                v-model="perPage"
                :options="perPageOptions"
                size="sm"
                style="max-width: 70px; margin-right: 10px"
                @change="handleFilter"
            />
            <BFormLabel
                for="order-by-select"
                class="form-label-sm fs-8 mx-2"
            >
                {{ $t('order_by') }}:
            </BFormLabel>
            <BFormSelect
                is="order-by-select"
                v-model="order.field"
                :options="orderByOptions"
                size="sm"
                style="max-width: 120px;"
                @change="handleFilter"
            />
            <BButton
                variant="primary"
                size="sm"
                class="ms-2"
                @click="toggleOrderDirection"
            >
                <i :class="order.direction === 'asc' ? 'fas fa-sort-amount-down-alt' : 'fa fa-sort-amount-down'"></i>
            </BButton>
        </div>
    </div>
</template>

<style scoped>

</style>
