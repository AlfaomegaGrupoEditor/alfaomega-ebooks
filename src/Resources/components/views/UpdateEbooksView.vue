<script setup lang="ts">
    import {computed, onMounted, onUnmounted, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';
    import AoScheduledActions from '@/components/aoScheduledActions.vue';

    const {t} = useI18n();
    const processStore = useProcessStore();
    const updateStatus = computed(() => processStore.getUpdateEbooks);
    const processing = computed(() => updateStatus.value.status === 'processing');
    const modalName = 'update-ebooks-modal';
    const {show} = useModal(modalName);
    const intervalId = ref(null);
    const poolTimeout = 60 * 1000;
    const enablePolling = ref(false);

    const handleUpdate = () => {
        console.log('updating ebooks...');
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        })
    };

    onMounted(() => {
        if (enablePolling.value) {
            intervalId.value = setInterval(() => {
                processStore.dispatchRetrieveQueueStatus('update-ebooks');
            }, poolTimeout);

            processStore.dispatchRetrieveQueueStatus('update-ebooks');
        }

        processStore.dispatchRetrieveQueueStatus('update-ebooks');
    });

    onUnmounted(() => {
        clearInterval(intervalId.value);
    });
</script>

<template>
    <div class="container">
        <div class="row mt-2">
            <ao-scheduled-actions
                v-bind="updateStatus"
                action="update"
                @action="handleImport"
                queue="update-ebooks"
            />
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
