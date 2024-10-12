<script setup lang="ts">
    import {computed, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';
    import { useModal } from 'bootstrap-vue-next';

    const {t} = useI18n();
    const setupStatus = ref({
        status: 'idle',
        completed: 0,
        processing: 0,
        pending: 0,
        failed: 0
    });
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

    const handleSetup = () => {
        console.log('Setting ebooks prices...', factor.value, value.value);
    };
</script>

<template>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-md-offset-2">
                <ao-alert
                    :caption="$t('setup_ebooks_price')"
                    :dismissible="false"
                    type="notice"
                >
                    {{ $t('setup_ebooks_price_notice') }}
                </ao-alert>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-3 d-flex justify-content-center">
                <ao-processing-queue v-bind="setupStatus" />
                <ao-processing-actions
                    :action="'setup'"
                    :processing="processing"
                    @action="show"
                />
            </div>
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
            :ok-disabled="factor === '' || value === 0"
            button-size="sm"
            @ok="handleSetup"
    >
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
