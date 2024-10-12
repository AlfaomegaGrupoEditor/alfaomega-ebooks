<script setup lang="ts">
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';
    import {useProcessStore} from '@/stores';
    import {computed} from 'vue';
    import {ProcessType, ProcessNameType} from '@/types';

    const props = defineProps({
        action: {type: String as () => ProcessNameType , default: 'import'},
        processing: {type: Boolean, default: false},
    });

    const emit = defineEmits(['action']);
    const modalName = 'clear-queue-modal';
    const {show} = useModal(modalName);
    const processStore = useProcessStore();
    const process = computed((): ProcessType => {
        switch (props.action) {
            case 'import':
                return 'import-new-ebooks';
            case 'update':
                return 'update-ebooks';
            case 'link':
                return 'link-products';
            case 'setup':
                return 'setup-prices';
        }
    });

    const handleClearQueue = () => {
        eventBus.emit('notification', {
            message: 'clear_queue_success',
            type: 'success'
        });
    };
    const handleAction = () => {
        emit('action');
    };

    const handleRefresh = () => {
        processStore.dispatchRetrieveQueueStatus(process.value);
    };
</script>

<template>
    <div class="col-3 d-flex flex-column justify-content-end">
        <BButton class="my-1"
                 variant="info"
                 size="sm"
                 style="max-width: 120px"
                 :disabled="processing"
                 @click="handleAction"
        >
            <BSpinner small v-if="processing"/>
            {{ $t(action) }}
        </BButton>
        <BButton class="my-1"
                 variant="info"
                 size="sm"
                 style="max-width: 120px"
                 @click="handleRefresh"
        >
            {{ $t('refresh_queue') }}
        </BButton>
        <BButton class="my-1"
                 variant="info"
                 size="sm"
                 style="max-width: 120px"
                 @click="show"
        >
            {{ $t('clear_queue') }}
        </BButton>
    </div>
    <ao-dialog
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleClearQueue"
    >
        {{ $t('clear_queue_confirmation') }}
    </ao-dialog>
</template>

<style scoped>

</style>
