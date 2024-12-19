<script setup lang="ts">
import {BookType} from '@/types';
    import {defineEmits, onMounted, ref} from 'vue';
    import {aoCornerRibbon} from '@/components';
    import {useI18n} from 'vue-i18n';

    const props = defineProps({
        data: {type: Object as () => BookType | null, default: null},
        disabled: {type: Boolean, default: false}
    });
    const emit = defineEmits<{ (e: 'open', payload: BookType): void }>();

    const {t} = useI18n();
    const hover = ref(false);
    const built = ref(false);
    const covers = ref(window.wpApiSettings.covers);

    const handleClick = () => {
        if (!props.disabled && props.data !== null) {
            emit('open', props.data);
        }
    };

    onMounted(() => {
        built.value = true;
    });

</script>

<template>
    <transition name="fade">
        <div v-if="built"
             style="position: relative; max-width: 170px; padding: 0;"
             class="d-inline-block mx-4 my-3"
             :class="disabled ? 'processing' : ''"
        >
            <BCard
                class="px-1 py-1 border-2"
                :class="hover ? 'shadow-lg border-primary' : ''"
                v-if="data !== null"
                :img-src="covers + data.cover"
                :img-alt="data.title"
                no-body
                :role="!disabled ? 'button' : ''"
                @click="handleClick"
                @mouseover="hover = true"
                @mouseleave="hover = false"
            />
            <ao-corner-ribbon
                v-if="data !== null"
                :title="$t(data.status)"
                :show="data.status === 'expired' || data.status === 'cancelled'"
            />
        </div>
    </transition>
</template>

<style scoped>
    .bootstrap-app .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s;
    }

    .bootstrap-app .fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */
    {
        opacity: 0;
    }

    .bootstrap-app .processing {
        cursor: progress;
        pointer-events: none;
        opacity: 0.6;
        filter: grayscale(100%);
    }
</style>
