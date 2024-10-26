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
    const updateStatus = computed(() => processStore.getUpdateEbooks);
    const processing = computed(() => updateStatus.value.status === 'processing');
    const modalName = 'update-ebooks-modal';
    const {show} = useModal(modalName);

    const handleUpdate = () => {
        processStore.dispatchUpdateEbooks();
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        })
    };
</script>

<template>
    <div class="container">
        <div class="row mt-2">
            <ao-scheduled-actions
                :status="updateStatus.status"
                :completed="Number(updateStatus.completed)"
                :processing="Number(updateStatus.processing)"
                :pending="Number(updateStatus.pending)"
                :failed="Number(updateStatus.failed)"
                action="update"
                queue="update-ebooks"
                @action="show"
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
