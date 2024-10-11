<script setup lang="ts">
    import {ref} from 'vue';
    import {aoAlert, aoProcessingQueue, aoProcessingActions} from '@/components';
    import {useI18n} from 'vue-i18n';

    const {t} = useI18n();
    const linkStatus = ref({
        status: 'idle',
        completed: 0,
        processing: 0,
        pending: 0,
        failed: 0
    });
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
                    @action="handleLink"
                />
            </div>
        </div>
    </div>
</template>
