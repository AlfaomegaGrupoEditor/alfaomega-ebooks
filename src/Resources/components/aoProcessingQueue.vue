<script setup lang="ts">
    import {computed} from 'vue';import {useI18n} from 'vue-i18n';

    const props = defineProps({
        status: {type: String as () => 'idle' | 'processing' | 'failed' | 'completed' , default: 'idle'},
        completed: { type: Number, default: 0 },
        processing: { type: Number, default: 0 },
        pending: { type: Number, default: 0 },
        failed: { type: Number, default: 0 },
    });

    const {t} = useI18n();
    const variant = computed(() => {
        switch (props.status) {
            case 'idle':
                return 'warning';
            case 'processing':
                return 'info';
            case 'completed':
                return 'success';
            case 'failed':
                return 'primary';
        }
    });
</script>

<template>
    <BCard class="px-0 w-100 border-light"
           style="max-width: 100%; margin-right: 15px;"
    >
        <div class="card-title fw-bold fs-6 px-0 pt-0 pb-2 text-muted text-uppercase border-bottom">
            {{ $t('processing_queue_status') }}
            <i class="bi bi-alarm"></i>
        </div>

        <div class="mx-2 py-2">
            <div class="fs-6 fw-bold text-center mt-2">
                {{ $t('status') }}:
                <BBadge class="fs-7 py-2" :variant="variant">
                    {{ $t(status) }}
                </BBadge>
            </div>

            <div class="row mt-1 px-2 mx-4 py-4">
                <div class="pl-2 pb-2">{{ $t('completed') }}:
                    <BBadge class="fs-7" variant="success">{{ completed }}</BBadge>
                </div>
                <div class="pl-2 pb-2">{{ $t('processing') }}:
                    <BBadge class="fs-7">{{ processing }}</BBadge>
                </div>
                <div class="pl-2 pb-2">{{ $t('pending') }}:
                    <BBadge class="fs-7">{{ pending }}</BBadge>
                </div>
                <div class="pl-2 pb-2">{{ $t('has_failed') }}:
                    <BBadge class="fs-7" variant="primary">{{ failed }}</BBadge>
                </div>
            </div>
        </div>
    </BCard>
</template>

<style scoped>
    .border-light{
        border-color: var(--bs-light-border-subtle) !important;
    }
</style>
