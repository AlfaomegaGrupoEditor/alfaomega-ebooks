<script setup lang="ts">
    import {useI18n} from 'vue-i18n';

    const props = defineProps({
        slug: { type: String, default: 'slug' },
        title: { type: String, default: 'title'},
        description: { type: String, default: 'description' },
        count: { type: [null, Number], default: null },
        action: { type: String, default: 'action'}
    });

    const {t} = useI18n();
</script>

<template>
    <BListGroupItem
        class="d-flex justify-content-between align-items-start border-bottom"
        :href="action"
    >
        <div class="ms-2 me-auto">
            <div class="fw-bold">{{ $t(title)}}</div>
            {{ $t(description) }}
        </div>
        <BBadge v-if="count !== null"
            variant="info" pill>{{ count }}</BBadge>
    </BListGroupItem>
</template>

<style scoped>

</style>
