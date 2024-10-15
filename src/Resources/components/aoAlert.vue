<script setup lang="ts">
    import {computed, ref} from 'vue';

    const props = defineProps({
        caption: {type: String, default: 'caption'},
        type: { type: String as () => 'info' | 'warning' | 'danger' | 'notice', default: 'info' },
        action: { type: String, default: null },
        dismissible: { type: Boolean, default: true }
    });

    const emit = defineEmits<{ action: () => void }>();

    const variant = computed(() => {
        switch (props.type) {
            case 'info':
                return 'info';
            case 'warning':
                return 'warning';
            case 'danger':
                return 'danger';
            case 'notice':
                return 'light';
        }
    });
    const show = ref(true);

</script>

<template>
    <BAlert
        class="fs-7 my-2 my-md-3"
        :variant="variant"
        :model-value="show"
        :dismissible="dismissible"
    >
        <h6 class="alert-heading">{{ caption }}</h6>
        <p v-html="$slots.default ? $slots.default()[0].children : ''"></p>
        <div v-if="action" class="text-end">
            <BButton
                class="fs-7 fw-bold"
                variant="link"
                size="sm"
                @click="$emit('action')"
            >{{ action }}
            </BButton>
        </div>
    </BAlert>
</template>

<style scoped>
    .bootstrap-app .alert-heading {
        margin-top: 0;
        margin-bottom: 15px;
    }
</style>
