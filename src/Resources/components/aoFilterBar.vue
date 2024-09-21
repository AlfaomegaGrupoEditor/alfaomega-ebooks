<script setup lang="ts">
  import { useI18n } from "vue-i18n";
  import { ref, defineEmits } from 'vue';
  import {EbooksFilterType, OrderType} from '@/types';

  const emit = defineEmits<{ filter: (payload: EbooksFilterType) => void }>();
  const { t } = useI18n();

  const accessType = ref(null)
  const accessTypeOptions = [
    {value: null, text: t('access_type')},
    {value: 'purchases', text: t('purchased')},
    {value: 'samples', text: t('samples')},
  ]

  const accessStatus = ref(null)
  const accessStatusOptions = [
    {value: null, text: t('status')},
    {value: 'created', text: t('created')},
    {value: 'active', text: t('active')},
    {value: 'expired', text: t('expired')},
    {value: 'cancelled', text: t('cancelled')},
  ]

  const search = ref(null)

  const order = ref<OrderType>({
    field: 'title',
    direction: 'asc'
  });
  const orderByOptions = [
    {value: 'title', text: t('title')},
    {value: 'created_at', text: t('created_at')},
    {value: 'status', text: t('status')},
    {value: 'valid_until', text: t('valid_until')},
    {value: 'access_at', text: t('access_at')},
  ]

  const toggleOrderDirection = () => {
    order.value.direction = order.value.direction === 'asc' ? 'desc' : 'asc';
    handleFilter();
  }

  const handleFilter = () => {
    const filter = {
      accessType: accessType.value,
      accessStatus: accessStatus.value,
      search: search.value,
      order: order.value
    };

    emit('filter', filter);
  }
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
        <!--      search-->
        <div class="col-6">
          <BInputGroup size="sm">
            <BFormInput
                class="form-control-sm"
                id="search-input"
                :placeholder="$t('search')"
                type="text"
                v-model="search"
                @change="handleFilter"
                @input="handleFilter"
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
    <!--  order by -->
    <div class="col-12 d-flex justify-content-end align-items-center">
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
          style="max-width: 100px;"
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
