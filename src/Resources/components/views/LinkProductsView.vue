<script setup lang="ts">
import {computed, onMounted, onUnmounted, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';

    const {t} = useI18n();
    const processStore = useProcessStore();
    const linkStatus = computed(() => processStore.getLinkProducts);
    const processing = computed(() => linkStatus.value.status === 'processing');
    const modalName = 'link-products-modal';
    const {show} = useModal(modalName);
    const intervalId = ref(null);
    const poolTimeout = 60 * 1000;
    const enablePolling = ref(false);

    const handleLink = () => {
        console.log('Linking products...');
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        });
    };

    onMounted(() => {
        if (enablePolling.value) {
            intervalId.value = setInterval(() => {
                processStore.dispatchRetrieveQueueStatus('link-products');
            }, poolTimeout);
        }

        processStore.dispatchRetrieveQueueStatus('link-products');
    });

    onUnmounted(() => {
        clearInterval(intervalId.value);
    });

</script>

<template>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-md-offset-2">
                <ao-alert
                    :caption="$t('link_products')"
                    :dismissible="false"
                    type="notice"
                >
                    {{ $t('link_products_notice') }}
                </ao-alert>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-3 d-flex justify-content-center">
                <ao-processing-queue v-bind="linkStatus" />
                <ao-processing-actions
                    :action="'link'"
                    :processing="processing"
                    @action="show"
                />
            </div>
        </div>
    </div>
    <ao-dialog
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleLink"
    >
        {{ $t('link_products_confirmation') }}
    </ao-dialog>
</template>
