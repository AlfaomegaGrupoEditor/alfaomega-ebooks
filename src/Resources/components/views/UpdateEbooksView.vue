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
    const updateStatus = computed(() => processStore.getUpdateEbooks);
    const processing = computed(() => updateStatus.value.status === 'processing');
    const modalName = 'update-ebooks-modal';
    const {show} = useModal(modalName);
    const intervalId = ref(null);
    const poolTimeout = 60 * 1000;

    const handleUpdate = () => {
        console.log('updating ebooks...');
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        })
    };

    onMounted(() => {
        intervalId.value = setInterval(() => {
            processStore.dispatchRetrieveQueueStatus('update-ebooks');
        }, poolTimeout);

        processStore.dispatchRetrieveQueueStatus('update-ebooks');
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
                    :caption="$t('update_ebooks')"
                    :dismissible="false"
                    type="notice"
                >
                    {{ $t('update_ebooks_notice') }}
                </ao-alert>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-3 d-flex justify-content-center">
                <ao-processing-queue v-bind="updateStatus" />
                <ao-processing-actions
                    :action="'update'"
                    :processing="processing"
                    @action="show"
                />
            </div>
        </div>
    </div>
    <ao-dialog
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleUpdate"
    >
        {{ $t('update_ebooks_confirmation') }}
    </ao-dialog>
</template>
