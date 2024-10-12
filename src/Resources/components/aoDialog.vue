<script setup lang="ts">
    import {ref} from 'vue';import {useI18n} from 'vue-i18n';

    const props = defineProps({
        name: {type: String, default: 'ao-modal'},
        title: {type: String, default: 'The title'}
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
            title-class="fs-6"
            header-border-variant="secondary"
            footer-border-variant="secondary"
            :cancel-title="$t('cancel')"
            cancel-variant="secondary"
            :ok-title="$t('ok')"
            ok-variant="info"
            button-size="sm"
            @ok="emit('action')"
    >
        <slot></slot>
<!--        <div v-html="$slots.default ? $slots.default()[0].children : ''">
        </div>-->
    </BModal>
</template>

<style scoped>

</style>
