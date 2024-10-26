<script setup lang="ts">
    import {ref} from 'vue';import {useI18n} from 'vue-i18n';
    import {Size} from 'bootstrap-vue-next';

    const props = defineProps({
        name: {type: String, default: 'ao-modal'},
        title: {type: String, default: 'The title'},
        type: {type: String as () => 'confirm' | 'dlg', default: 'confirm'},
        size: {type: String as () => Size, default: 'md'},
    });
    const emit = defineEmits(['action']);

    const {t} = useI18n();
    const modal = ref(false);
</script>

<template>
    <BModal v-model="modal"
            centered
            no-fade
            teleport-to="#ao-container"
            :id="name"
            :title="title"
            :size="size"
            title-class="fs-6"
            header-class="py-2"
            footer-border-variant="secondary"
            :cancel-title="$t('cancel')"
            cancel-variant="light"
            :ok-only="type==='dlg'"
            :ok-title="$t('ok')"
            ok-variant="info"
            button-size="sm"
            @ok="emit('action')"
    >
        <slot></slot>
    </BModal>
</template>

<style scoped>

</style>
