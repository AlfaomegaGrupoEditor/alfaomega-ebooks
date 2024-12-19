<script setup lang="ts">
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {computed} from 'vue';
    import {ProcessType, ProcessNameType} from '@/types';

    const props = defineProps({
        action: {type: String as () => ProcessNameType , default: 'import'},
        direction: {type: String, default: 'column'},
        processing: {type: Boolean, default: false},
    });

    const emit = defineEmits(['action', 'refresh', 'clear']);
    const modalName = 'clear-queue-modal';
    const {show} = useModal(modalName);
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
        emit('clear');
    };
    const handleAction = () => {
        emit('action');
    };

    const handleRefresh = () => {
        emit('refresh');
    };
</script>

<template>
    <div class="col d-flex justify-content-end"
         :class="`flex-${direction}`"
    >
        <BButton class="my-1 mx-1"
                 variant="secondary"
                 size="sm"
                 style="max-width: 120px"
                 @click="handleRefresh"
        >
            {{ $t('refresh_queue') }}
        </BButton>
        <BButton class="my-1 mx-1"
                 variant="secondary"
                 size="sm"
                 style="max-width: 120px"
                 @click="show"
        >
            {{ $t('clear_queue') }}
        </BButton>
        <BButton class="my-1 mx-1"
                 variant="info"
                 size="sm"
                 style="max-width: 120px"
                 :disabled="processing"
                 @click="emit('action')"
        >
            <BSpinner small v-if="processing"/>
            {{ $t(action) }}
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
