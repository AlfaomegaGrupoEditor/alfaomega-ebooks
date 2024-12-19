<script setup lang="ts">
    import {useI18n} from 'vue-i18n';
    import {computed, onMounted} from 'vue';
    import { useRoute } from 'vue-router';
    import {useEbookStore} from '@/stores';
    import AoToast from '@/components/aoToast.vue';
    import { useToast } from '@/composables/useToast';
    import {eventBus, useMittEvents} from '@/events';
    import {NotificationEvent} from '@/events/types';

    /**
     * Registers the event the App will be listening to.
     */
    useMittEvents(eventBus, {
        notification: (event: NotificationEvent) => notificationHandler(event),
    });

    const {t} = useI18n();
    const ebookStore = useEbookStore();
    const route = useRoute();
    const { toast, toastActive, showToast } = useToast();
    const pageTitle = computed(() => `Alfaomega eBooks [${t(route?.name || 'dashboard')}]`);

    /**
     * When a notification is sent
     */
    const notificationHandler = (data: NotificationEvent) => {
        if (data.type === 'success') {
            showToast({
                variant: 'success',
                title: t('success'),
                content: t(data.message)
            });
        } else if (data.type === 'error') {
            showToast({
                variant: 'primary',
                title: t('attention'),
                content: t(data.message)
            });
        }
    };

    onMounted(() => {
        ebookStore.dispatchRetrieveEbooksInfo();
        ebookStore.dispatchRetrieveProductsInfo();
        ebookStore.dispatchRetrieveAccessInfo();
        ebookStore.dispatchRetrieveCodesInfo();
    });
</script>

<template>
    <teleport to="body">
        <div id="ao-container" class="bootstrap-app"></div>
    </teleport>
    <div class="container-fluid">
        <div class="row">
            <main class="px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2 fs-6 fw-bold">{{ pageTitle }}</h1>
                    <BButton
                        v-if="route.name !== 'dashboard'"
                        variant="info"
                        size="sm"
                        style="min-width: 100px;"
                        :to="{name: 'dashboard'}"
                    >
                        {{ $t('back') }}
                    </BButton>
                </div>
            </main>
            <section>
                <router-view></router-view>
            </section>
        </div>
    </div>

    <!--  Toast message-->
    <ao-toast
        :active="toastActive"
        :variant="toast?.variant || 'success'"
        :title="toast?.title || ''"
        :content="toast?.content || 'test'"
    />
</template>

<style scoped>
</style>
