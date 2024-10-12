<script setup lang="ts">
import {computed, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';

    const {t} = useI18n();
    const setupStatus = ref({
        status: 'idle',
        completed: 0,
        processing: 0,
        pending: 0,
        failed: 0
    });
    const processing = computed(() => setupStatus.value.status === 'processing');

    const handleSetup = () => {
        console.log('Setting ebooks prices...');
    };

</script>

<template>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-md-offset-2">
                <ao-alert
                    :caption="$t('setup_ebooks_price')"
                    :dismissible="false"
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
                    @action="handleSetup"
                />
            </div>
        </div>
    </div>
</template>
