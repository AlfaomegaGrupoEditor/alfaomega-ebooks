<script setup lang="ts">
import {computed, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';

    const {t} = useI18n();
    const updateStatus = ref({
        status: 'idle', // idle,
        completed: 0,
        processing: 0,
        pending: 0,
        failed: 0
    });
    const processing = computed(() => updateStatus.value.status === 'processing');

    const handleUpdate = () => {
        console.log('updating ebooks...');
    };
</script>

<template>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-md-offset-2">
                <ao-alert
                    :caption="$t('update_ebooks')"
                    :dismissible="false"
                    type="notice"
                >
                    {{ $t('update_ebooks_notice') }}
                </ao-alert>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-3 d-flex justify-content-center">
                <ao-processing-queue v-bind="updateStatus" />
                <ao-processing-actions
                    :action="'update'"
                    :processing="processing"
                    @action="handleUpdate"
                />
            </div>
        </div>
    </div>
</template>
