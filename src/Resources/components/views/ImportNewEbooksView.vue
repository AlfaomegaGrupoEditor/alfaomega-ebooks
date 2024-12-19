<script setup lang="ts">
import {computed} from 'vue';
    import {useI18n} from 'vue-i18n';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';
    import AoScheduledActions from '@/components/aoScheduledActions.vue';

    const {t} = useI18n();
    const processStore = useProcessStore();
    const importStatus = computed(() => processStore.getImportNewEbooks);
    const modalName = 'import-ebooks-modal';
    const {show} = useModal(modalName);

    const handleImport = () => {
        processStore.dispatchImportNewEbooks();
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        });
    };
</script>

<template>
    <div class="container">
        <div class="row mt-2">
            <ao-scheduled-actions
                :status="importStatus.status"
                :completed="Number(importStatus.completed)"
                :processing="Number(importStatus.processing)"
                :pending="Number(importStatus.pending)"
                :failed="Number(importStatus.failed)"
                :excluded="Number(importStatus.excluded)"
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
