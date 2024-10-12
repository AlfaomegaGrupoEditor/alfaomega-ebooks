<script setup lang="ts">
    const props = defineProps({
        title: { type: String, required: true, default: 'Ribbon'},
        variant: { type: String, required: false, default: 'primary' },
        position: { type: String, required: false, default: 'ribbon-top-left' },
        show: { type: Boolean, required: false, default: false },
        size: { type: Number, required: false, default: 100 } // Size in pixels
    });
</script>

<template>
    <div
        v-if="show"
        class="ribbon"
        :class="variant + ' ' + position"
        :style="{ '--ribbon-size': size + 'px' }"
    >
        <span>{{ title }}</span>
    </div>
</template>

<style scoped>
    @import url(https://fonts.googleapis.com/css?family=Lato:700);

    .ribbon {
        width: var(--ribbon-size);
        height: var(--ribbon-size);
        overflow: hidden;
        position: absolute;
    }

    .ribbon::before,
    .ribbon::after {
        position: absolute;
        z-index: -1;
        content: '';
        display: block;
        border: calc(var(--ribbon-size) / 30) solid #2980b9;
    }

    .ribbon span {
        position: absolute;
        display: block;
        width: calc(var(--ribbon-size) * 1.5);
        padding: calc(var(--ribbon-size) / 10) 0;
        background-color: #3498db;
        box-shadow: 0 5px 10px rgba(0, 0, 0, .1);
        color: #fff;
        font: 700 calc(var(--ribbon-size) / 8.33) / 1 'Lato', sans-serif;
        text-shadow: 0 1px 1px rgba(0, 0, 0, .2);
        text-transform: uppercase;
        text-align: center;
    }

    .ribbon.primary::before,
    .ribbon.primary::after {
        border: calc(var(--ribbon-size) / 30) solid #771004;
    }

    .ribbon.primary span {
        background-color: #D4230F;
    }

    /* top left */
    .ribbon-top-left {
        top: calc(var(--ribbon-size) / -15);
        left: calc(var(--ribbon-size) / -15);
    }

    .ribbon-top-left::before,
    .ribbon-top-left::after {
        border-top-color: transparent;
        border-left-color: transparent;
    }

    .ribbon-top-left::before {
        top: 0;
        right: 0;
    }

    .ribbon-top-left::after {
        bottom: 0;
        left: 0;
    }

    .ribbon-top-left span {
        right: calc(var(--ribbon-size) / -6);
        top: calc(var(--ribbon-size) / 5);
        transform: rotate(-45deg);
    }

    /* top right */
    .ribbon-top-right {
        top: calc(var(--ribbon-size) / -15);
        right: calc(var(--ribbon-size) / -15);
    }

    .ribbon-top-right::before,
    .ribbon-top-right::after {
        border-top-color: transparent;
        border-right-color: transparent;
    }

    .ribbon-top-right::before {
        top: 0;
        left: 0;
    }

    .ribbon-top-right::after {
        bottom: 0;
        right: 0;
    }

    .ribbon-top-right span {
        left: calc(var(--ribbon-size) / -6);
        top: calc(var(--ribbon-size) / 5);
        transform: rotate(45deg);
    }

    /* bottom left */
    .ribbon-bottom-left {
        bottom: calc(var(--ribbon-size) / -15);
        left: calc(var(--ribbon-size) / -15);
    }

    .ribbon-bottom-left::before,
    .ribbon-bottom-left::after {
        border-bottom-color: transparent;
        border-left-color: transparent;
    }

    .ribbon-bottom-left::before {
        bottom: 0;
        right: 0;
    }

    .ribbon-bottom-left::after {
        top: 0;
        left: 0;
    }

    .ribbon-bottom-left span {
        right: calc(var(--ribbon-size) / -6);
        bottom: calc(var(--ribbon-size) / 5);
        transform: rotate(225deg);
    }

    /* bottom right */
    .ribbon-bottom-right {
        bottom: calc(var(--ribbon-size) / -15);
        right: calc(var(--ribbon-size) / -15);
    }

    .ribbon-bottom-right::before,
    .ribbon-bottom-right::after {
        border-bottom-color: transparent;
        border-right-color: transparent;
    }

    .ribbon-bottom-right::before {
        bottom: 0;
        left: 0;
    }

    .ribbon-bottom-right::after {
        top: 0;
        right: 0;
    }

    .ribbon-bottom-right span {
        left: calc(var(--ribbon-size) / -6);
        bottom: calc(var(--ribbon-size) / 5);
        transform: rotate(-225deg);
    }
</style>
