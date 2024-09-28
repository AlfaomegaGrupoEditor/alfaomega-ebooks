<script setup lang="ts">
    import {ColorVariantType} from '@/types';
    import {ref, watch} from 'vue';

    const props = defineProps({
        show: {type: Boolean, default: false},
        variant: {type: String as () => ColorVariantType, default: 'success'},
        height: {type: Number, default: 5},
        speed: {type: Number, default: 100}
    });

    const interval = ref(null);
    const progress = ref(0);

    watch(() => props.show, (newVal) => {
        if (newVal) {
            progress.value = 0;
            interval.value = setInterval(() => {
                if (progress.value < 100) {
                    progress.value += 10;
                } else {
                    clearInterval(interval.value);
                    progress.value = 0;
                }
            }, props.speed);
        } else {
            clearInterval(interval.value);
            progress.value = 0;
        }
    });
</script>

<template>
    <div class="row row-cols-1 mx-0"
         style="min-height: 25px"
    >
        <BProgress
            v-if="show"
            class="mt-3 px-0" heigh="2px"
            style="height: 2px; background-color: transparent; box-shadow: none; border-radius: 0"
        >
            <BProgressBar
                height="2px"
                :value="progress"
                :variant="variant"
                style="border-radius: 0"
            />
        </BProgress>
    </div>
</template>

<style scoped>

</style>
