<script setup lang="ts">
    import {ButtonVariant, Size} from 'bootstrap-vue-next';

    const props = defineProps({
        caption: {type: String, default: 'caption'},
        icon: {type: String, default: 'fa-user'},
        disabled: {type: Boolean, default: false},
        color: {type: String as () => ButtonVariant, default: 'primary'},
        size: {type: String as () => Size, default: 'sm'},
        tooltip: {type: String, default: ''},
        loading: {type: Boolean, default: false}
    });

    const emit = defineEmits(['click']);

    const handleClick = () => {
        emit('click');
    };
</script>

<template>
    <!--  FIXME: v-b-tooltip.hover.top -->
    <BButton
        :variant="color"
        class="rounded-pill mx-1 px-2"
        :size="size"
        :disabled="disabled || loading"
        @click="handleClick"
        :title="tooltip"
        style="min-width: 110px;"
    >
        <span class="mx-2 d-inline-block">
            <BSpinner small v-if="loading"/>
            <i class="fs-7 fa ml-2" :class="icon" v-else></i>
            <span class="mx-2 fs-8 fw-bold d-inline-block">{{ $t(caption) }}</span>
        </span>
    </BButton>
</template>

<style scoped>

</style>
