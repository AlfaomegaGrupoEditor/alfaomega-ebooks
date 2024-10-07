<script setup lang="ts">
import {computed, ref} from 'vue';
import {BookType} from '@/types';

    const props = defineProps({
        caption: {type: String, default: 'caption'},
        type: { type: String as () => 'info' | 'warning' | 'danger', default: 'info' },
        action: { type: String, default: null }
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
        }
    });
    const show = ref(true);

</script>

<template>
    <BAlert
        class="fs-7 mx-md-5 mx-0"
        :variant="variant"
        :model-value="show"
        dismissible
    >
        <h6 class="alert-heading fs-7">{{ caption }}</h6>
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
    .alert-heading {
        margin-top: 0;
        margin-bottom: 15px;
    }
</style>
