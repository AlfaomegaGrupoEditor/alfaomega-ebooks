<script setup lang="ts">
    import {computed, ref, watch} from 'vue';
    import {useI18n} from 'vue-i18n';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';
    import AoScheduledActions from '@/components/aoScheduledActions.vue';
    import {SetupPriceFactorType} from '@/types';

    const {t} = useI18n();
    const processStore = useProcessStore();
    const setupStatus = computed(() => processStore.getSetupPrices);
    const processing = computed(() => setupStatus.value.status === 'processing');
    const modalName = 'setup-prices-modal';
    const {show, hide, modal} = useModal(modalName)
    const dialog = ref(false);
    const value = ref<number>(0);
    const factor = ref<SetupPriceFactorType>('undefined');
    const factorDescription = computed(() => {
        switch (factor.value) {
            case 'price_update':
                return t('update_description');
            case 'page_count':
                return t('page_count_description');
            case 'fixed_number':
                return t('fixed_number_description');
            case 'percent':
                return t('percent_description');
            default:
                return t('please_select_factor');
        }
    });
    const validateSetup = computed(() => factor.value !== 'undefined' && value.value != 0);
    const addOn = ref('*');

    const handleSetup = () => {
        processStore.dispatchSetupEbooksPrice(factor.value, value.value);
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        })
    };

    watch(factor, (newValue) => {
        switch (newValue) {
            case 'price_update':
                value.value = 1;
                addOn.value = '>'
                break;
            case 'page_count':
                value.value = 10;
                addOn.value = '#'
                break;
            case 'percent':
                value.value = 3;
                addOn.value = '%'
                break;
            case 'fixed_number':
                value.value = 50.00;
                addOn.value = '$'
                break;
            default:
                value.value = 0;
                addOn.value = '*'
        }
    });
</script>

<template>
    <div class="container">
        <div class="row mt-2">
            <ao-scheduled-actions
                :status="setupStatus.status"
                :completed="Number(setupStatus.completed)"
                :processing="Number(setupStatus.processing)"
                :pending="Number(setupStatus.pending)"
                :failed="Number(setupStatus.failed)"
                action="setup"
                queue="setup-prices"
                @action="show"
            />
        </div>
    </div>
    <BModal v-model="dialog"
            centered
            no-fade
            teleport-to="#ao-container"
            :id="modalName"
            :title="$t('setup_prices')"
            header-class="py-2"
            title-class="fs-6"
            footer-border-variant="secondary"
            :cancel-title="$t('cancel')"
            cancel-variant="light"
            :ok-title="$t('ok')"
            ok-variant="info"
            :ok-disabled="!validateSetup"
            button-size="sm"
            @ok="handleSetup"
    >
        <div class="row px-4 pb-3">
            {{ $t('confirm_update_process') }}
        </div>
        <div class="row">
            <div class="col mx-2">
                <label for="update-factor"
                       class="form-label fw-bold fs-8"
                >
                    {{ $t('factor') }}:
                </label>
                <select id="update-factor"
                        v-model="factor"
                        class="form-select fs-8"
                >
                    <option selected value="">{{ $t('select_factor')}}</option>
                    <option value="price_update">{{ $t('price_update') }}</option>
                    <option value="page_count">{{ $t('page_count') }}</option>
                    <option value="fixed_number">{{ $t('fixed_number') }}</option>
                    <option value="percent">{{ $t('percent') }}</option>
                </select>
            </div>
            <div class="col mx-2">
                <label for="update-value"
                       class="form-label fw-bold fs-8"
                >
                    {{ $t('value') }}:
                </label>
                <div class="input-group">
                    <span class="input-group-text fs-8 text-info">{{ addOn }}</span>
                    <input type="number"
                           v-model="value"
                           class="form-control fs-8"
                           :disabled="factor === 'price_update' || factor === 'undefined'"
                           id="update-value"
                    >
                </div>
            </div>
        </div>
        <div class="mx-3 mt-3">
            {{ $t(factorDescription) }}
        </div>
    </BModal>
</template>
