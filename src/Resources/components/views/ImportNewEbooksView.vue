<script setup lang="ts">
import {computed, onMounted, onUnmounted, ref} from 'vue';
    import {useI18n} from 'vue-i18n';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';
    import AoScheduledActions from '@/components/aoScheduledActions.vue';

    const {t} = useI18n();
    const processStore = useProcessStore();
    const importStatus = computed(() => processStore.getImportNewEbooks);
    //const processing = computed(() => importStatus.value.status === 'processing');
    const modalName = 'import-ebooks-modal';
    const {show} = useModal(modalName);
    const intervalId = ref(null);
    const poolTimeout = 60 * 1000;
    const enablePolling = ref(false);

    const handleImport = (action) => {
        processStore.dispatchImportNewEbooks();
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        });
    };

    /*onMounted(() => {
        if (enablePolling.value) {
            intervalId.value = setInterval(() => {
                processStore.dispatchRetrieveQueueStatus('import-new-ebooks');
            }, poolTimeout);
        }

        processStore.dispatchRetrieveQueueStatus('import-new-ebooks');
    });

    onUnmounted(() => {
        clearInterval(intervalId.value);
    });*/

</script>

<template>
    <div class="container">
        <div class="row mt-2">
            <ao-scheduled-actions
                v-bind="importStatus"
                action="import"
                queue="import-new-ebooks"
                @action="show"
            />
        </div>
    </div>
    <ao-dialog
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleImport"
    >
        {{ $t('import_ebooks_confirmation') }}
    </ao-dialog>
</template>
