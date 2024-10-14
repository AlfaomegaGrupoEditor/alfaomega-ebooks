<script setup lang="ts">
import {computed, onMounted, onUnmounted, ref} from 'vue';
    import {useI18n} from 'vue-i18n';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';
import AoScheduledActions from '@/components/aoScheduledActions.vue';

    const {t} = useI18n();
    const processStore = useProcessStore();
    const setupStatus = computed(() => processStore.getSetupPrices);
    const processing = computed(() => setupStatus.value.status === 'processing');
    const modalName = 'setup-prices-modal';
    const {show, hide, modal} = useModal(modalName)
    const dialog = ref(false);
    const value = ref(0);
    const factor = ref('');
    const factorDescription = computed(() => {
        switch (factor.value) {
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
    const validateSetup = computed(() => factor.value !== '' && value.value != 0);
    const intervalId = ref(null);
    const poolTimeout = 60 * 1000;
    const enablePolling = ref(false);

    const handleSetup = () => {
        console.log('Setting ebooks prices...', factor.value, value.value);
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        })
    };

    onMounted(() => {
        if (enablePolling.value) {
            intervalId.value = setInterval(() => {
                processStore.dispatchRetrieveQueueStatus('setup-prices');
            }, poolTimeout);
        }

        processStore.dispatchRetrieveQueueStatus('setup-prices');
    });

    onUnmounted(() => {
        clearInterval(intervalId.value);
    });
</script>

<template>
    <div class="container">
        <div class="row mt-2">
            <ao-scheduled-actions
                v-bind="setupStatus"
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
                <input type="number"
                       v-model="value"
                       class="form-control fs-8"
                       id="update-value"
                >
            </div>
        </div>
        <div class="mx-3 mt-3">
            {{ $t(factorDescription) }}
        </div>
    </BModal>
</template>
