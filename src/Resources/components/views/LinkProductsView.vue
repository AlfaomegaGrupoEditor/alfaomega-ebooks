<script setup lang="ts">
import {computed, ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';

    const {t} = useI18n();
    const linkStatus = ref({
        status: 'processing', // idle, completed, processing, pending, failed
        completed: 0,
        processing: 0,
        pending: 0,
        failed: 0
    });
    const processing = computed(() => linkStatus.value.status === 'processing');
    const handleLink = () => {
        console.log('Linking products...');
    };

</script>

<template>
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8 col-md-offset-2">
                <ao-alert
                    :caption="$t('link_ebooks')"
                    :dismissible="false"
                >
                    {{ $t('link_ebooks_notice') }}
                </ao-alert>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-3 d-flex justify-content-center">
                <ao-processing-queue v-bind="linkStatus" />
                <ao-processing-actions
                    :action="'link'"
                    :processing="processing"
                    @action="handleLink"
                />
            </div>
        </div>
    </div>
</template>
