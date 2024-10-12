<script setup lang="ts">
    import {computed, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';

    const {t} = useI18n();
    const importStatus = ref({
        status: 'idle', // idle, completed, processing, pending, failed
        completed: 0,
        processing: 0,
        pending: 0,
        failed: 0
    });
    const processing = computed(() => importStatus.value.status === 'processing');
    const modalName = 'import-ebooks-modal';
    const {show} = useModal(modalName);

    const handleImport = () => {
        console.log('Importing ebooks...');
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        });
    };

</script>

<template>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-md-offset-2">
                <ao-alert
                    :caption="$t('import_ebooks')"
                    :dismissible="false"
                    type="notice"
                >
                    {{ $t('import_ebooks_notice') }}
                </ao-alert>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-3 d-flex justify-content-center">
                <ao-processing-queue v-bind="importStatus" />
                <ao-processing-actions
                    :action="'import'"
                    :processing="processing"
                    @action="show"
                />
            </div>
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
