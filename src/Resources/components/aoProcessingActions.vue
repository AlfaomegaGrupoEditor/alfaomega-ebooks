<script setup lang="ts">

    const props = defineProps({
        action: {type: String as () => 'import' | 'update' | 'link' | 'setup' , default: 'import'},
    });

    const emit = defineEmits(['action']);

    const handleClearCache = () => {
        console.log('Clear cache');
    };
    const handleAction = () => {
        emit('action');
    };
</script>

<template>
    <div class="col-3 d-flex flex-column justify-content-end">
        <BButton class="my-1"
                 variant="info"
                 size="sm"
                 style="max-width: 120px"
                 @click="handleAction"
        >
            {{ $t(action) }}
        </BButton>
        <BButton class="my-1"
                 variant="info"
                 size="sm"
                 style="max-width: 120px"
                 @click="handleClearCache"
        >
            {{ $t('clear_cache') }}
        </BButton>
    </div>
</template>

<style scoped>

</style>
