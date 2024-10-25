<script setup lang="ts">
    import {ColorVariantType} from '@/types';
    import {ref, watch} from 'vue';
    import {ColorVariant} from 'bootstrap-vue-next';

    const props = defineProps({
        show: {type: Boolean, default: false},
        variant: {type: String as () => ColorVariant, default: 'success'},
        height: {type: Number, default: 5},
        speed: {type: Number, default: 100}
    });

    const interval = ref(0);
    const progress = ref(0);

    watch(() => props.show, (newVal) => {
        if (newVal) {
            progress.value = 0;
            interval.value = setInterval(() => {
                if (progress.value < 100) {
                    progress.value += 10;
                } else {
                    clearInterval(interval.value);
                    progress.value = 100;
                }
            }, props.speed);
        } else {
            clearInterval(interval.value);
            progress.value = 100;
        }
    });
</script>

<template>
    <div class="row row-cols-1 mx-0 mt-2"
         style="min-height: 25px"
    >
        <BProgress
            v-if="show && progress > 0"
            class="w-100 px-0 mt-2 pb-0"
            height="2px"
            style="background-color: transparent;"
        >
            <BProgressBar
                :value="progress"
                :variant="variant"
                style="box-shadow: none; border-radius: 0"
            />
        </BProgress>
    </div>
</template>

<style scoped>

</style>
