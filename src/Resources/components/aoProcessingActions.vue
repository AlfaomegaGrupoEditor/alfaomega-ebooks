<script setup lang="ts">
    import AoDialog from '@/components/aoDialog.vue';
    import { useModal } from 'bootstrap-vue-next';
    import {eventBus} from '@/events';

    const props = defineProps({
        action: {type: String as () => 'import' | 'update' | 'link' | 'setup' , default: 'import'},
        processing: {type: Boolean, default: false},
    });

    const emit = defineEmits(['action']);
    const modalName = 'clear-cache-modal';
    const {show} = useModal(modalName);

    const handleClearCache = () => {
        console.log('Clear cache');
        eventBus.emit('notification', {
            message: 'clear_cache_success',
            type: 'success'
        });
    };
    const handleAction = () => {
        emit('action');
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
                 @click="show"
        >
            {{ $t('clear_cache') }}
        </BButton>
    </div>
    <ao-dialog
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleClearCache"
    >
        {{ $t('clear_cache_confirmation') }}
    </ao-dialog>
</template>

<style scoped>

</style>
