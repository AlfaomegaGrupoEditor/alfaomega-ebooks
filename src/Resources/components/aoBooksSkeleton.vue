<script setup lang="ts">
    import {computed, ref} from 'vue';

    const itemCount = ref(8);
    const items = computed(() => Array.from(
        {length: itemCount.value},
        (_, i) => i + 1
    ));
    const publicUrl = import.meta.env.VITE_PUBLIC_URL;
</script>

<template>
    <div class="row row-cols-1 row-cols-md-4 mt-4">
        <div v-for="item in items" :key="item"
            class="col mx-4 my-3"
            style="position: relative; max-width: 170px; padding: 0;"
        >
            <BPlaceholderCard
                class="px-1 py-1 border-2 ao-book-skeleton placeholder-glow opacity-animation"
                :imgSrc="`${publicUrl}/img/book_skeleton.png`"
                noFooter
                noHeader
                noButton
            />
        </div>
    </div>
</template>

<style scoped>
    @keyframes opacityAnimation {
        0% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 0.3;
        }
    }

    .bootstrap-app .opacity-animation {
        animation: opacityAnimation 1.5s infinite;
    }
</style>
