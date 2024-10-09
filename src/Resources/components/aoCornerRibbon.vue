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

    .bootstrap-app .ribbon {
        width: var(--ribbon-size);
        height: var(--ribbon-size);
        overflow: hidden;
        position: absolute;
    }

    .bootstrap-app .ribbon::before,
    .bootstrap-app .ribbon::after {
        position: absolute;
        z-index: -1;
        content: '';
        display: block;
        border: calc(var(--ribbon-size) / 30) solid #2980b9;
    }

    .bootstrap-app .ribbon span {
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

    .bootstrap-app .ribbon.primary::before,
    .bootstrap-app .ribbon.primary::after {
        border: calc(var(--ribbon-size) / 30) solid #771004;
    }

    .bootstrap-app .ribbon.primary span {
        background-color: #D4230F;
    }

    /* top left */
    .bootstrap-app .ribbon-top-left {
        top: calc(var(--ribbon-size) / -15);
        left: calc(var(--ribbon-size) / -15);
    }

    .bootstrap-app .ribbon-top-left::before,
    .bootstrap-app .ribbon-top-left::after {
        border-top-color: transparent;
        border-left-color: transparent;
    }

    .bootstrap-app .ribbon-top-left::before {
        top: 0;
        right: 0;
    }

    .bootstrap-app .ribbon-top-left::after {
        bottom: 0;
        left: 0;
    }

    .bootstrap-app .ribbon-top-left span {
        right: calc(var(--ribbon-size) / -6);
        top: calc(var(--ribbon-size) / 5);
        transform: rotate(-45deg);
    }

    /* top right */
    .bootstrap-app .ribbon-top-right {
        top: calc(var(--ribbon-size) / -15);
        right: calc(var(--ribbon-size) / -15);
    }

    .bootstrap-app .ribbon-top-right::before,
    .bootstrap-app .ribbon-top-right::after {
        border-top-color: transparent;
        border-right-color: transparent;
    }

    .bootstrap-app .ribbon-top-right::before {
        top: 0;
        left: 0;
    }

    .bootstrap-app .ribbon-top-right::after {
        bottom: 0;
        right: 0;
    }

    .bootstrap-app .ribbon-top-right span {
        left: calc(var(--ribbon-size) / -6);
        top: calc(var(--ribbon-size) / 5);
        transform: rotate(45deg);
    }

    /* bottom left */
    .bootstrap-app .ribbon-bottom-left {
        bottom: calc(var(--ribbon-size) / -15);
        left: calc(var(--ribbon-size) / -15);
    }

    .bootstrap-app .ribbon-bottom-left::before,
    .bootstrap-app .ribbon-bottom-left::after {
        border-bottom-color: transparent;
        border-left-color: transparent;
    }

    .bootstrap-app .ribbon-bottom-left::before {
        bottom: 0;
        right: 0;
    }

    .bootstrap-app .ribbon-bottom-left::after {
        top: 0;
        left: 0;
    }

    .bootstrap-app .ribbon-bottom-left span {
        right: calc(var(--ribbon-size) / -6);
        bottom: calc(var(--ribbon-size) / 5);
        transform: rotate(225deg);
    }

    /* bottom right */
    .bootstrap-app .ribbon-bottom-right {
        bottom: calc(var(--ribbon-size) / -15);
        right: calc(var(--ribbon-size) / -15);
    }

    .bootstrap-app .ribbon-bottom-right::before,
    .bootstrap-app .ribbon-bottom-right::after {
        border-bottom-color: transparent;
        border-right-color: transparent;
    }

    .bootstrap-app .ribbon-bottom-right::before {
        bottom: 0;
        left: 0;
    }

    .bootstrap-app .ribbon-bottom-right::after {
        top: 0;
        right: 0;
    }

    .bootstrap-app .ribbon-bottom-right span {
        left: calc(var(--ribbon-size) / -6);
        bottom: calc(var(--ribbon-size) / 5);
        transform: rotate(-225deg);
    }
</style>
