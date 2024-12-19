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
    const linkStatus = computed(() => processStore.getLinkProducts);
    const processing = computed(() => linkStatus.value.status === 'processing');
    const modalName = 'link-products-modal';
    const {show} = useModal(modalName);

    const handleLink = () => {
        processStore.dispatchLinkProducts();
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
                :status="linkStatus.status"
                :completed="Number(linkStatus.completed)"
                :processing="Number(linkStatus.processing)"
                :pending="Number(linkStatus.pending)"
                :failed="Number(linkStatus.failed)"
                action="link"
                queue="link-products"
                @action="show"
            />
        </div>
    </div>
    <ao-dialog
        :name="modalName"
        :title="$t('confirmation')"
        @action="handleLink"
    >
        {{ $t('link_products_confirmation') }}
    </ao-dialog>
</template>
