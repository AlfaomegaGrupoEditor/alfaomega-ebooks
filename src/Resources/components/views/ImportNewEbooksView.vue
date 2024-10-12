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
    const importStatus = computed(() => processStore.getImportNewEbooks);
    const processing = computed(() => importStatus.value.status === 'processing');
    const modalName = 'import-ebooks-modal';
    const {show} = useModal(modalName);
    const intervalId = ref(null);
    const poolTimeout = 60 * 1000;

    const handleImport = () => {
        console.log('Importing ebooks...');
        eventBus.emit('notification', {
            message: 'tasks_added',
            type: 'success'
        });
    };

    onMounted(() => {
        intervalId.value = setInterval(() => {
            processStore.dispatchRetrieveQueueStatus('import-ebook');
        }, poolTimeout);

        processStore.dispatchRetrieveQueueStatus('import-ebook');
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
